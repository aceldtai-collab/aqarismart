Aqari Smart — Production Deployment 
Cheatsheet
A practical, opinionated guide for deploying a Laravel SaaS on Ubuntu (Nginx + PHP-FPM 
8.3 + MariaDB + Redis). All commands are in English. Edit and extend as needed.
How to Use This Document
Use this cheatsheet as your single source of truth for deployment. Update any environmentspecific values (domain, IP, repo, passwords). You can insert a Word Table of Contents 
(References → Table of Contents) and update it as you edit.
Architecture Overview
• OS: Ubuntu 24.04 LTS (VPS on Contabo).
• Web: Nginx (reverse proxy) → PHP-FPM 8.3 (Unix socket).
• App: Laravel 12.x with releases + symlink strategy 
(`/var/www/app/releases/<timestamp>` → `/var/www/app/current`).
• State: Shared paths in `/var/www/app/shared` (storage, bootstrap/cache, .env).
• DB: MariaDB 10.11 (local).
• Cache/Queues/Sessions: Redis (local).
• Assets: Vite build via Node 20 LTS.
• SSL: Let’s Encrypt (certbot).
• Workers: Supervisor for queue:work and/or Horizon.
• Backups: Nightly DB dumps + logrotate for app logs.
Directory Structure (Server)
\
/var/www/app/
├─ current -> /var/www/app/releases/<timestamp> # symlink to active 
release
├─ releases/
│ ├─ 2025YYYYmmddHHMMSS/ # each deploy makes a new 
release
│ └─ ...
└─ shared/
 ├─ .env
 ├─ storage/
 │ ├─ app/
 │ ├─ framework/
 │ └─ logs/
 └─ bootstrap/
 └─ cache/
Prerequisites (Once per Server)
# Base packages
sudo apt update && sudo apt -y install software-properties-common curl gnupg 
ca-certificates lsb-release unzip git
# PHP 8.3 + extensions
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt -y install php8.3 php8.3-fpm php8.3-cli php8.3-mbstring php8.3-xml 
php8.3-curl php8.3-zip \
 php8.3-gd php8.3-intl php8.3-bcmath php8.3-mysql php8.3-redis
