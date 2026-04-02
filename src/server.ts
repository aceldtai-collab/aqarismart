import process from "node:process";

import {
    McpServer,
    ResourceTemplate,
} from "@modelcontextprotocol/sdk/server/mcp.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import { chromium, type Browser, type Page } from "playwright";
import * as z from "zod/v4";

const SERVER_NAME = "browser-pages";
const SERVER_VERSION = "1.0.0";
const TEXT_PREVIEW_LIMIT = 1000;

const pageSummarySchema = z.object({
    pageId: z.string(),
    url: z.string(),
    title: z.string(),
    active: z.boolean(),
});

const pageStateSchema = pageSummarySchema.extend({
    textPreview: z.string(),
});

const baseResultShape = {
    success: z.boolean(),
    error: z.string().optional(),
};

const trackedPages = new Map<string, Page>();

let browser: Browser | null = null;
let browserLaunchPromise: Promise<Browser> | null = null;
let currentPageId: string | null = null;
let pageCounter = 0;
let isShuttingDown = false;

const server = new McpServer(
    {
        name: SERVER_NAME,
        version: SERVER_VERSION,
    },
    {
        capabilities: {
            logging: {},
        },
    },
);

function getNextPageId(): string {
    pageCounter += 1;
    return `page-${pageCounter}`;
}

function getPageResourceUri(pageId: string): string {
    return `browser://pages/${pageId}`;
}

function normalizeText(value: string): string {
    return value.replace(/\s+/g, " ").trim();
}

function truncateText(value: string, limit = TEXT_PREVIEW_LIMIT): string {
    if (value.length <= limit) {
        return value;
    }

    return `${value.slice(0, limit)}...`;
}

function getErrorMessage(error: unknown): string {
    if (error instanceof Error) {
        return error.message;
    }

    return String(error);
}

function safeSerialize(value: unknown): unknown {
    if (value === null || value === undefined) {
        return null;
    }

    if (
        typeof value === "string" ||
        typeof value === "number" ||
        typeof value === "boolean"
    ) {
        return value;
    }

    if (typeof value === "bigint") {
        return value.toString();
    }

    if (Array.isArray(value)) {
        return value.map((entry) => safeSerialize(entry));
    }

    if (typeof value === "object") {
        try {
            return JSON.parse(JSON.stringify(value));
        } catch {
            return String(value);
        }
    }

    return String(value);
}

function buildToolResult(data: Record<string, unknown>) {
    return {
        content: [
            {
                type: "text" as const,
                text: JSON.stringify(data, null, 2),
            },
        ],
        structuredContent: data,
    };
}

function buildToolError(message: string, extra: Record<string, unknown> = {}) {
    const body = {
        success: false,
        error: message,
        ...extra,
    };

    return {
        content: [
            {
                type: "text" as const,
                text: JSON.stringify(body, null, 2),
            },
        ],
        structuredContent: body,
        isError: true,
    };
}

function notifyResourceListChanged(): void {
    if (server.isConnected()) {
        server.sendResourceListChanged();
    }
}

function clearTrackedState(notify = true): void {
    trackedPages.clear();
    currentPageId = null;

    if (notify) {
        notifyResourceListChanged();
    }
}

function cleanupTrackedPage(pageId: string, notify = true): void {
    const hadPage = trackedPages.delete(pageId);

    if (!hadPage) {
        return;
    }

    if (currentPageId === pageId) {
        currentPageId = trackedPages.keys().next().value ?? null;
    }

    if (notify) {
        notifyResourceListChanged();
    }
}

function pruneClosedPages(): void {
    for (const [pageId, page] of trackedPages.entries()) {
        if (page.isClosed()) {
            cleanupTrackedPage(pageId, false);
        }
    }

    if (trackedPages.size === 0) {
        currentPageId = null;
    } else if (currentPageId && !trackedPages.has(currentPageId)) {
        currentPageId = trackedPages.keys().next().value ?? null;
    }
}

function getTrackedPage(pageId: string): Page | null {
    const page = trackedPages.get(pageId);

    if (!page) {
        return null;
    }

    if (page.isClosed()) {
        cleanupTrackedPage(pageId);
        return null;
    }

    return page;
}

