<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacebookController;
use App\Http\Controllers\FacebookPageController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\AiContentController;
use App\Http\Controllers\ScheduledPostController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;

use App\Models\Post;

Route::get('/', function () {
    $posts = Post::where('is_published', true)->with('category')->latest()->take(3)->get();
    return view('welcome', compact('posts'));
});

Route::view('/privacy-policy', 'privacy')->name('privacy');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Facebook Socialite
Route::prefix('auth/facebook')->group(function () {
    Route::get('/redirect', [FacebookController::class, 'redirectToFacebook'])->name('facebook.redirect');
    Route::get('/callback', [FacebookController::class, 'handleFacebookCallback'])->name('facebook.callback');
    Route::post('/disconnect', [FacebookController::class, 'disconnect'])->middleware('auth')->name('facebook.disconnect');
});

// Google Socialite
Route::prefix('auth/google')->group(function () {
    Route::get('/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
    Route::post('/disconnect', [GoogleController::class, 'disconnect'])->middleware('auth')->name('google.disconnect');
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Facebook Pages
    Route::get('/facebook/pages/{account}', [FacebookPageController::class, 'pages']);

    // Scraper
    Route::get('/scraper', [ScraperController::class, 'index']);
    Route::get('/scraper/pages/{account}', [ScraperController::class, 'fetchPages']);
    Route::post('/scraper/scrape', [ScraperController::class, 'scrape']);
    Route::post('/scraper/search/keyword', [ScraperController::class, 'searchByKeyword']);
    Route::post('/scraper/search/user', [ScraperController::class, 'searchByUser']);
    Route::get('/scraper/videos', [ScraperController::class, 'listVideos'])->name('scraper.videos');
    Route::delete('/scraper/videos/{video}', [ScraperController::class, 'deleteVideo']);

    // AI Content
    Route::get('/ai-content', [AiContentController::class, 'index']);
    Route::post('/ai-content/generate', [AiContentController::class, 'generate']);
    Route::post('/ai-content/{aiContent}/save', [AiContentController::class, 'save']);

    // Scheduled Posts
    Route::get('/scheduled-posts', [ScheduledPostController::class, 'index']);
    Route::post('/scheduled-posts', [ScheduledPostController::class, 'store']);
    Route::put('/scheduled-posts/{scheduledPost}', [ScheduledPostController::class, 'update']);
    Route::delete('/scheduled-posts/{scheduledPost}', [ScheduledPostController::class, 'destroy']);

    // Payment / Subscription
    Route::get('/pricing', [PaymentController::class, 'pricing'])->withoutMiddleware('auth');
    Route::post('/subscribe', [PaymentController::class, 'subscribe']);
    Route::get('/payment/success', [PaymentController::class, 'success']);
    Route::get('/payment/failure', [PaymentController::class, 'failure']);

    // Affiliates
    Route::get('/affiliates', [AffiliateController::class, 'index']);
    Route::get('/affiliates/withdraw', [AffiliateController::class, 'withdrawalForm']);
    Route::post('/affiliates/withdraw', [AffiliateController::class, 'requestWithdrawal']);
});

// Midtrans Notification (no auth)
Route::post('/payment/notification', [PaymentController::class, 'notification']);