<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\pengajuanInvestasi;
use Akaunting\Money\Money;
use App\Models\Deposit;
use App\Models\User;
use App\Models\tipeInvest;
use App\Models\RiwayatInvest;
use App\Models\saldoUser;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SaldoExport;
class SaldoUserController extends Controller
{
    public function export()
    {
        return Excel::download(new SaldoExport, 'SaldoUser.xlsx');

    }
    public function index()
    {
        if (request()->ajax()) {
            return Datatables::of(
                User::with('oDatasaldo','mDeposit','mWd')
                    ->where('role', 3)
                    ->get(),
            )
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $dataj = json_encode($data);
                    $btn =
                        "
                    <div class='dropdown d-inline mr-2'>
                    <button class='btn btn-primary dropdown-toggle' type='button'
                        id='dropdownMenuButton' data-toggle='dropdown' aria-haspopup='true'
                        aria-expanded='false'>
                        Aksi
                    </button>
                    <div class='dropdown-menu'>
                    <a type='button'  onclick='depo(" .
                        $dataj .
                        ")'
                    class='dropdown-item' href='#'>Deposit</a>
                   
                    <a type='button' target='_blank' href='" .
                    url('data-saldo/riwayat-deposit/') .
                    '/' .
                    $data->id .
                    "'
                    class='dropdown-item' href='#'>Riwayat Deposit</a>
                        
                        <a type='button'  onclick='wd(" .
                        $dataj .
                        ")'
                         class='dropdown-item' href='#'>WD</a>
                         
                         <a type='button' target='_blank' href='" .
                         url('data-saldo/riwayat-wd/') .
                         '/' .
                         $data->id .
                         "'
                         class='dropdown-item' href='#'>Riwayat WD</a>
                      
                         <a type='button'  onclick='staffdel(" .
                        $data->id .
                        ")'  class='dropdown-item' href='#'>Reset</a>
                    </div>
                </div>
                    ";
                    if ($data->oDatasaldo == null) {
                        $btn = 'Belum Verifikasi';
                    }
                    return $btn;
                })
                ->addColumn('deponya', function ($data) {
                    
                    if ($data->mDeposit) {
                        $btn = Money::IDR($data->mDeposit->sum('jumlah'), true);
                    } else {
                        $btn = Money::IDR(0, true);
                    }

                    return $btn;
                })
                ->addColumn('wdnya', function ($data) {
                    
                    if ($data->mWd) {
                        $btn = Money::IDR($data->mWd->sum('jumlah'), true);
                    } else {
                        $btn = Money::IDR(0, true);
                    }

                    return $btn;
                })
                ->addColumn('saldonya', function ($data) {
                    $btn = Money::IDR($data->oDatasaldo->saldo_active ?? 0, true);

                    return $btn;
                })
                ->addColumn('saldotnya', function ($data) {
                    $btn = Money::IDR($data->oDatasaldo->saldo_tertahan ?? 0, true);

                    return $btn;
                })
              ->rawColumns(['aksi', 'deponya', 'wdnya'])
                ->make(true);
        }
        return view('admin.datasaldo');
    }
    public function storedepo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'depo' => ['required', 'string', 'max:255'],
            'id' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = Deposit::create([
            'id_user' => $request->id,
            'jumlah' => $request->depo,
            'status' => 2,
        ]);

        if ($data) {
            $saldo = saldoUser::where('id_user',$request->id)->first();
            $saldo->saldo_active = $saldo->saldo_active + $request->depo;
            $saldo->save();
            return 'success';
        }
    }
    public function storewithdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wd' => ['required', 'string', 'max:255'],
            'id' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = Withdraw::create([
            'id_user' => $request->id,
            'jumlah' => $request->wd,
            'status' => 2,
        ]);

        if ($data) {
            $saldo = saldoUser::where('id_user',$request->id)->first();
            $saldo->saldo_active = $saldo->saldo_active - $request->wd;
            $saldo->save();
            return 'success';
        }
    }
    public function reset($id)
    {
        $data =saldoUser::where('id_user',$id)->first();
        if ($data) {
            $data->saldo_active = 0;
            $data->saldo_tertahan = 0;
            $depo = Deposit::where('id_user',$id)->delete();
            $wd = Withdraw::where('id_user',$id)->delete();
            $data->save();
            return 'success';
        }
    }
}
