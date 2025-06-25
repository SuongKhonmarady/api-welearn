<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScholarshipController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// SEO-friendly scholarship routes for frontend
Route::get('/{slug}', [ScholarshipController::class, 'showBySlug'])
    ->where('slug', '[a-z0-9-]+')
    ->name('scholarship.show');

// CSRF cookie endpoint for SPA
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
})->middleware('web');

require __DIR__.'/auth.php';