# PHP-FPM tuning (edit as needed)
sudo sed -i 's/^pm = .*/pm = dynamic/; s/^pm.max_children = .*/pm.max_children 
= 24/; s/^pm.start_servers = .*/pm.start_servers = 6/; s/^pm.min_spare_servers 
= .*/pm.min_spare_servers = 6/; s/^pm.max_spare_servers = 
.*/pm.max_spare_servers = 12/; s/^;*pm.max_requests = .*/pm.max_requests = 
500/' /etc/php/8.3/fpm/pool.d/www.conf
sudo sed -i 's/^memory_limit = .*/memory_limit = 512M/; s/^post_max_size = 
.*/post_max_size = 64M/; s/^upload_max_filesize = .*/upload_max_filesize = 
64M/; s/^max_execution_time = .*/max_execution_time = 120/' 
/etc/php/8.3/fpm/php.ini
sudo bash -c 'cat >>/etc/php/8.3/fpm/php.ini <<EOF
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
EOF'
sudo systemctl restart php8.3-fpm
# Nginx
sudo apt -y install nginx
sudo systemctl enable --now nginx
# MariaDB + basic hardening
sudo apt -y install mariadb-server
sudo systemctl enable --now mariadb
sudo mysql_secure_installation
# Redis
sudo apt -y install redis-server
sudo sed -i 's/^supervised .*/supervised systemd/' /etc/redis/redis.conf
sudo systemctl enable --now redis-server
# Node 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt -y install nodejs
# Composer (if not present)
php -r "copy('https://getcomposer.org/installer','composer-setup.php');"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer --version
Nginx Site (HTTP baseline)
sudo tee /etc/nginx/sites-available/laravel >/dev/null <<'CONF'
server {
 listen 80;
 server_name _;
 root /var/www/app/current/public;
 index index.php;
 charset utf-8;
 location / { try_files $uri $uri/ /index.php?$query_string; }
 location ~ \.php$ {
 include snippets/fastcgi-php.conf;
 fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
 }
 location ~* \.(js|css|png|jpg|jpeg|gif|webp|svg|ico|ttf|otf|woff|woff2)$ {
 expires 7d;
 access_log off;
 add_header Cache-Control "public, must-revalidate, proxy-revalidate";
 }
 client_max_body_size 64M;
}
CONF
sudo ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sitesenabled/laravel
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
Application Paths (Once)
sudo mkdir -p /var/www/app/{releases,shared/storage,shared/bootstrap/cache}
sudo chown -R rentojo:www-data /var/www/app
sudo chmod -R g+ws /var/www/app
GitHub Deploy Key (Server → Repo Deploy Key: Read-only)
ssh-keygen -t ed25519 -f ~/.ssh/github_rentojo -C "deploy@rentojo" -N ""
ssh-keyscan -t ed25519 github.com >> ~/.ssh/known_hosts
chmod 600 ~/.ssh/github_rentojo ~/.ssh/known_hosts
cat ~/.ssh/github_rentojo.pub # add this as Deploy Key on the repo (read 
access)
Shared .env (Server)
# Place production .env once:
# scp C:\path\to\.env rentojo@SERVER_IP:/var/www/app/shared/.env
# Typical edits:
sed -i \
 -e 's/^APP_ENV=.*/APP_ENV=production/' \
 -e 's/^APP_DEBUG=.*/APP_DEBUG=false/' \
 -e 's|^APP_URL=.*|APP_URL=https://your-domain.com|' \
 -e 's|^APP_TIMEZONE=.*|APP_TIMEZONE=Asia/Amman|' \
 -e 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' \
 -e 's/^DB_HOST=.*/DB_HOST=127.0.0.1/' \
 -e 's/^DB_PORT=.*/DB_PORT=3306/' \
 -e 's/^DB_DATABASE=.*/DB_DATABASE=rentojo/' \
 -e 's/^DB_USERNAME=.*/DB_USERNAME=rentojo/' \
 -e "s/^DB_PASSWORD=.*/DB_PASSWORD=changeMeStrong!/" \
 /var/www/app/shared/.env
MariaDB App User (Server)
sudo mysql <<'SQL'
CREATE DATABASE IF NOT EXISTS rentojo CHARACTER SET utf8mb4 COLLATE 
utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'rentojo'@'localhost' IDENTIFIED BY 
'changeMeStrong!';
GRANT ALL PRIVILEGES ON rentojo.* TO 'rentojo'@'localhost';
FLUSH PRIVILEGES;
SQL
# test
mysql -h 127.0.0.1 -u rentojo -p'changeMeStrong!' -e "SHOW DATABASES LIKE 
'rentojo';" 
Deploy Script (Server) — /usr/local/bin/rentojo-deploy
Runs clone → install → migrate → build → cache → switch → cleanup. Edit repo/branch as 
needed.
sudo tee /usr/local/bin/rentojo-deploy >/dev/null <<'BASH'
#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/app"
REL_DIR="$APP_DIR/releases"
SHARED_DIR="$APP_DIR/shared"
REPO_SSH="${REPO_SSH:-git@github.com:ahmadhassan989/rentojo.git}"
BRANCH="${BRANCH:-main}"
KEY="${KEY:-/home/rentojo/.ssh/github_rentojo}"
KEEP="${KEEP:-5}"
REL="$(date +%Y%m%d%H%M%S)"
DEST="$REL_DIR/$REL"
# Ensure shared dirs
mkdir -p "$SHARED_DIR/storage/app" "$SHARED_DIR/storage/framework" 
"$SHARED_DIR/storage/logs" "$SHARED_DIR/bootstrap/cache"
chown -R rentojo:www-data "$SHARED_DIR"
find "$SHARED_DIR" -type d -exec chmod 775 {} \;
find "$SHARED_DIR" -type f -exec chmod 664 {} \;
umask 002
# Clone
mkdir -p "$DEST"
GIT_SSH_COMMAND="ssh -i $KEY -o IdentitiesOnly=yes" \
 git clone --depth 1 --branch "$BRANCH" "$REPO_SSH" "$DEST"
