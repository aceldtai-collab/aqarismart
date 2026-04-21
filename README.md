# Browser Pages MCP Server

Local MCP server for VS Code and Codex that uses Playwright to open, track, and interact with browser pages over stdio.

## Requirements

- Node.js 18+
- npm

## Install

```bash
npm install
npm run mcp:install-browser
```

## Run

```bash
npm run mcp:dev
```

## Workspace MCP config

VS Code can discover the server from `.vscode/mcp.json`:

```json
{
    "servers": {
        "browser-pages": {
            "type": "stdio",
            "command": "npx",
            "args": ["tsx", "${workspaceFolder}/src/server.ts"]
        }
    }
}
```

## Tools

- `page_open`
- `page_list`
- `page_switch`
- `page_close`
- `page_click`
- `page_type`
- `page_snapshot`
- `page_back`
- `page_forward`
- `page_eval`

## Resources

- `browser://pages/{pageId}`

## Deployment Docs

- Local workflow: [LOCAL_SETUP.md](LOCAL_SETUP.md)
- Production workflow: [PRODUCTION_SETUP.md](PRODUCTION_SETUP.md)
