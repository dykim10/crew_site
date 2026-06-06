#!/usr/bin/env bash
# PAC-RUN CREW — EC2 배포 스크립트
# 사용법: bash .claude/scripts/deploy-ec2.sh
# 흐름: 로컬 git push → EC2 SSH → git pull → build → migrate → cache → restart

set -e

KEY="C:/src/projects/pub_key/crew-new-key.pem"
HOST="ubuntu@13.125.109.144"
SITE="/var/www/crew-site"
TOTAL=8

echo "▶ CREW EC2 배포 시작 — $(date '+%Y-%m-%d %H:%M:%S')"
echo "  Host : $HOST"
echo "  Path : $SITE"
echo ""

# ── [1/8] 로컬 → GitHub ──
echo "[1/$TOTAL] git push origin main"
git push origin main
echo ""

# ── [2/8 ~ 8/8] EC2 원격 실행 ──
ssh -i "$KEY" -o StrictHostKeyChecking=no "$HOST" "
  set -e
  cd $SITE

  echo '[2/$TOTAL] git pull origin main'
  git pull origin main

  echo '[3/$TOTAL] composer install'
  composer install --no-dev --optimize-autoloader --quiet

  echo '[4/$TOTAL] npm ci'
  npm ci --silent

  echo '[5/$TOTAL] npm run build  (Vite 프로덕션 빌드)'
  npm run build

  echo '[6/$TOTAL] php artisan migrate'
  php artisan migrate --force

  echo '[7/$TOTAL] 캐시 초기화 → 재생성'
  php artisan optimize:clear
  php artisan optimize

  echo '[8/$TOTAL] php8.3-fpm restart + nginx reload'
  sudo systemctl restart php8.3-fpm
  sudo nginx -s reload

  echo ''
  echo '✓ EC2 배포 완료 —' \$(date '+%Y-%m-%d %H:%M:%S')
"

echo ""
echo "✓ 전체 배포 완료 — $(date '+%Y-%m-%d %H:%M:%S')"
