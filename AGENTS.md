Use the `browser-pages` MCP server whenever the task needs browser navigation, DOM interaction, or browser state inspection.

Prefer `page_list` before assuming which tab is active or which pages are available.

Prefer `page_snapshot` immediately after `page_open`, `page_click`, `page_type`, `page_back`, and `page_forward` so the latest browser state is grounded in actual page content.

If multiple tracked pages exist, call `page_switch` explicitly before interacting with a specific tab.

Use `browser://pages/{pageId}` resources to read passive page state. Use tools for actions.
