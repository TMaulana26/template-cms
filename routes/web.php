<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('menu', MenuController::class);
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/404', function () {
    $locale = session('locale', config('app.locale'));
    app()->setLocale($locale);

    return inertia('Errors/NotFound', [
        'status' => 404,
        'isAuthenticated' => auth()->check(),
        'user' => auth()->check() ? [
            'name' => auth()->user()->name,
        ] : null,
    ])
        ->toResponse(request())
        ->setStatusCode(404);
})->name('404');

Route::get('/test', function () {
    return Inertia::render('Test');
})->name('test');

Route::get('language/{locale}', function ($locale) {
    session()->put('locale', $locale);
    return redirect()->back();
})->name('language');

require __DIR__ . '/auth.php';
