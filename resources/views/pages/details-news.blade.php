@extends('layouts.app') 
@section('title', $news->title) 

@section('content')
<div class="container-fluid bg-breadcrumb">
            <div class="container text-center custom-header" style="max-width: 900px;">
                <h4 class="text-white display-4 wow fadeInDown" data-wow-delay="0.1s">Tin tức</h4>
                <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('news') }}">Tin tức</a></li>
                    <li class="breadcrumb-item active text-primary">Chi tiết</li>
                </ol>    
            </div>
        </div>
<div class="container mb-4">
    <div class="row">
        <!-- Nội dung tin tức -->
        <div class="col-md-8 mx-auto">
            <!-- Tiêu đề tin -->
            <h1 class="my-4">{{ $news->title }}</h1>

            <!-- Ngày tạo tin -->
            <p class="text-muted">
                <i class="fa fa-calendar"></i> {{ $news->created_at->format('d/m/Y') }}
            </p>

            <!-- Ảnh tin -->
            @if ($news->image)
                <img src="{{ asset($news->image) }}" alt="{{ $news->title }}" class="img-fluid mb-4 news-image">
            @endif

            <!-- Nội dung tin tức -->
            <div class="content">
                {!! nl2br(e($news->content)) !!} <!-- Hiển thị nội dung -->
            </div>
        </div>
       <!-- Tin tức liên quan -->
       <div class="col-md-4 mt-4">
            <h4 class="mb-4">Tin tức liên quan</h4>
            <div class="related-news">
                @foreach ($relatedNews as $related)
                <div class="related-item mb-3">
                    <!-- Tiêu đề -->
                    <a href="{{ route('news.show', $related->id) }}" class="text-dark d-block">
                        <h6 class="mb-1">{{ $related->title }}</h6>
                    </a>

                    <!-- Ngày tạo -->
                    <small class="text-muted"><i class="fa fa-calendar"></i> {{ $related->created_at->format('d/m/Y') }}</small>
                </div>
                <hr>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    .news-image {
    width: 100%;             
    height: auto;            
    object-fit: cover;        
    max-height: 400px;        
    border-radius: 10px;    
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
   }
.related-news {
        font-size: 0.9rem;
    }

    .related-item h6 {
        font-weight: 600;
        font-size: 1rem;
    }

    .related-item small {
        color: #6c757d;
    }

    .related-item hr {
        margin: 10px 0;
        border-top: 1px solid #e9ecef;
    }

</style>