Use the `browser-pages` MCP server whenever the task needs browser navigation, DOM interaction, or browser state inspection.

Prefer `page_list` before assuming which tab is active or which pages are available.

Prefer `page_snapshot` immediately after `page_open`, `page_click`, `page_type`, `page_back`, and `page_forward` so the latest browser state is grounded in actual page content.

If multiple tracked pages exist, call `page_switch` explicitly before interacting with a specific tab.

Use `browser://pages/{pageId}` resources to read passive page state. Use tools for actions.

Use the `server-access` MCP server for tasks that require SSH/SCP or running commands on remote servers.

- Purpose: Run remote commands, upload/download files, inspect logs, and perform server-side maintenance via an authenticated SSH connection.
- How to use: start the local scaffold in `.mcp/server-access/` (see its README) and direct MCP calls to the server's endpoints.

Security: prefer installing a deploy public key on the remote host; never commit private keys. The agent will prompt before using credentials.
