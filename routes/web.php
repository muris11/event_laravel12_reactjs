<?php

use Illuminate\Support\Facades\Route;

Route::get('/fix-storage-link', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    if (file_exists($link)) {
        return 'Storage link already exists!';
    }

    try {
        symlink($target, $link);
        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        return 'Failed to create link: ' . $e->getMessage();
    }
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
