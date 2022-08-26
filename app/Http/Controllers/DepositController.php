<?php

namespace App\Http\Controllers;

use Akaunting\Money\Money;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;

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
                    <li class='list-inline-item'>
                    <button type='button'  onclick='staffdel(" .
                        $data->id .
                        ")'   class='btn btn-danger btn-xs mb-1'>Reset</button>
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
                ->addColumn('wdnya', function ($data) {
                    if ($data->oSaldo) {
                        $btn = Money::IDR(0, true);
                    }else{
                        $btn = Money::IDR(0, true);

                    }
                    return $btn;
                })
                ->rawColumns(['aksi', 'deponya','wdnya'])
                ->make(true);
        }
        return view('admin.saldo');
    }
}
