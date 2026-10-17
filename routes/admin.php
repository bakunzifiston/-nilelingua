<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EntryController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\StringController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Admin dashboard. There is no public registration: administrators are created by another
// administrator (Admin → Administrators) or with `php artisan cms:admin`.
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [AuthController::class, 'show'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:admin-login');
    });

    Route::middleware('admin')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        // Pages, services, articles and settings.
        Route::get('content/{entry}', [EntryController::class, 'edit'])->name('entries.edit');
        Route::put('content/{entry}', [EntryController::class, 'update'])->name('entries.update');
        Route::post('content/{entry}/publish', [EntryController::class, 'publish'])->name('entries.publish');
        Route::post('content/{entry}/discard', [EntryController::class, 'discard'])->name('entries.discard');
        Route::post('content/{entry}/unpublish', [EntryController::class, 'unpublish'])->name('entries.unpublish');
        Route::get('content/{entry}/preview', [EntryController::class, 'preview'])->name('entries.preview');
        Route::get('content/{entry}/history', [EntryController::class, 'history'])->name('entries.history');
        Route::post('content/{entry}/history/{revision}', [EntryController::class, 'restoreRevision'])->name('entries.restore-revision');
        Route::delete('content/{entry}', [EntryController::class, 'destroy'])->name('entries.destroy');
        Route::post('content/{entry}/restore', [EntryController::class, 'restore'])->name('entries.restore')->withTrashed();

        // Services and Knowledge Center articles.
        Route::get('collections/{type}', [CollectionController::class, 'index'])->name('collections.index')->whereIn('type', ['service', 'article']);
        Route::get('collections/{type}/create', [CollectionController::class, 'create'])->name('collections.create')->whereIn('type', ['service', 'article']);
        Route::post('collections/{type}', [CollectionController::class, 'store'])->name('collections.store')->whereIn('type', ['service', 'article']);
        Route::post('collections/{type}/order', [CollectionController::class, 'reorder'])->name('collections.reorder')->whereIn('type', ['service', 'article']);

        // Website form submissions.
        Route::get('submissions/{type}', [SubmissionController::class, 'index'])->name('submissions.index')->whereIn('type', ['quotes', 'applications', 'subscribers']);
        Route::get('submissions/{type}/{id}', [SubmissionController::class, 'show'])->name('submissions.show')->whereIn('type', ['quotes', 'applications'])->whereNumber('id');
        Route::get('submissions/{type}/{id}/file', [SubmissionController::class, 'download'])->name('submissions.download')->whereIn('type', ['quotes', 'applications'])->whereNumber('id');
        Route::post('submissions/{type}/{id}/unread', [SubmissionController::class, 'markUnread'])->name('submissions.unread')->whereIn('type', ['quotes', 'applications'])->whereNumber('id');
        Route::delete('submissions/{type}/{id}', [SubmissionController::class, 'destroy'])->name('submissions.destroy')->whereIn('type', ['quotes', 'applications', 'subscribers'])->whereNumber('id');

        // Interface text (headings, buttons and labels in the templates).
        Route::get('interface-text', [StringController::class, 'index'])->name('strings.index');
        Route::put('interface-text', [StringController::class, 'update'])->name('strings.update');

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::post('media', [MediaController::class, 'store'])->name('media.store');
        Route::put('media/{media}', [MediaController::class, 'update'])->name('media.update');
        Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('administrators', [UserController::class, 'index'])->name('users.index');
        Route::post('administrators', [UserController::class, 'store'])->name('users.store');
        Route::delete('administrators/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('account', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [AccountController::class, 'update'])->name('account.update');
    });
});
