@php $current = $current ?? ''; @endphp
<div class="flex items-center gap-2 border-b border-neutral-200 mb-8">
    @foreach ([['notices','공지사항','support.notices'], ['faq','자주 묻는 질문','support.faq'], ['inquiries','1:1 문의','support.inquiries']] as [$key, $label, $route])
        <a href="{{ route($route) }}"
           class="px-5 py-3 text-sm font-semibold border-b-2 -mb-px transition
                  {{ $current === $key ? 'border-brand-700 text-brand-700' : 'border-transparent text-neutral-500 hover:text-brand-700' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
