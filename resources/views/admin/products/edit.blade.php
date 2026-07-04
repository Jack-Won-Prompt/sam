@extends('layouts.admin')

@section('title', '상품 수정')

@section('content')
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.products._form', ['product' => $product])
</form>
@endsection

@push('scripts')
<script>
async function deleteProductImage(id) {
    if (!confirm('이미지를 삭제할까요?')) return;
    const token = document.querySelector('meta[name=csrf-token]').content;
    const res = await fetch('{{ url('admin/product-images') }}/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        body: new URLSearchParams({ _method: 'DELETE' }),
    });
    if (res.ok) document.getElementById('pimg-' + id)?.remove();
}
</script>
@endpush
