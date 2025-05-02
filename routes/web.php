<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\MatchController as AdminMatchController;
use App\Http\Controllers\ArduinoSerialController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ArduinoPythonController;
use App\Http\Controllers\GeneralChatbotController;

// ✅ Page d'accueil du guichet avec deux boutons : Réserver un match / Voir vos tickets
Route::get('/', function () {
    return view('guichet');
})->name('home');

// Routes utilisateur authentifié
Route::middleware(['auth', 'verified'])->group(function () {
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestion des matchs
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/{match}', [MatchController::class, 'show'])->name('matches.show');
    Route::get('/matches/{match}/sections', [MatchController::class, 'sections'])->name('matches.sections');

    // Gestion des tickets
    Route::get('/my-tickets', [TicketController::class, 'index'])->name('my-tickets');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');

    // Achat de ticket par section
    Route::post('/matches/{match}/purchase', [TicketController::class, 'purchase'])->name('tickets.purchase');
});

// Déconnexion
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout')->middleware('auth');

// Routes Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Gestion des matchs
    Route::resource('matches', AdminMatchController::class)->names([
        'index' => 'admin.matches.index',
        'create' => 'admin.matches.create',
        'store' => 'admin.matches.store',
        'show' => 'admin.matches.show',
        'edit' => 'admin.matches.edit',
        'update' => 'admin.matches.update',
        'destroy' => 'admin.matches.destroy',
    ]);

    // Gestion des réservations
    Route::get('/bookings', [BookingController::class, 'index'])->name('admin.bookings.index');

    // Gestion des utilisateurs
    Route::resource('users', UserController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ])->except(['show']);
    Route::get('users/{user}', [UserController::class, 'show'])->name('admin.users.show');

    // Gestion des sections
    Route::get('/matches/{match}/sections', [AdminMatchController::class, 'manageSections'])->name('admin.matches.sections');
    Route::post('/matches/{match}/sections', [AdminMatchController::class, 'storeSections'])->name('admin.matches.sections.store');
    Route::delete('/matches/{match}/sections/{sectionId}', [AdminMatchController::class, 'deleteSection'])->name('admin.matches.sections.delete');

    // Upload images 360
    Route::post('/matches/{match}/sections/upload360', [AdminMatchController::class, 'upload360'])->name('admin.sections.upload360');
});

// Routes chatbot
Route::post('/chatbot/message', [ChatbotController::class, 'handle'])->name('chatbot.message');
Route::post('/chatbot/general', [GeneralChatbotController::class, 'handle'])->name('chatbot.general');

// Routes Arduino
Route::prefix('arduino')->group(function () {
    Route::get('/ports', [ArduinoPythonController::class, 'listPorts']);
    Route::get('/connect', [ArduinoPythonController::class, 'connect']);
    Route::get('/read', [ArduinoPythonController::class, 'read']);
    Route::get('/disconnect', [ArduinoPythonController::class, 'disconnect']);
});

// Auth (login/register)
require __DIR__.'/auth.php';
