@extends('layouts.admin')

@section('title', '공지 등록')

@section('content')
<form method="POST" action="{{ route('admin.notices.store') }}">
    @csrf
    @include('admin.notices._form', ['notice' => null])
</form>
@endsection
