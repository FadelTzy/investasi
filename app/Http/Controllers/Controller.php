<?php

namespace App\Http\Controllers;

use App\Models\ktp;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Deposit;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function profil()
    {
        return view('admin.profil');
    }
    public function storeprofil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users,email,' . $request->id],
        ]);

        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return redirect()
                ->back()
                ->with($data);
        }
        $user = User::findorfail($request->id);
        if ($user) {
            $user->name = $request->nama;
            $user->no = $request->no;

            $user->email = $request->email;
            if ($request->pass != '' || $request->pass != null) {
                $user->password = Hash::make($request->pass);
            }
            $user->save();
            return redirect()
                ->back()
                ->with('message', 'success');
        }
    }
    public function investoraktif($d)
    {
        $data = User::where('id',$d)->first();
        $data->is_active = 1;
        $data->save();
        if ($data) {
            Deposit::create([
                'id_user' => $d,
                'total_depo' => 0,
                'total_wd' => 0
            ]);
            return 'success';
        }
    }
    public function index()
    {
        return view('admin.dashboard');
    }

    public function investorstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'nomor' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:20', 'min:3'],
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = User::create([
            'nama' => $request->nama,
            'role' => 3,
            'username' => $request->username,
            'status' => 1,
            'nomor' => $request->nomor,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($data) {
            ktp::create([
                'id_user' => $data->id,
                'nama' => $data->nama,
            ]);
            return 'success';
        }
    }
    public function investorktpstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'id' => ['required', 'string', 'max:255'],
            'nik' => [ 'max:255'],
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        // return $request->all();
        $data = ktp::where('id_user', $request->id)->first();
        if (request()->file('foto')) {
            $gmbr = request()->file('foto');
            if ($data->foto) {
                $path = '/file/foto/' . $data->foto;
                if (file_exists(public_path() . $path)) {
                    unlink(public_path() . $path);
                }
            }
            $nama_file = str_replace(' ', '_', time() . '_' . $gmbr->getClientOriginalName());
            $tujuan_upload = 'file/foto/';
            $gmbr->move($tujuan_upload, $nama_file);

            $data->foto = $nama_file;
        }
        $data->nama = $request->nama;
        $data->nik = $request->nik;
        $data->tempat_lahir = $request->tempatlahir;
        $data->tanggal_lahir = $request->tanggallahir;
        $data->jk = $request->jk;
        $data->alamat = $request->alamat;
        $data->rt = $request->rt;
        $data->rw = $request->rw;
        $data->kel_des = $request->keldes;
        $data->kecamatan = $request->kecamatan;
        $data->agama = $request->agama;
        $data->pekerjaan = $request->pekerjaan;
        $data->status_kawin = $request->statusp;
        $data->warganegara = $request->kewarganegaraan;
        $data->save();
        if ($data) {
            return 'success';
        }
    }
    public function adminstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:20', 'min:2'],
        ]);
        if ($validator->fails()) {
            $data = ['status' => 'error', 'data' => $validator->errors()];
            return $data;
        }
        $data = User::create([
            'nama' => $request->nama,
            'role' => $request->role,
            'username' => $request->username,
            'status' => 1,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($data) {
            return 'success';
        }
    }
  

    public function admin()
    {
        if (request()->ajax()) {
            return Datatables::of(
                User::where('role', 1)
                    ->orWhere('role', 2)
                    ->get(),
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
                ->addColumn('rolenya', function ($data) {
                    if ($data->role == 1) {
                        $btn = "<ul class='list-inline mb-0'>
                <li class='list-inline-item'>
                <span type='button' class='badge badge-primary btn-xs mb-1'>Super Admin</span>
                </li>
                   
                     
                </ul>";
                    } elseif ($data->role == 2) {
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
        return view('admin.admin');
    }
    public function investor()
    {
        if (request()->ajax()) {
            return Datatables::of(
                User::with('oKtp')
                    ->where('role', 3)
                    ->get(),
            )
                ->addIndexColumn()
                ->addColumn('aksi', function ($data) {
                    $dataj = json_encode($data);
                    $btn =
                        "<ul class='list-inline mb-0'>
                <li class='list-inline-item'>
                <button type='button' data-toggle='modal' onclick='staffupd(" .
                        $dataj .
                        ")'   class='btn btn-sm btn-info btn-xs mb-1'>Edit</button>
                </li>
                    <li class='list-inline-item'>
                    <button type='button'  onclick='staffdel(" .
                        $data->id .
                        ")'   class='btn btn-sm btn-danger btn-xs mb-1'>Hapus</button>
                    </li>
                    <li class='list-inline-item'>
                    <button type='button'  onclick='stafdata(" .
                        $dataj .
                        ")'   class='btn btn-sm btn-primary btn-xs mb-1'>Data</button>
                    </li>
                    <li class='list-inline-item'>
                    <button type='button'  onclick='aktif(" .
                        $data->id .
                        ")'   class='btn btn-sm btn-success btn-xs mb-1'><i class='fas fa-check'></i></button>
                    </li>
                </ul>";
                    return $btn;
                })
                ->addColumn('statusnya', function ($data) {
                    if ($data->is_active == 1) {
                        $btn = "<ul class='list-inline mb-0'>
                <li class='list-inline-item'>
                <span type='button' class='badge badge-success btn-xs mb-1'>Verified</span>
                </li>
                   
                     
                </ul>";
                    } else {
                        # code...

                        $btn = "<ul class='list-inline mb-0'>
                <li class='list-inline-item'>
                <span type='button' class='badge badge-warning btn-xs mb-1'>Unverified</span>
                </li>
                   
                     
                </ul>";
                    }

                    return $btn;
                })
                ->rawColumns(['aksi', 'statusnya'])
                ->make(true);
        }
        return view('admin.investor');
    }
    public function investorhapus($id)
    {
        $data = User::where('id',$id)->first();
        
        if ($data) {
            $checking = ktp::where('id_user',$id)->first();
            if ($checking) {
                if ($checking->foto) {
                    $path = '/file/foto/' . $checking->foto;
                    if (file_exists(public_path() . $path)) {
                        unlink(public_path() . $path);
                    }
                }
                $checking->delete();
            }
          
            $data->delete();
            return 'success'; 
        }
    }
}
