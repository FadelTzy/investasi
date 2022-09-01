<?php

namespace App\Http\Controllers;

use Akaunting\Money\Money;
use App\Models\Deposit;
use App\Models\saldoUser;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use App\Models\Withdraw;

class DepositController extends Controller
{
    public function saldo()
    {
        
        if (request()->ajax()) {
            return Datatables::of(
                User::with('oSaldo')->where('role',3)
                    ->get(),
            )
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $dataj = json_encode($data);
                    $btn =
                        "<ul class='list-inline mb-0'>
                <li class='list-inline-item'>
                <a type='button' target='_blank' href='" . url('saldo-user') . '/' . $data->id ."'   class='btn btn-success btn-xs mb-1'>Invest</a>
                </li>
                <li class='list-inline-item'>
                <a type='button' target='_blank' href='" . url('saldo-user') . '/' . $data->id . '/wd'."'     class='btn btn-primary btn-xs mb-1'>WD</a>
                </li>
                
                     
                </ul>";
                    return $btn;
                })
                ->addColumn('deponya', function ($data) {
                    
                    if ($data->oSaldo) {
                        $btn = Money::IDR($data->oSaldo->sum('total_depo'), true);

                    }else{
                        $btn = Money::IDR(0, true);
                    }

                    return $btn;
                })
                ->addColumn('investnya', function ($data) {
                    
                    if ($data->oSaldo) {
                        $btn = Money::IDR($data->oSaldo->sum('jumlah_investasi'), true);

                    }else{
                        $btn = Money::IDR(0, true);
                    }

                    return $btn;
                })
                ->addColumn('wdnya', function ($data) {
                    if ($data->oSaldo) {
                        $btn = Money::IDR(0, true);
                    }else{
                        $btn = Money::IDR(0, true);

                    }
                    return $btn;
                })
                ->rawColumns(['aksi','investnya', 'deponya','wdnya'])
                ->make(true);
        }
        return view('admin.saldo');
    }
    public function delete($id)
    {
        $data = Deposit::where('id',$id)->first();
        if ($data) {
            if ($data->status == 2) {
                # code...
                $datasaldo = saldoUser::where('id_user',$data->id_user)->first();
                $datasaldo->saldo_active = $datasaldo->saldo_active - $data->jumlah;
                $datasaldo->save();
            }
            $data->delete();
        return 'success';
        }
    }
    public function riwayat($id)
    {
        $user = User::where('id',$id)->first();
        if (request()->ajax()) {
            return Datatables::of(
                Deposit::where('id_user',$id)
                    ->orderBy('created_at','DESC')->get(),
            )
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $dataj = json_encode($data);
                    $btn =
                        "<ul class='list-inline mb-0'>
           
                    <li class='list-inline-item'>
                    <button type='button'  onclick='staffdel(" .
                        $data->id .
                        ")'   class='btn btn-danger btn-xs mb-1'>Hapus</button>
                    </li>
                     
                </ul>";
                    return $btn;
                })
                ->addColumn('jumlahnya', function ($data) {
                    
                        $btn = Money::IDR($data->jumlah, true);

                

                    return $btn;
                })
                ->addColumn('statusnya', function ($data) {
                    if ($data->status == 1) {
                        $btn = '<span class="badge badge-warning"> Menunggu </span>';
                    }
                    if ($data->status == 2) {
                        $btn = '<span class="badge badge-success"> Diterima </span>';
                    }
                    if ($data->status == 3) {
                        $btn = '<span class="badge badge-danger"> Ditolak </span>';
                    }
                    return $btn;
                })
                ->addColumn('datadepo', function ($data) {
                   $btn = $data->jumlah;
                    return $btn;
                })
                ->addColumn('tanggalnya', function ($data) {
                    $btn = $data->created_at->format('Y/m/d H:i:s');
                     return $btn;
                 })
                 ->rawColumns(['aksi','tanggalnya','jumlahnya','statusnya', 'deponya','wdnya'])
                ->make(true);
        }
        return view('admin.riwayatdepo',compact('user'));
    }
    public function riwayatwd($id)
    {
        $user = User::with('oDatasaldo')->where('id',$id)->first();
        if (request()->ajax()) {
            return Datatables::of(
                Withdraw::where('id_user',$id)
                    ->orderBy('created_at','DESC')->get(),
            )
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $dataj = json_encode($data);
                    $btn =
                        "<ul class='list-inline mb-0'>
           
                    <li class='list-inline-item'>
                    <button type='button'  onclick='staffdel(" .
                        $data->id .
                        ")'   class='btn btn-danger btn-xs mb-1'>Hapus</button>
                    </li>
                     
                </ul>";
                    return $btn;
                })
                ->addColumn('jumlahnya', function ($data) {
                    
                        $btn = Money::IDR($data->jumlah, true);

                

                    return $btn;
                })
                ->addColumn('statusnya', function ($data) {
                    if ($data->status == 1) {
                        $btn = '<span class="badge badge-warning"> Menunggu </span>';
                    }
                    if ($data->status == 2) {
                        $btn = '<span class="badge badge-success"> Diterima </span>';
                    }
                    if ($data->status == 3) {
                        $btn = '<span class="badge badge-danger"> Ditolak </span>';
                    }
                    return $btn;
                })
                ->addColumn('datadepo', function ($data) {
                   $btn = $data->jumlah;
                    return $btn;
                })
                ->addColumn('tanggalnya', function ($data) {
                    $btn = $data->created_at->format('Y/m/d H:i:s');
                     return $btn;
                 })
                 ->rawColumns(['aksi','tanggalnya','jumlahnya','statusnya', 'deponya','wdnya'])
                ->make(true);
        }
        return view('admin.riwayatwd',compact('user'));
    }
}