cd "$DEST"
# Link shared paths
rm -rf storage bootstrap/cache
ln -s "$SHARED_DIR/storage" storage
ln -s "$SHARED_DIR/bootstrap/cache" bootstrap/cache
[ -f "$SHARED_DIR/.env" ] || { echo "Missing $SHARED_DIR/.env"; exit 1; }
ln -s "$SHARED_DIR/.env" .env
# App steps as app user
sudo -u rentojo -H bash -lc 'composer install --no-dev --prefer-dist --
optimize-autoloader'
sudo -u rentojo -H bash -lc 'php artisan key:generate || true'
sudo -u rentojo -H bash -lc 'php artisan migrate --force || true'
if command -v npm >/dev/null 2>&1; then
 sudo -u rentojo -H bash -lc '(npm ci || npm i) && npm run build || true'
fi
# Cache warmup
sudo -u rentojo -H bash -lc 'php artisan config:cache || true'
sudo -u rentojo -H bash -lc 'php artisan route:cache || true'
sudo -u rentojo -H bash -lc 'php artisan view:cache || true'
# Permissions
chown -R rentojo:www-data "$APP_DIR"
# Atomic switch
ln -sfn "$DEST" "$APP_DIR/current"
# Reload Nginx
systemctl reload nginx || true
# Cleanup old releases
cd "$REL_DIR"
( ls -t | head -n "$KEEP"; ls ) | sort | uniq -u | xargs -r rm -rf
echo "Deployed release $REL"
BASH
sudo chmod +x /usr/local/bin/rentojo-deploy
First Deploy & Routine Deploy
# first deploy
sudo /usr/local/bin/rentojo-deploy
# routine deploy after pushing to main
sudo /usr/local/bin/rentojo-deploy
# verify
ls -l /var/www/app/current
php /var/www/app/current/artisan about
curl -I http://127.0.0.1
Rollback to Previous Release
PREV=$(ls -t /var/www/app/releases | sed -n '2p')
ln -sfn /var/www/app/releases/$PREV /var/www/app/current
sudo systemctl reload nginx
Domain + SSL (Let’s Encrypt)
# DNS: A records for @ and www → your server IP
# Nginx: set your domain
sudo sed -i 's/server_name _;/server_name your-domain.com www.yourdomain.com;/' /etc/nginx/sites-available/laravel
sudo nginx -t && sudo systemctl reload nginx
# Certbot
sudo snap install core && sudo snap refresh core
sudo snap install --classic certbot
sudo ln -s /snap/bin/certbot /usr/bin/certbot
sudo certbot --nginx -d your-domain.com -d www.your-domain.com --agree-tos -m 
you@example.com --redirect
# Update APP_URL
sed -i 's|^APP_URL=.*|APP_URL=https://your-domain.com|' 
/var/www/app/shared/.env
cd /var/www/app/current && php artisan config:clear && php artisan 
config:cache
Supervisor (Queues / Horizon)
sudo apt -y install supervisor
sudo tee /etc/supervisor/conf.d/laravel-queue.conf >/dev/null <<'CONF'
[program:laravel-queue]
command=/usr/bin/php /var/www/app/current/artisan queue:work --sleep=3 --
tries=3 --max-time=3600
user=rentojo
autostart=true
autorestart=true
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/laravel-queue.log
stopasgroup=true
killasgroup=true
environment=APP_ENV="production"
CONF
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl 
status
Backups + Logrotate
# Nightly DB backup (14-day retention)
sudo tee /usr/local/bin/db-backup >/dev/null <<'BASH'
#!/usr/bin/env bash
set -euo pipefail
OUT=/var/backups/db
mkdir -p "$OUT"
TS=$(date +%F-%H%M%S)
mysqldump -h 127.0.0.1 -u rentojo -p'changeMeStrong!' rentojo | gzip > 
"$OUT/rentojo-$TS.sql.gz"
find "$OUT" -type f -mtime +14 -delete
BASH
sudo chmod +x /usr/local/bin/db-backup
echo "0 3 * * * root /usr/local/bin/db-backup" | sudo tee /etc/cron.d/dbbackup
# Logrotate for Laravel logs
sudo tee /etc/logrotate.d/laravel >/dev/null <<'CONF'
/var/www/app/shared/storage/logs/*.log {
 daily
 rotate 14
 compress
 missingok
 notifempty
 copytruncate
}
CONF
Firewall & Security
# UFW
sudo ufw allow OpenSSH
sudo ufw allow 80,443/tcp
sudo ufw --force enable
sudo ufw status
# SSH hardening (keys only)
sudo sed -i 's/^#\?PasswordAuthentication .*/PasswordAuthentication no/' 
/etc/ssh/sshd_config
sudo systemctl reload ssh
# Unattended security updates
sudo apt -y install unattended-upgrades && sudo dpkg-reconfigure -plow 
unattended-upgrades
Optional: GitHub Actions (CI) + Manual SSH Deploy
\
# .github/workflows/deploy.yml (example that builds & notifies; server still 
runs the deploy script manually)
name: ci
on:
 push:
 branches: [ "main" ]