function getActivePageEntry(): { pageId: string; page: Page } | null {
    pruneClosedPages();

    if (!currentPageId) {
        return null;
    }

    const page = getTrackedPage(currentPageId);
    if (page) {
        return { pageId: currentPageId, page };
    }

    currentPageId = trackedPages.keys().next().value ?? null;
    if (!currentPageId) {
        return null;
    }

    const fallbackPage = getTrackedPage(currentPageId);
    if (!fallbackPage) {
        return null;
    }

    return { pageId: currentPageId, page: fallbackPage };
}

async function ensureBrowser(): Promise<Browser> {
    if (browser) {
        return browser;
    }

    if (!browserLaunchPromise) {
        browserLaunchPromise = (async () => {
            try {
                const launchedBrowser = await chromium.launch({
                    headless: false,
                });

                browser = launchedBrowser;
                browserLaunchPromise = null;

                launchedBrowser.on("disconnected", () => {
                    browser = null;
                    browserLaunchPromise = null;
                    clearTrackedState();
                });

                return launchedBrowser;
            } catch (error) {
                browserLaunchPromise = null;
                throw error;
            }
        })();
    }

    return browserLaunchPromise;
}

async function getPageTitle(page: Page): Promise<string> {
    try {
        return await page.title();
    } catch {
        return "";
    }
}

async function getPageTextPreview(page: Page): Promise<string> {
    try {
        const text = await page.evaluate(() => document.body?.innerText ?? "");
        return truncateText(normalizeText(text));
    } catch {
        return "";
    }
}

async function buildPageSummary(pageId: string, page: Page) {
    return {
        pageId,
        url: page.url(),
        title: await getPageTitle(page),
        active: currentPageId === pageId,
    };
}

async function buildPageState(pageId: string, page: Page) {
    return {
        ...(await buildPageSummary(pageId, page)),
        textPreview: await getPageTextPreview(page),
    };
}

function attachTrackedPage(pageId: string, page: Page): void {
    page.on("close", () => {
        cleanupTrackedPage(pageId);
    });
}

server.registerResource(
    "browser-pages-state",
    new ResourceTemplate("browser://pages/{pageId}", {
        list: async () => {
            pruneClosedPages();

            return {
                resources: await Promise.all(
                    Array.from(trackedPages.entries()).map(
                        async ([pageId, page]) => ({
                            name: `browser-page-${pageId}`,
                            title: `Tracked Browser Page ${pageId}`,
                            uri: getPageResourceUri(pageId),
                            mimeType: "application/json",
                            description: await getPageTitle(page),
                        }),
                    ),
                ),
            };
        },
    }),
    {
        title: "Tracked Browser Pages",
        description: "JSON state for tracked Playwright browser pages.",
        mimeType: "application/json",
    },
    async (_uri, variables) => {
        const pageId = String(variables.pageId ?? "");
        const trackedPage = getTrackedPage(pageId);

        if (!trackedPage) {
            throw new Error(`Unknown pageId: ${pageId}`);
        }

        return {
            contents: [
                {
                    uri: getPageResourceUri(pageId),
                    mimeType: "application/json",
                    text: JSON.stringify(
                        await buildPageState(pageId, trackedPage),
                        null,
                        2,
                    ),
                },
            ],
        };
    },
);

async function shutdown(closeServer = false): Promise<void> {
    if (isShuttingDown) {
        return;
    }

    isShuttingDown = true;
    clearTrackedState(false);

    const activeBrowser =
        browser ?? (await browserLaunchPromise?.catch(() => null)) ?? null;
    browser = null;
    browserLaunchPromise = null;

    if (activeBrowser?.isConnected()) {
        await activeBrowser.close().catch(() => undefined);
    }

    if (closeServer) {
        await server.close().catch(() => undefined);
    }
}

server.server.onclose = () => {
    void shutdown();
};

server.registerTool(
    "page_open",
    {
        description:
            "Open a new browser page, navigate to a URL, track it, and make it active.",
        inputSchema: {
            url: z.string().url("Invalid URL."),
        },
        outputSchema: {
            ...baseResultShape,
            pageId: z.string().optional(),
            url: z.string().optional(),
        },
    },
    async ({ url }) => {
        try {
            const normalizedUrl = new URL(url).toString();
            const playwrightBrowser = await ensureBrowser();
            const page = await playwrightBrowser.newPage();

            try {
                await page.goto(normalizedUrl, {
                    waitUntil: "domcontentloaded",
                });
            } catch (error) {
                await page.close().catch(() => undefined);
                return buildToolError(
                    `Failed to open ${normalizedUrl}: ${getErrorMessage(error)}`,
                );
            }

            const pageId = getNextPageId();
            trackedPages.set(pageId, page);
            currentPageId = pageId;
            attachTrackedPage(pageId, page);
            notifyResourceListChanged();

            return buildToolResult({
                success: true,
                pageId,
                url: page.url(),
            });
        } catch (error) {
            return buildToolError(getErrorMessage(error));
        }
    },
);

