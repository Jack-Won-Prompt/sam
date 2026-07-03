# 강원 산양삼 쇼핑몰 (sam)

강원도 산양삼(자연 재배 인삼) 전문 쇼핑몰. `gnmart.co.kr` UI/UX 참조.

- **Framework**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade + Tailwind CSS + Alpine.js
- **DB**: MySQL / MariaDB
- **결제**: 토스페이먼츠
- **메인 컬러**: `#3182f6`

## 주요 기능

### 고객 (프론트)
- 메인: 배너 슬라이더, 카테고리, 베스트/신상품 진열
- 카테고리 · 상품목록(정렬) · 상품상세(옵션 선택/수량/합계) · 검색
- 장바구니(회원/비회원, 로그인 시 병합) · 주문/체크아웃 · 주문완료 · 마이페이지(주문내역)
- 회원가입/로그인 (Laravel Breeze)

### 관리자 (`/admin`)
- 대시보드(매출·주문 통계)
- 상품 관리(옵션·이미지), 카테고리, 주문 관리(상태 변경), 회원, 배너

### 결제
- 토스페이먼츠 카드 결제 연동 (금액 위변조 검증 + 서버 승인 confirm)

## 설치

```bash
# 1. 의존성
composer install
npm install && npm run build

# 2. 환경설정
cp .env.example .env
php artisan key:generate
# .env 에서 DB / TOSS 키 설정

# 3. DB
php artisan migrate --seed

# 4. 스토리지 링크 (상품·배너 이미지 노출용)
php artisan storage:link

# 5. 실행
php artisan serve   # http://localhost:8000
```

### XAMPP 서브폴더로 실행 (`http://localhost/sam/`)
프로젝트 루트의 `index.php` + `.htaccess`(RewriteBase `/sam/`)가 `public/`을
서브폴더에서 서빙합니다. `htdocs/sam` 에 두고 `.env`에 아래를 설정:

```
APP_URL=http://localhost/sam
ASSET_URL=http://localhost/sam
```

> XAMPP Apache PHP 버전이 8.2대인 경우, composer 의존성은 `platform.php`를
> Apache PHP 버전에 맞춰 설치해야 합니다 (`composer config platform.php 8.2.12`).

## 기본 계정 (시더)

| 구분 | 이메일 | 비밀번호 |
|------|--------|----------|
| 관리자 | admin@sam.test | password |
| 회원 | user@sam.test | password |

## 결제 키 안내

`.env`의 `TOSS_CLIENT_KEY` / `TOSS_SECRET_KEY`는 [토스페이먼츠](https://developers.tosspayments.com)
가맹점 대시보드에서 발급받은 **테스트 키**로 교체해야 실제 결제창이 동작합니다.
로컬 개발 시에는 결제 페이지의 `[개발용] 테스트 결제 완료` 링크(APP_ENV=local 전용)로
전체 주문 흐름을 검증할 수 있습니다.
