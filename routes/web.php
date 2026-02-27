<?php
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'check.banned'])->name('dashboard');

Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('/colocations', ColocationController::class);
    Route::post('/colocations/{colocation}/invite', [InvitationController::class, 'send'])
        ->name('colocations.invite.send');

});
Route::get('/invitations/{token}', [InvitationController::class, 'show'])
    ->name('invitations.show');

Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])
        ->name('invitations.accept');
    Route::post('/invitations/{token}/decline', [InvitationController::class, 'decline'])
        ->name('invitations.decline');
});
require __DIR__.'/auth.php';
