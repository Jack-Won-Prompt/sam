<footer class="mt-20 bg-neutral-900 text-neutral-400">
    <div class="container-shop py-12">
        <div class="grid gap-8 md:grid-cols-4">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl">🌿</span>
                    <span class="text-lg font-extrabold text-white">강원<span class="text-gold-400">산양삼</span></span>
                </div>
                <p class="text-sm leading-relaxed">해발 700m 강원도 청정 산속에서<br>자연 그대로 키운 산양삼을 전해드립니다.</p>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">고객센터</h4>
                <p class="text-2xl font-bold text-white">1588-0000</p>
                <p class="text-sm mt-2">평일 09:00 ~ 18:00<br>점심 12:00 ~ 13:00 / 주말·공휴일 휴무</p>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">쇼핑안내</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('cart.index') }}" class="hover:text-white">장바구니</a></li>
                    <li><a href="{{ route('collection', 'best') }}" class="hover:text-white">베스트 상품</a></li>
                    <li><a href="{{ route('collection', 'sale') }}" class="hover:text-white">특가 상품</a></li>
                    @auth<li><a href="{{ route('order.index') }}" class="hover:text-white">주문/배송 조회</a></li>@endauth
                </ul>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-3 text-sm">입금계좌</h4>
                <p class="text-sm">농협 000-0000-0000-00<br>예금주 : (주)강원산양삼</p>
            </div>
        </div>

        <div class="border-t border-neutral-800 mt-10 pt-6 text-xs leading-relaxed">
            <p>(주)강원산양삼 &nbsp;|&nbsp; 대표 홍길동 &nbsp;|&nbsp; 사업자등록번호 000-00-00000 &nbsp;|&nbsp; 통신판매업 제2026-강원홍천-0000호</p>
            <p class="mt-1">주소 : 강원특별자치도 홍천군 홍천읍 산양삼로 1 &nbsp;|&nbsp; 개인정보관리책임자 : 홍길동</p>
            <p class="mt-3 text-neutral-500">© {{ date('Y') }} 강원산양삼. All rights reserved. <span class="text-neutral-600">본 사이트는 데모용으로 제작되었습니다.</span></p>
        </div>
    </div>
</footer>
