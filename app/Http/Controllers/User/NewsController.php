<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        $newsList = News::orderBy('created_at', 'desc')->paginate(3);
        return view('pages.news', compact('newsList'));
    }

    public function show($id)
    {
        $news = News::findOrFail($id); 
        $relatedNews = News::where('id', '!=', $news->id)
            ->latest()
            ->take(5)
            ->get();
        return view('pages.details-news', compact('news','relatedNews'));
    }
}