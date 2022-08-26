<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\tipeInvest;
use Illuminate\Support\Facades\Validator;


class TipeInvestController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return Datatables::of(
                tipeInvest::get(),
            )
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $dataj = json_encode($data);
                    $btn =
                        "<ul class='list-inline mb-0'>
                        <li class='list-inline-item'>
                        <button type='button' data-toggle='modal' onclick='staffupd(" .
                                $dataj .
                                ")'   class='btn btn-success btn-xs mb-1'>Edit</button>
                        </li>
                            <li class='list-inline-item'>
                            <button type='button'  onclick='staffdel(" .
                                $data->id .
                                ")'   class='btn btn-danger btn-xs mb-1'>Hapus</button>
                            </li>
                     
                </ul>";
                    return $btn;
                })
                
                ->rawColumns(['aksi',])
                ->make(true);
        }
        return view('admin.data.tipe');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis' => ['required', 'string', 'max:255'],
            'bulan' => ['required', 'string', 'max:255'],
            'persenan' => ['required', 'string', 'max:255'],
       
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = tipeInvest::create([
            'bulan' => $request->bulan,
            'persenan' => $request->persenan,
            'periodik' => $request->jenis,
        ]);

        if ($data) {
        
            return 'success';
        }
    }
    public function destroy($id)
    {
        $data = tipeInvest::where('id',$id)->first();
        
        if ($data) {
         
            $data->delete();
            return 'success'; 
        }
    }
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis' => ['required', 'string', 'max:255'],
            'bulan' => ['required', 'string', 'max:255'],
            'persenan' => ['required', 'string', 'max:255'],
       
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = tipeInvest::where('id',$request->id)->first();
          $data->bulan = $request->bulan;
          $data->persenan = $request->persenan;
          $data->periodik = $request->jenis;
            $data->save();

        if ($data) {
        
            return 'success';
        }
    }
}
