@extends('layouts.admin')

@section('title', '상품 수정')

@section('content')
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('admin.products._form', ['product' => $product])
</form>
@endsection
