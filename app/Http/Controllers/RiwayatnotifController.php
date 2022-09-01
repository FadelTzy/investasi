<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\riwayatnotif;

class RiwayatnotifController extends Controller
{
    public function notif()
    {
        if (request()->ajax()) {
            return Datatables::of(riwayatnotif::get())
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $dataj = json_encode($data);
                    if ($data->jns_notif == 1) {
                        $btn =
                            "<ul class='list-inline mb-0'>
                            <li class='list-inline-item'>
                            <button type='button'  onclick='staffdel(" .
                                $data->id .
                                ")'   class='btn btn-danger btn-xs mb-1'>Hapus</button>
                            </li>
                </ul>";
                    }

                    return $btn;
                })
                ->addColumn('rolenya', function ($data) {
                    if ($data->jns_notif == 1) {
                        $btn = "<ul class='list-inline mb-0'>
                <li class='list-inline-item'>
                <span type='button' class='badge badge-primary btn-xs mb-1'>Registrasi User</span>
                </li>
                   
                     
                </ul>";
                    } elseif ($data->jns_notif == 2) {
                        # code...

                        $btn = "<ul class='list-inline mb-0'>
                <li class='list-inline-item'>
                <span type='button' class='badge badge-warning btn-xs mb-1'>Admin</span>
                </li>
                   
                     
                </ul>";
                    }

                    return $btn;
                })
                ->rawColumns(['aksi', 'rolenya'])
                ->make(true);
        }
        return view('admin.riwayatnotif');
    }
    public function notifhapus($id)
    {
        riwayatnotif::where('id',$id)->delete();
        return 'success';
    }
}