server.registerTool(
    "page_list",
    {
        description:
            "List all tracked browser pages and identify the active page.",
        outputSchema: {
            ...baseResultShape,
            pages: z.array(pageSummarySchema).optional(),
        },
    },
    async () => {
        pruneClosedPages();

        const pages = await Promise.all(
            Array.from(trackedPages.entries()).map(([pageId, page]) =>
                buildPageSummary(pageId, page),
            ),
        );

        return buildToolResult({
            success: true,
            pages,
        });
    },
);

server.registerTool(
    "page_switch",
    {
        description: "Switch the active page to a different tracked page.",
        inputSchema: {
            pageId: z.string().min(1, "pageId is required."),
        },
        outputSchema: {
            ...baseResultShape,
            pageId: z.string().optional(),
            url: z.string().optional(),
            title: z.string().optional(),
        },
    },
    async ({ pageId }) => {
        const page = getTrackedPage(pageId);
        if (!page) {
            return buildToolError(`Unknown pageId: ${pageId}`);
        }

        currentPageId = pageId;

        return buildToolResult({
            success: true,
            pageId,
            url: page.url(),
            title: await getPageTitle(page),
        });
    },
);

server.registerTool(
    "page_close",
    {
        description:
            "Close a tracked page and remove it from the tracked page list.",
        inputSchema: {
            pageId: z.string().min(1, "pageId is required."),
        },
        outputSchema: {
            ...baseResultShape,
            pageId: z.string().optional(),
            activePageId: z.string().nullable().optional(),
        },
    },
    async ({ pageId }) => {
        const page = getTrackedPage(pageId);
        if (!page) {
            return buildToolError(`Unknown pageId: ${pageId}`);
        }

        try {
            await page.close();
            cleanupTrackedPage(pageId);

            return buildToolResult({
                success: true,
                pageId,
                activePageId: currentPageId,
            });
        } catch (error) {
            return buildToolError(
                `Failed to close ${pageId}: ${getErrorMessage(error)}`,
            );
        }
    },
);

server.registerTool(
    "page_click",
    {
        description: "Click an element on the active page.",
        inputSchema: {
            selector: z.string().min(1, "selector is required."),
        },
        outputSchema: {
            ...baseResultShape,
            pageId: z.string().optional(),
            url: z.string().optional(),
            selector: z.string().optional(),
        },
    },
    async ({ selector }) => {
        const activePage = getActivePageEntry();
        if (!activePage) {
            return buildToolError(
                "No active page. Open or switch to a tracked page first.",
            );
        }

        try {
            await activePage.page.click(selector);
            await activePage.page
                .waitForLoadState("domcontentloaded", { timeout: 2000 })
                .catch(() => undefined);

            return buildToolResult({
                success: true,
                pageId: activePage.pageId,
                url: activePage.page.url(),
                selector,
            });
        } catch (error) {
            return buildToolError(
                `Failed to click selector "${selector}": ${getErrorMessage(error)}`,
                {
                    pageId: activePage.pageId,
                },
            );
        }
    },
);

server.registerTool(
    "page_type",
    {
        description: "Fill text into an element on the active page.",
        inputSchema: {
            selector: z.string().min(1, "selector is required."),
            text: z.string(),
        },
        outputSchema: {
            ...baseResultShape,
            pageId: z.string().optional(),
            url: z.string().optional(),
            selector: z.string().optional(),
        },
    },
    async ({ selector, text }) => {
        const activePage = getActivePageEntry();
        if (!activePage) {
            return buildToolError(
                "No active page. Open or switch to a tracked page first.",
            );
        }

        try {
            await activePage.page.locator(selector).fill(text);

            return buildToolResult({
                success: true,
                pageId: activePage.pageId,
                url: activePage.page.url(),
                selector,
            });
        } catch (error) {
            return buildToolError(
                `Failed to type into selector "${selector}": ${getErrorMessage(error)}`,
                {
                    pageId: activePage.pageId,
                },
            );
        }
    },
);

