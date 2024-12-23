@extends('super_admin.layouts.app')
@section('title', 'Chi tiết tin tức')

@section('content')
<div class="container my-3">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Tiêu đề Tin tức -->
            <h1 class="mb-4">{{ $news->title }}</h1>

            <!-- Ảnh tin tức -->
            @if ($news->image)
            <img src="{{ asset($news->image) }}" alt="{{ $news->title }}" class="img-fluid mb-4">
            @endif

            <!-- Nội dung Tin tức -->
            <div class="content">
                {!! nl2br(e($news->content)) !!}
            </div>

            <!-- Thời gian đăng tin -->
            <p class="mt-4 text-muted">Đăng lúc: {{ $news->created_at->format('d/m/Y H:i') }}</p>

            <!-- Quay lại danh sách tin tức -->
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary mt-2">Quay lại danh sách tin tức</a>
        </div>
    </div>
</div>
@endsection
<style>
    img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
}

</style>