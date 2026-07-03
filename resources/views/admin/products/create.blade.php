@extends('layouts.admin')

@section('title', '상품 등록')

@section('content')
<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
    @csrf
    @include('admin.products._form', ['product' => null])
</form>
@endsection
