<?php

use App\Http\Controllers\InformationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\NfcController;
use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CardController as AdminCardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\AppController as AdminAppController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->to('/card');
    } else {
        return redirect()->to('/login');
    }
})->name('home');

Route::get('/nfc/{slug}', [NfcController::class, 'show'])->name('nfc.show');

Route::middleware('auth')->group(function () {
    Route::get('/card', [CardController::class, 'index'])->name('card');
    Route::get('/card/{card}', [CardController::class, 'show'])->name('card.show');
    Route::put('/card', [CardController::class, 'update'])->name('card.update');
    Route::delete('/card', [CardController::class, 'destroy'])->name('card.destroy');
    Route::post('/card/location', [CardController::class, 'addLocation'])->name('card.location');
    Route::post('/card/billing_info', [CardController::class, 'billingInformation'])->name('card.billing_info');
    Route::patch('/card/removeLocation', [CardController::class, 'removeLocation'])->name('card.removeLocation');
    Route::post('/card/importFrom', [CardController::class, 'importFrom'])->name('card.importFrom');
    Route::put('/card/switchCatalog/{card}', [CardController::class, 'switchCatalog'])->name('card.switchCatalog');
    Route::patch('/card/default_card', [ProfileController::class, 'changeDefaultCard'])->name('card.change_default');

    // Catalog
    Route::get('/card/{card}/catalog', [CatalogController::class, 'show'])->name('card.catalog');
    Route::post('/card/catalog/addImage', [CatalogController::class, 'store'])->name('card.catalog.addImage');
    Route::delete('/card/catalog/delete/{image}', [CatalogController::class, 'destroy'])->name('card.catalog.destroy');
    Route::patch('/card/catalog/updateOrder', [CatalogController::class, 'updateOrder'])->name('card.catalog.updateOrder');

    // PDF
    Route::post('/card/{card}/pdfs', [PdfController::class, 'store'])->name('card.pdf.store');
    Route::delete('/pdfs/{pdf}', [PdfController::class, 'destroy'])->name('pdf.destroy');

    // About
    Route::post('/card/about', [CardController::class, 'storeAbout'])->name('card.about');


    Route::post('/information', [InformationController::class, 'store'])->name('information.store');
    Route::put('/information', [InformationController::class, 'update'])->name('information.update');
    Route::patch('/information', [InformationController::class, 'updateOrder'])->name('information.updateOrder');
    Route::delete('/information/{information}', [InformationController::class, 'destroy'])->name('information.destroy');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Social Login
Route::get('/login/google', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'handleGoogleCallback']);

require __DIR__.'/auth.php';

Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('change-language');

// Admin Group
Route::group(['middleware' => ['auth', 'admin']], function () {
    // Define admin routes here
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/key/decrypt', [AdminController::class, 'decrypt'])->name('admin.key.decrypt');

    // User Operations
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::put('/admin/users/{user}/update', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/admin/toggle-premium/{user}', [AdminUserController::class, 'togglePremium'])->name('toggle.premium');

    // Card Operations
    Route::get('/admin/cards', [AdminCardController::class, 'index'])->name('admin.cards.index');
    Route::get('/admin/cards/{card}', [AdminCardController::class, 'show'])->name('admin.cards.show');
    Route::delete('/admin/cards/{card}', [AdminCardController::class, 'destroy'])->name('admin.cards.destroy');

    // Category Operations
    Route::get('/admin/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/admin/categories/{category}', [AdminCategoryController::class, 'show'])->name('admin.categories.show');
    Route::delete('/admin/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');
    Route::put('/admin/categories/update-order', [AdminCategoryController::class, 'updateOrder'])->name('admin.categories.updateOrder');
    Route::put('/admin/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::post('/admin/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');

    // App Operations
    Route::get('/admin/apps', [AdminAppController::class, 'index'])->name('admin.apps.index');
    Route::get('/admin/apps/{app}', [AdminAppController::class, 'show'])->name('admin.apps.show');
    Route::delete('/admin/apps/{app}', [AdminAppController::class, 'destroy'])->name('admin.apps.destroy');
    Route::put('/admin/apps/update-order', [AdminAppController::class, 'updateOrder'])->name('admin.apps.updateOrder');
    Route::put('/admin/apps/{app}', [AdminAppController::class, 'update'])->name('admin.apps.update');
    Route::post('/admin/apps', [AdminAppController::class, 'store'])->name('admin.apps.store');
});
