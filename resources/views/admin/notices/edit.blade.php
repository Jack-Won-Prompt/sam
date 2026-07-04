@extends('layouts.admin')

@section('title', '공지 수정')

@section('content')
<form method="POST" action="{{ route('admin.notices.update', $notice) }}">
    @csrf @method('PUT')
    @include('admin.notices._form', ['notice' => $notice])
</form>
@endsection
