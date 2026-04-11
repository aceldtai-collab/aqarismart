---
name: server-access
description: "MCP agent for authenticated server access (SSH/SCP/remote commands). Use when tasks require running commands on remote hosts, transferring build artifacts, or inspecting logs/files on servers."
use_when:
  - "Run remote shell commands over SSH (artisan, ls, tail logs, systemctl)"
  - "Upload/download files and build artifacts via scp"
  - "Inspect remote filesystem, logs, or permissions"
tools:
  - run_in_terminal
  - file_read
  - file_write
security: |
  - Prefer installing a public deploy key on the server and using key-based auth.
  - Never commit or store private keys in the repo.
  - The agent will always prompt before using credentials or keys.
startup: |
  - Install Node 18+ and ensure `ssh` and `scp` are available on the system PATH.
  - From repository root: `node .mcp/server-access/server-access.js`
  - The scaffold listens on `localhost:3741` by default.
examples: |
  - POST /ssh  { "user":"deploy","host":"1.2.3.4","command":"ls -la /home/deploy" }
---

See `.mcp/server-access/README.md` for a local scaffold and usage notes.

Use when: run remote commands, copy files, inspect logs, or perform server maintenance. Follow the security notes above.
