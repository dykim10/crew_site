<?php

namespace App\Http\Controllers;

use App\Models\PhotoGallery;
use Illuminate\View\View;

class PhotoGalleryController extends Controller
{
    public function index(): View
    {
        $galleries = PhotoGallery::orderByDesc('taken_at')
            ->orderByDesc('sort_order')
            ->paginate(15);
        return view('photos.index', compact('galleries'));
    }

    public function show(PhotoGallery $photoGallery): View
    {
        $photoGallery->increment('view_count');
        return view('photos.show', ['gallery' => $photoGallery]);
    }
}
