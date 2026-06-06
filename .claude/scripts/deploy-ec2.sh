#!/usr/bin/env bash
# PAC-RUN CREW — EC2 배포 스크립트
# 사용법: bash .claude/scripts/deploy-ec2.sh
# Windows: bash .claude/scripts/deploy-ec2.sh (Git Bash / WSL)

set -e

KEY="C:/src/projects/pub_key/crew-new-key.pem"
HOST="ubuntu@13.125.109.144"
SITE="/var/www/crew-site"

echo "▶ CREW EC2 배포 시작 — $(date '+%Y-%m-%d %H:%M:%S')"
echo "  Host : $HOST"
echo "  Path : $SITE"
echo ""

ssh -i "$KEY" -o StrictHostKeyChecking=no "$HOST" "
  set -e
  cd $SITE

  echo '[1/7] git pull origin main'
  git pull origin main

  echo '[2/7] composer install'
  composer install --no-dev --optimize-autoloader --quiet

  echo '[3/7] npm ci'
  npm ci --silent

  echo '[4/7] npm run build (Vite 프로덕션 빌드)'
  npm run build

  echo '[5/7] php artisan migrate'
  php artisan migrate --force

  echo '[6/7] php artisan optimize (캐시 초기화 + 재생성)'
  php artisan optimize:clear
  php artisan optimize

  echo '[7/7] php8.3-fpm restart + nginx reload'
  sudo systemctl restart php8.3-fpm
  sudo nginx -s reload

  echo ''
  echo '✓ 배포 완료 — ' \$(date '+%Y-%m-%d %H:%M:%S')
"

echo ""
echo "✓ 배포 완료"
