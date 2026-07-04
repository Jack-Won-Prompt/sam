// ---------------------------------------------------------------------------
// 프리미엄 인터랙션 (은은한 고급감) — 경량 vanilla, 외부 라이브러리 없음
// ---------------------------------------------------------------------------

// JS 활성 표시 (JS 없으면 리빌 숨김이 적용되지 않아 콘텐츠가 항상 보임)
document.documentElement.classList.add('js');

function initReveal() {
    const els = document.querySelectorAll('[data-reveal]');
    if (!els.length || !('IntersectionObserver' in window)) {
        els.forEach((el) => el.classList.add('is-visible'));
        return;
    }
    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    // 자식 순차 등장(stagger)
                    const delay = entry.target.dataset.revealDelay || 0;
                    entry.target.style.transitionDelay = delay + 'ms';
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
    );
    els.forEach((el) => io.observe(el));
}

function initHeaderShrink() {
    const header = document.querySelector('.site-header');
    if (!header) return;
    const onScroll = () => header.classList.toggle('is-shrunk', window.scrollY > 24);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
}

// 토스트 알림
window.samToast = function (message, type = 'success') {
    let wrap = document.getElementById('toast-wrap');
    if (!wrap) {
        wrap = document.createElement('div');
        wrap.id = 'toast-wrap';
        wrap.className = 'fixed left-1/2 bottom-8 z-[200] flex flex-col items-center gap-2 pointer-events-none';
        document.body.appendChild(wrap);
    }
    const t = document.createElement('div');
    const color = type === 'error' ? 'bg-red-600' : 'bg-neutral-900';
    t.className = `toast ${color} text-white text-sm font-medium px-5 py-3 rounded-full shadow-lg pointer-events-auto`;
    t.textContent = message;
    wrap.appendChild(t);
    setTimeout(() => {
        t.classList.add('toast-out');
        setTimeout(() => t.remove(), 300);
    }, 2200);
};

// 카트 뱃지 갱신 + 통 튀기
window.bumpCart = function (count) {
    const badge = document.getElementById('cart-count');
    if (typeof count === 'number') {
        if (badge) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            // 뱃지가 없던 경우(0→1) 페이지 새로고침 없이 생성은 생략, 카운트만 표시
            const link = document.getElementById('cart-link');
            if (link && count > 0) {
                const span = document.createElement('span');
                span.id = 'cart-count';
                span.className =
                    'absolute -top-1.5 right-1 bg-gold-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center';
                span.textContent = count;
                link.appendChild(span);
            }
        }
    }
    const b = document.getElementById('cart-count');
    if (b) {
        b.classList.remove('cart-bump');
        void b.offsetWidth; // reflow
        b.classList.add('cart-bump');
    }
};

// 상세 이미지 확대(돋보기) — 커서 위치 기준 내부 줌
function initImageZoom() {
    document.querySelectorAll('.zoom-frame').forEach((frame) => {
        const target = frame.querySelector('img') || frame.firstElementChild;
        if (!target) return;
        frame.addEventListener('mousemove', (e) => {
            const r = frame.getBoundingClientRect();
            const x = ((e.clientX - r.left) / r.width) * 100;
            const y = ((e.clientY - r.top) / r.height) * 100;
            target.style.transformOrigin = `${x}% ${y}%`;
            target.style.transform = 'scale(1.8)';
        });
        frame.addEventListener('mouseleave', () => {
            target.style.transform = '';
        });
    });
}

function initPremium() {
    initReveal();
    initHeaderShrink();
    initImageZoom();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPremium);
} else {
    initPremium();
}
