<?php

use App\Models\Zone;
use App\Models\Tujuan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TujuanController;

Route::get('/railway-test', function () {
    return 'Laravel berjalan!';
});


// Landing page
Route::get('/', function () {
    $tujuans = Tujuan::orderBy('unit')->orderBy('nama')->get();
    return view('landing', compact('tujuans'));
});
//Route::get('/', fn() => view('landing'));

Route::get('/get-tujuan/{unit}', function($unit) {
    $data = Tujuan::where('unit', $unit)->get();
    return response()->json($data);
});
// Landing submit form
Route::post('/submit', [SubmissionController::class,'store'])->name('submit');
Route::post('/submission', [SubmissionController::class, 'store'])->name('submission.store');
//Route::post('/submission/store', [SubmissionController::class, 'store'])->name('submission.store');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    //akun user
    Route::resource('users', UserController::class);
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Dashboard & sidebar
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    // Route::get('/datatamu', [DashboardController::class,'dataTamu'])->name('datatamu');
    Route::get('/datatamu', [SubmissionController::class, 'datatamu'])->name('submission.datatamu');
    //Route::get('/usermain', [SubmissionController::class, 'usermain'])->name('usermain');
    Route::get('/datauser', [DashboardController::class,'dataUser'])->name('datauser');
    // Route::get('/laporan', [SubmissionController::class, 'laporanNonaktif'])->name('laporan');
    Route::get('/pengaturan', [DashboardController::class,'pengaturan'])->name('pengaturan');

    // Submission CRUD
    Route::get('/submissions', [SubmissionController::class, 'datatamu'])->name('submissions');

    Route::get('/submission/add', [SubmissionController::class, 'create'])->name('submission.add');
    
    Route::get('/submission/{id}/edit', [SubmissionController::class, 'edit'])->name('submission.edit');
    Route::put('/submission/{id}', [SubmissionController::class, 'update'])->name('submission.update');
    Route::delete('/submission/delete/{id}', [SubmissionController::class, 'forceDelete'])->name('submission.forceDelete');
    Route::delete('/submission/reset-nonaktif', [SubmissionController::class, 'resetNonaktif'])->name('submission.resetNonaktif');

    // ACC Pending / Checkout
    //Route::post('/tamu/acc/{id}', [SubmissionController::class, 'accPending'])->name('tamu.acc');
    Route::post('/tamu/confirm-checkout', [SubmissionController::class, 'confirmCheckout'])->name('tamu.confirmCheckout');
    Route::post('/tamu/checkout-by-kartu/{id}', [SubmissionController::class, 'checkoutByKartu'])->name('tamu.checkoutByKartu');

    Route::post('/tamu/acc/{id}', [SubmissionController::class, 'accPending'])
        ->name('tamu.acc');

    // Checkout
    Route::post('/tamu/checkout', [SubmissionController::class, 'checkout'])
        ->name('tamu.checkout');

    // List Zones
    Route::get('/zones', [ZoneController::class, 'index'])->name('zones');
    Route::get('/zones/add', [ZoneController::class, 'create'])->name('zones.add');
    Route::post('/zones/add', [ZoneController::class, 'store'])->name('zones.store');
    Route::get('/zones/edit/{nomor}', [ZoneController::class, 'edit'])->name('zones.edit');
    Route::post('/zones/edit/{nomor}', [ZoneController::class, 'update'])->name('zones.update');
    Route::delete('/zones/delete/{nomor}', [ZoneController::class, 'destroy'])->name('zones.delete');
    Route::get('/zones/get-by-kartu/{id_kartu}', [ZoneController::class, 'getByKartu'])->name('zones.getByKartu');


    // tujuan
    //Route::get('/tujuans', [App\Http\Controllers\TujuanController::class, 'index'])->name('tujuans.index');
    Route::get('/tujuans', [TujuanController::class, 'index'])->name('tujuans.index');
    Route::get('/tujuans/create', [TujuanController::class, 'create'])->name('tujuans.create');
    Route::post('/tujuans', [TujuanController::class, 'store'])->name('tujuans.store');
    Route::get('/tujuans/{tujuan}/edit', [TujuanController::class, 'edit'])->name('tujuans.edit');
    //Route::put('/tujuans/{tujuan}', [TujuanController::class, 'update'])->name('tujuans.update');
    Route::delete('/tujuans/{tujuan}', [TujuanController::class, 'destroy'])->name('tujuans.destroy');
    Route::get('/tujuans/pdf', [TujuanController::class, 'picPdf'])->name('tujuans.picPdf');
    Route::post('/tujuans/import', [TujuanController::class, 'import'])->name('tujuans.import');
    Route::get('/tujuans/export', [TujuanController::class, 'export'])->name('tujuans.export');

    Route::resource('tujuans', TujuanController::class)->except(['update']);
    Route::patch('tujuans/{tujuan}', [TujuanController::class, 'update'])->name('tujuans.update');

    // PDF export
    Route::get('/cetak-laporans', [PdfController::class, 'cetakLaporan'])->name('cetak.laporans');

    // Laporan
    Route::get('/laporans', [LaporanController::class, 'index'])->name('laporans');
    Route::get('/laporans/data', [LaporanController::class, 'data'])->name('laporans.data'); // <-- AJAX
    // Export laporan semua / per unit
    Route::get('/laporans/export', [LaporanController::class, 'export'])->name('laporans.export');


    // Setting
    Route::get('/setting', [SettingController::class, 'edit'])->name('setting');
    Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');;

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});


Route::post('/test-submit', function(Request $request){
    return response()->json(['success' => true]);
});

// Auth routes
require __DIR__.'/auth.php';
