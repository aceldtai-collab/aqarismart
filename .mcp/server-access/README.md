# server-access MCP scaffold

This folder contains a minimal local MCP server scaffold to execute SSH and SCP commands by shelling out to the system `ssh` and `scp` binaries. It is intended for local testing and must be secured before use.

Prerequisites
- Node 18+ (or compatible)
- System `ssh` and `scp` on PATH
- A deploy public key installed on the remote servers (recommended)

Install
```bash
npm install express body-parser
```

Run
```bash
node .mcp/server-access/server-access.js
```

Endpoints
- `POST /ssh` — JSON: `{ user, host, command }` — runs `ssh user@host 'command'`
- `POST /scp/upload` — JSON: `{ user, host, localPath, remotePath }` — runs `scp localPath user@host:remotePath`
- `POST /scp/download` — JSON: `{ user, host, remotePath, localPath }` — runs `scp user@host:remotePath localPath`

Security
- Do not run this scaffold on a public network without TLS and authentication.
- Do not commit private keys or credentials into the repository.

This scaffold is intentionally small — adapt and harden it before using in production.
