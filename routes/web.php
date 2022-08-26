<?php
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\TipeInvestController;
use App\Http\Controllers\PengajuanInvestasiController;
use App\Http\Controllers\RiwayatInvestController;
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

// Route::get('/', 'Controller@index')->name('admin.dash');
// Route::get('/', function () {
//     return view('welcome');
// });
Route::group(['middleware' => ['auth']], function () {

    
    Route::get('/', [Controller::class, 'index'])->name('admin');
  

    Route::get('/data-investor', [Controller::class, 'investor'])->name('investor.index');
    Route::post('/data-investor', [Controller::class, 'investorstore'])->name('investor.store');
    Route::post('/data-investorktp', [Controller::class, 'investorktpstore'])->name('investorktp.store');

    Route::post('/data-investor/update', [Controller::class, 'investorupdate'])->name('investor.update');
    Route::delete('/data-investor/{id}', [Controller::class, 'investorhapus'])->name('investor.destroy');
    Route::post('/data-investor/{id}/aktif', [Controller::class, 'investoraktif']);

    Route::get('/data-admin', [Controller::class, 'admin'])->name('admin.index');
    Route::post('/data-admin', [Controller::class, 'adminstore'])->name('admin.store');
    Route::post('/data-admin/update', [Controller::class, 'adminupdate'])->name('admin.update');
    Route::delete('/data-admin/{id}', [Controller::class, 'adminhapus'])->name('admin.destroy');

    Route::get('/data-notif', [NotifController::class, 'notif'])->name('notif.index');
    Route::post('/data-notif', [NotifController::class, 'notifstore'])->name('notif.store');
    Route::post('/data-notif/update', [NotifController::class, 'notifupdate'])->name('notif.update');
    Route::delete('/data-notif/{id}', [NotifController::class, 'notifhapus'])->name('notif.destroy');

    //saldo-user
    Route::get('/saldo-user', [DepositController::class, 'saldo'])->name('saldo.index');
    //pengajuan

    Route::get('/saldo-user/{id}', [PengajuanInvestasiController::class, 'pengajuan'])->name('pengajuan.index');
    Route::post('/saldo-user/pengajuan-investasi', [PengajuanInvestasiController::class, 'store'])->name('pengajuan.store');
    Route::post('/saldo-user/pengajuan-investasi/edit', [PengajuanInvestasiController::class, 'edit'])->name('pengajuan.edit');
    Route::delete('/saldo-user/pengajuan-investasi/{id}', [PengajuanInvestasiController::class, 'destroy'])->name('pengajuan.destroy');
    Route::delete('/saldo-user/pengajuan-investasi/delete/{id}', [PengajuanInvestasiController::class, 'delete'])->name('pengajuan.delete');
    //wd
    Route::get('/saldo-user/{id}/wd', [PengajuanInvestasiController::class, 'wd'])->name('wd.index');
    Route::post('/saldo-user/wd', [PengajuanInvestasiController::class, 'storewd'])->name('wd.storewd');

    //depo
    Route::post('/deposit', [RiwayatInvestController::class, 'depo'])->name('depo.store');
    Route::get('/riwayat-depo/{id}', [RiwayatInvestController::class, 'riwayat'])->name('depo.riwayat');
    Route::delete('/riwayat-depo/{id}', [RiwayatInvestController::class, 'destroy'])->name('depo.destroy');
    Route::post('/riwayat-depo/verif/{id}', [RiwayatInvestController::class, 'verif'])->name('depo.verif');


    //data-master
    //jenisinver
    Route::prefix('data-master')->group(function () {
        Route::get('/jenis-investasi', [TipeInvestController::class, 'index'])->name('jenisinvest.index');
        Route::post('/jenis-investasi', [TipeInvestController::class, 'store'])->name('jenisinvest.store');
        Route::post('/jenis-investasi/update', [TipeInvestController::class, 'edit'])->name('jenisinvest.update');
        Route::delete('/jenis-investasi/{id}', [TipeInvestController::class, 'destroy'])->name('jenisinvest.destroy');

    });





Route::get('/profil', [Controller::class, 'profil']);
Route::post('/profil', [Controller::class, 'storeprofil']);
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
