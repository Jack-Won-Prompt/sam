#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# 운영 배포 스크립트  —  git pull 이후 반드시 실행
#
#   git pull 은 "코드"만 갱신합니다. 아래 항목은 git 에 포함되지 않는
#   런타임 상태라서, pull 후 이 스크립트를 돌려야 메인화면에 상품이 보입니다.
#     - public/build (프론트 빌드)   : .gitignore 대상 → npm run build 필요
#     - DB 상품/카테고리 레코드       : 시더로 생성 → db:seed 필요
#     - public/storage 심볼릭 링크    : .gitignore 대상 → storage:link 필요
#     - 뷰/설정/라우트 캐시            : optimize:clear 로 초기화
#
# 사용법:  git pull && bash deploy.sh
# (Windows/XAMPP 는 Git Bash 에서 실행. php 가 PATH 에 없으면 아래 PHP 변수 지정)
# ---------------------------------------------------------------------------
set -e

PHP="${PHP:-php}"          # 예) Windows XAMPP:  PHP=/e/xampp/php/php.exe bash deploy.sh
COMPOSER="${COMPOSER:-composer}"

echo "▶ 1/5  PHP 의존성 설치"
$COMPOSER install --no-dev --optimize-autoloader

echo "▶ 2/5  프론트엔드 빌드 (public/build 재생성)"
npm ci
npm run build

echo "▶ 3/5  DB 마이그레이션 & 연근 라인업 시딩"
$PHP artisan migrate --force
$PHP artisan db:seed --class=LineupSeeder --force

echo "▶ 4/5  스토리지 심볼릭 링크"
$PHP artisan storage:link || true

echo "▶ 5/5  캐시 초기화 후 재생성"
$PHP artisan optimize:clear
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache

echo "✅ 배포 완료 — 메인화면 상품/인증/QR 반영됨"