server.registerTool(
    "page_snapshot",
    {
        description: "Capture a lightweight snapshot of the active page.",
        outputSchema: {
            ...baseResultShape,
            page: pageStateSchema.optional(),
        },
    },
    async () => {
        const activePage = getActivePageEntry();
        if (!activePage) {
            return buildToolError(
                "No active page. Open or switch to a tracked page first.",
            );
        }

        await activePage.page
            .waitForLoadState("domcontentloaded", { timeout: 2000 })
            .catch(() => undefined);

        return buildToolResult({
            success: true,
            page: await buildPageState(activePage.pageId, activePage.page),
        });
    },
);

server.registerTool(
    "page_back",
    {
        description: "Navigate back in the active page history.",
        outputSchema: {
            ...baseResultShape,
            pageId: z.string().optional(),
            url: z.string().optional(),
            title: z.string().optional(),
            navigated: z.boolean().optional(),
        },
    },
    async () => {
        const activePage = getActivePageEntry();
        if (!activePage) {
            return buildToolError(
                "No active page. Open or switch to a tracked page first.",
            );
        }

        try {
            const response = await activePage.page.goBack({
                waitUntil: "domcontentloaded",
            });

            return buildToolResult({
                success: true,
                pageId: activePage.pageId,
                url: activePage.page.url(),
                title: await getPageTitle(activePage.page),
                navigated: response !== null,
            });
        } catch (error) {
            return buildToolError(
                `Failed to navigate back: ${getErrorMessage(error)}`,
                {
                    pageId: activePage.pageId,
                },
            );
        }
    },
);

server.registerTool(
    "page_forward",
    {
        description: "Navigate forward in the active page history.",
        outputSchema: {
            ...baseResultShape,
            pageId: z.string().optional(),
            url: z.string().optional(),
            title: z.string().optional(),
            navigated: z.boolean().optional(),
        },
    },
    async () => {
        const activePage = getActivePageEntry();
        if (!activePage) {
            return buildToolError(
                "No active page. Open or switch to a tracked page first.",
            );
        }

        try {
            const response = await activePage.page.goForward({
                waitUntil: "domcontentloaded",
            });

            return buildToolResult({
                success: true,
                pageId: activePage.pageId,
                url: activePage.page.url(),
                title: await getPageTitle(activePage.page),
                navigated: response !== null,
            });
        } catch (error) {
            return buildToolError(
                `Failed to navigate forward: ${getErrorMessage(error)}`,
                {
                    pageId: activePage.pageId,
                },
            );
        }
    },
);

server.registerTool(
    "page_eval",
    {
        description:
            "Evaluate JavaScript on the active page and return the serialized result.",
        inputSchema: {
            script: z.string().min(1, "script is required."),
        },
        outputSchema: {
            ...baseResultShape,
            pageId: z.string().optional(),
            result: z.unknown().optional(),
        },
    },
    async ({ script }) => {
        const activePage = getActivePageEntry();
        if (!activePage) {
            return buildToolError(
                "No active page. Open or switch to a tracked page first.",
            );
        }

        try {
            const result = await activePage.page.evaluate(
                ({ expression }) => {
                    return (0, eval)(expression);
                },
                { expression: script },
            );

            return buildToolResult({
                success: true,
                pageId: activePage.pageId,
                result: safeSerialize(result),
            });
        } catch (error) {
            return buildToolError(
                `Failed to evaluate script: ${getErrorMessage(error)}`,
                {
                    pageId: activePage.pageId,
                },
            );
        }
    },
);

async function main(): Promise<void> {
    const transport = new StdioServerTransport();
    await server.connect(transport);
    console.error(`${SERVER_NAME} MCP server is running on stdio.`);
}

const handleSignal = (signal: NodeJS.Signals) => {
    void (async () => {
        console.error(`Received ${signal}, shutting down ${SERVER_NAME}.`);
        await shutdown(true);
        process.exit(0);
    })();
};

process.on("SIGINT", handleSignal);
process.on("SIGTERM", handleSignal);

main().catch(async (error) => {
    console.error(`Failed to start ${SERVER_NAME}:`, error);
    await shutdown(true);
    process.exit(1);
});