jobs:
 build:
 runs-on: ubuntu-latest
 steps:
 - uses: actions/checkout@v4
 - uses: shivammathur/setup-php@v2
 with:
 php-version: '8.3'
 - name: Install Composer deps
 run: composer install --no-dev --prefer-dist --no-interaction --noprogress
 - name: Build assets
 run: |
 npm ci || npm i
 npm run build
 - name: Artifact (optional)
 run: zip -r artifact.zip .
Troubleshooting (Common Errors)
• 404 from Nginx: wrong root or current not linked → ensure /var/www/app/current 
points to the latest release and contains /public.
• 502 Bad Gateway: php-fpm down or wrong socket → check `systemctl status php8.3-
fpm` and `fastcgi_pass` path.
• 500 Internal Server Error: see `/var/www/app/shared/storage/logs/laravel.log`.
• MySQL “Access denied for user 'root'@'localhost'”: never use root in .env; use app user; 
clear caches; restart php-fpm.
• Permission denied for storage/bootstrap/cache: ensure shared dirs and perms; symlink 
correctly.
• Composer Telescope crash on prod: keep Telescope in require-dev only and register 
only in local env.
• Config caching stale: remove `bootstrap/cache/config.php`, restart php-fpm, rebuild 
caches.
• Tenant not found: seed a tenant row matching host or first-label slug; or add a fallback 
in resolver during testing.
Command Reference (Quick)
# Deploy now
sudo /usr/local/bin/rentojo-deploy
# Rollback
PREV=$(ls -t /var/www/app/releases | sed -n '2p'); ln -sfn 
/var/www/app/releases/$PREV /var/www/app/current; sudo systemctl reload nginx
# Logs
tail -n 200 /var/www/app/shared/storage/logs/laravel.log
sudo tail -n 200 /var/log/nginx/error.log
sudo journalctl -u php8.3-fpm -n 200 --no-pager
# Services
systemctl status nginx --no-pager
systemctl status php8.3-fpm --no-pager
systemctl status redis-server --no-pager
systemctl status supervisor --no-pager
# Laravel maintenance mode
php /var/www/app/current/artisan down --render="errors::503"
php /var/www/app/current/artisan up