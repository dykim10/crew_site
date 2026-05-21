<?php

namespace App\Http\Controllers;

use App\Models\PhotoGallery;
use Illuminate\View\View;

class PhotoGalleryController extends Controller
{
    public function index(): View
    {
        $galleries = PhotoGallery::orderBy('session_number', 'desc')->paginate(12);
        return view('photos.index', compact('galleries'));
    }

    public function show(PhotoGallery $photoGallery): View
    {
        return view('photos.show', ['gallery' => $photoGallery]);
    }
}
