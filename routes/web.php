<?php

use App\Http\Controllers\API\CAIUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembelajaranController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\TugasController;
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
Route::post('apiregister',  [CAIUser::class, 'store']);

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


    //text
    Route::get('/data-materi/{id}/text', [materiController::class, 'materitext']);
    Route::post('/data-materi/text', [materiController::class, 'materitextstore'])->name('materitext.store');
    Route::post('/data-materi/textup', [materiController::class, 'materitextupdate'])->name('materitext.update');
    Route::delete('/data-materi/{id}/text', [materiController::class, 'materitexthapus']);

    Route::get('/data-materi/{id}/file', [materiController::class, 'materifile']);
    Route::post('/data-materi/file', [materiController::class, 'materifilestore'])->name('materifile.store');
    Route::post('/data-materi/fileup', [materiController::class, 'materifileupdate'])->name('materifile.update');
    Route::delete('/data-materi/{id}/file', [materiController::class, 'materifilehapus']);

    Route::get('/data-materi/{id}/video', [materiController::class, 'materivideo']);
    Route::post('/data-materi/video', [materiController::class, 'materivideostore'])->name('materivideo.store');
    Route::post('/data-materi/videoup', [materiController::class, 'materivideoupdate'])->name('materivideo.update');
    Route::delete('/data-materi/{id}/video', [materiController::class, 'materivideohapus']);

    Route::get('/data-materi/{id}/tugas', [TugasController::class, 'materitugas']);
    Route::post('/data-materi/tugas', [TugasController::class, 'materitugasstore'])->name('materitugas.store');
    Route::post('/data-materi/tugasup', [TugasController::class, 'materitugasupdate'])->name('materitugas.update');
    Route::delete('/data-materi/{id}/tugas', [TugasController::class, 'materitugashapus']);



    Route::get('/profil', [Controller::class, 'profil']);
    Route::post('/profil', [Controller::class, 'storeprofil']);
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
