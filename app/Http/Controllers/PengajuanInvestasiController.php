<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\pengajuanInvestasi;
use Akaunting\Money\Money;
use App\Models\RiwayatInvest;
use App\Models\User;
use App\Models\tipeInvest;
class PengajuanInvestasiController extends Controller
{
    public function storewd(Request $request)
    {
        $validator = Validator::make($request->all(), [
           
            'id_investasi' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = pengajuanInvestasi::where('id', $request->id_investasi)->first();

        $data->total_wd = $request->jml_wd;
        $data->total_bonus = $request->jml_bonus;
        $data->tanggal_penarikan = date('Y/m/d');
        $data->status_wd = 2;
        $data->status_investasi = 2;
        $data->save();

        if ($data) {
            return 'success';
        }
    }
    public function wd($id)
    {
        $tipe = tipeInvest::get();
        $user = User::with('oKtp')
            ->where('id', $id)
            ->first();
        if (request()->ajax()) {
            return Datatables::of(
                pengajuanInvestasi::with('oRiwayat', 'oTipe')
                    ->where('id_user', $id)
                    ->get(),
            )
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $sisa = $data->jumlah_investasi - $data->oRiwayat->sum('jumlah_depo');
                    $dataj = json_encode($data);
                    $btn =
                        "<ul class='list-inline mb-0'>";
                        if ($data->status_investasi == 2) {
                            $btn .= "<li class='list-inline-item'>
                            <button type='button' disabled='enabled' data-toggle='modal' onclick='depo(" .
                            $dataj .
                            ',' .
                            json_encode($sisa) .
                            ")'   class='btn-sm btn btn-success btn-xs mb-1'>Withdraw</button>
                            </li>";
                        }else{
                            $btn .= "<li class='list-inline-item'>
                            <button type='button'  data-toggle='modal' onclick='depo(" .
                            $dataj .
                            ',' .
                            json_encode($sisa) .
                            ")'   class='btn-sm btn btn-success btn-xs mb-1'>Withdraw</button>
                            </li>";
                        }
                   
                        $btn .= "<li class='list-inline-item'>
                        <button type='button' data-toggle='modal' onclick='depo(" .
                        $dataj .
                        ',' .
                        json_encode($sisa) .
                        ")'   class='btn-sm btn btn-primary btn-xs mb-1'>Detail</button>
                        </li>
                  
                   
                </ul>";
                    return $btn;
                })
                ->addColumn('investnya', function ($data) {
                    $btn = Money::IDR($data->jumlah_investasi, true);
                    return $btn;
                })
                ->addColumn('datainves', function ($data) {
                    $btn = $data->total_wd;;
                    return $btn;
                })
                ->addColumn('databonus', function ($data) {
                    $btn = $data->total_bonus;;
                    return $btn;
                })
                ->addColumn('deponya', function ($data) {
                    $btn = Money::IDR($data->oRiwayat->sum('jumlah_depo'), true);
                    return $btn;
                })
                ->addColumn('datadepo', function ($data) {
                    $btn = $data->oRiwayat->sum('jumlah_depo');
                    return $btn;
                })
                ->addColumn('estimasinya', function ($data) {
                    $btn = $data->jumlah_investasi * $data->oTipe->persenan * 0.01 + $data->jumlah_investasi;
                    $btn = Money::IDR($btn, true);
                    return $btn;
                })
                ->addColumn('tipenya', function ($data) {
                    $btn = $data->oTipe->periodik;
                    return $btn;
                })
                ->addColumn('statusnya', function ($data) {
                    if ($data->status_wd == 1) {
                        $btn = '<span class="badge badge-primary"> Belum WD </span>';
                    } else {
                        $btn = '<span class="badge badge-success"> WD </span>';
                    }
                    return $btn;
                })
                ->rawColumns(['aksi','databonus', 'estimasinya', 'statusnya', 'datainves', 'datadepo', 'tipenya', 'investnya', 'deponya'])
                ->make(true);
        }
        return view('admin.wd', compact('tipe', 'user'));
    }
    public function pengajuan($id)
    {
        $tipe = tipeInvest::get();
        $user = User::with('oKtp')
            ->where('id', $id)
            ->first();
        if (request()->ajax()) {
            return Datatables::of(
                pengajuanInvestasi::with('oRiwayat')
                    ->where('id_user', $id)
                    ->get(),
            )
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $sisa = $data->jumlah_investasi - $data->oRiwayat->sum('jumlah_depo');
                    $dataj = json_encode($data);
                    $btn =
                        "<ul class='list-inline mb-0'>
                        <li class='list-inline-item'>
                        <button type='button' data-toggle='modal' onclick='depo(" .
                        $dataj .
                        ',' .
                        json_encode($sisa) .
                        ")'   class='btn-sm btn btn-success btn-xs mb-1'>Depo</button>
                        </li>
             
                <li class='list-inline-item'>
                <button type='button' data-toggle='modal' onclick='riwayat(" .
                        $dataj .
                        ")'   class='btn-sm btn btn-warning btn-xs mb-1'>Riwayat</button>
                </li>
                    <li class='list-inline-item'>
                    <button type='button'  onclick='resetInvestasi(" .
                        $data->id .
                        ")'   class='btn-sm btn btn-danger btn-xs mb-1'>Reset</button>
                    </li>
                    <li class='list-inline-item'>
                    <div class='dropdown d-inline mr-2'>
                    <button class='btn btn-sm btn-primary dropdown-toggle' type='button' id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                      Aksi
                    </button>
                    <div class='dropdown-menu'>
                      <a class='dropdown-item'  href='#' onclick='editInvestasi(" .
                        $dataj .
                        ")' >Edit</a>
                      <a class='dropdown-item' onclick='deleteInvestasi(" .
                        $data->id .
                        ")'   href='#'>Hapus</a>
                    </div>
                  </div>
                  </li>
                </ul>";
                    return $btn;
                })
                ->addColumn('investnya', function ($data) {
                    $btn = Money::IDR($data->jumlah_investasi, true);
                    return $btn;
                })
                ->addColumn('datainves', function ($data) {
                    $btn = $data->jumlah_investasi;
                    return $btn;
                })
                ->addColumn('deponya', function ($data) {
                    $btn = Money::IDR($data->oRiwayat->sum('jumlah_depo'), true);
                    return $btn;
                })
                ->addColumn('datadepo', function ($data) {
                    $btn = $data->oRiwayat->sum('jumlah_depo');
                    return $btn;
                })
                ->addColumn('tipenya', function ($data) {
                    $btn = $data->oTipe->periodik;
                    return $btn;
                })
                ->addColumn('statusnya', function ($data) {
                    if ($data->status_investasi == 1) {
                        $btn = '<span class="badge badge-primary"> Progress </span>';
                    } else {
                        $btn = '<span class="badge badge-success"> Lunas </span>';
                    }
                    return $btn;
                })
                ->rawColumns(['aksi', 'statusnya', 'datainves', 'datadepo', 'tipenya', 'investnya', 'deponya'])
                ->make(true);
        }
        return view('admin.pengajuan', compact('tipe', 'user'));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe' => ['required', 'string', 'max:255'],
            'investasi' => ['required', 'string', 'max:255'],
            'id' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = pengajuanInvestasi::create([
            'id_user' => $request->id,
            'jumlah_investasi' => $request->investasi,
            'tipe_investasi' => $request->tipe,
            'status' => 1,
            'status_wd' => 1,
            'status_investasi' => 1,
            'total_depo' => 0,
            'total_wd' => 0,
            'total_bonus' => 0,
        ]);

        if ($data) {
            return 'success';
        }
    }
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe' => ['required', 'string', 'max:255'],
            'investasi' => ['required', 'string', 'max:255'],
            'id' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = pengajuanInvestasi::where('id', $request->id)->first();
        $data->jumlah_investasi = $request->investasi;
        $data->tipe_investasi = $request->tipe;
        $data->save();

        if ($data) {
            return 'success';
        }
    }
    public function destroy($id)
    {
        $data = pengajuanInvestasi::where('id', $id)->first();
        if ($data) {
            RiwayatInvest::where('id_pengajuan', $id)->delete();
            $data->total_depo = 0;
            $data->save();
            return 'success';
        }
    }
    public function delete($id)
    {
        $data = pengajuanInvestasi::where('id', $id)->first();
        if ($data) {
            RiwayatInvest::where('id_pengajuan', $id)->delete();
            $data->delete();
            return 'success';
        }
    }
}
