#!/usr/bin/env node
const express = require('express');
const bodyParser = require('body-parser');
const { execFile } = require('child_process');
const fs = require('fs');

const app = express();
const PORT = process.env.PORT || 3741;
app.use(bodyParser.json());

function safeRespond(res, err, stdout, stderr) {
  if (err) {
    return res.status(500).json({ error: err.message, stdout, stderr });
  }
  return res.json({ stdout, stderr });
}

app.get('/', (req, res) => res.json({ ok: true, service: 'server-access', port: PORT }));

// Run a command over SSH (requires keys set up on the host)
app.post('/ssh', (req, res) => {
  const { user, host, command } = req.body || {};
  if (!user || !host || !command) return res.status(400).json({ error: 'user, host, and command required' });
  const target = `${user}@${host}`;
  execFile('ssh', [target, command], { timeout: 5 * 60 * 1000 }, (err, stdout, stderr) => safeRespond(res, err, stdout, stderr));
});

// Upload a local file to remote host via scp
app.post('/scp/upload', (req, res) => {
  const { user, host, localPath, remotePath } = req.body || {};
  if (!user || !host || !localPath || !remotePath) return res.status(400).json({ error: 'user, host, localPath, remotePath required' });
  if (!fs.existsSync(localPath)) return res.status(400).json({ error: 'localPath not found' });
  const target = `${user}@${host}:${remotePath}`;
  execFile('scp', [localPath, target], { timeout: 10 * 60 * 1000 }, (err, stdout, stderr) => safeRespond(res, err, stdout, stderr));
});

// Download a remote file to local path via scp
app.post('/scp/download', (req, res) => {
  const { user, host, remotePath, localPath } = req.body || {};
  if (!user || !host || !remotePath || !localPath) return res.status(400).json({ error: 'user, host, remotePath, localPath required' });
  const target = `${user}@${host}:${remotePath}`;
  execFile('scp', [target, localPath], { timeout: 10 * 60 * 1000 }, (err, stdout, stderr) => safeRespond(res, err, stdout, stderr));
});

app.listen(PORT, () => console.log(`server-access listening on http://localhost:${PORT}`));
