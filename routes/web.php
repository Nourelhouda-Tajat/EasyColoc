<?php
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'check.banned'])->name('dashboard');

Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('colocations', ColocationController::class)->except(['edit']);
    Route::post('/colocations/{colocation}/leave',[ColocationController::class, 'leave'])->name('colocations.leave');
    Route::delete('/colocations/{colocation}/members/{member}', [ColocationController::class, 'removeMember'])->name('colocations.members.remove');

    Route::post('/colocations/{colocation}/settlements', [SettlementController::class, 'store'])->name('colocations.settlements.store');

    Route::post('/colocations/{colocation}/categories', [CategoryController::class, 'store'])->name('colocations.categories.store');
    Route::delete('/colocations/{colocation}/categories/{category}', [CategoryController::class, 'destroy'])->name('colocations.categories.destroy');

    Route::post('/colocations/{colocation}/invite', [InvitationController::class, 'send'])->name('colocations.invite.send');
    Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{token}/decline', [InvitationController::class, 'decline'])->name('invitations.decline');
    

    Route::resource('colocations.expenses', ExpenseController::class)->only(['store', 'update', 'destroy']);

});

require __DIR__.'/auth.php';
