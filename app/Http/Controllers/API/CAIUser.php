<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\ktp;
use Illuminate\Support\Facades\Hash;


class CAIUser extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(new UserResource(false, 'Gagal Registrasi', 'Data tidak Lengkap'));
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            $filename = time() . '_' . $file->getClientOriginalName();

            // File extension
            $extension = $file->getClientOriginalExtension();

            // File upload location
            $location = 'file/ktp';

            // Upload file
            $file->move($location, $filename);

            // File path
            $filepath = url('file/ktp' . $filename);

            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'username' => $request->username,
                'role' => 3,
                'is_active' => 0,
                'status' => 0,
                'kode' => 0,
                'password' => Hash::make($request->password),
            ]);

            $ktps = ktp::create([
                'id_user' => $user->id,
                'nama' => $request->nama,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jk' => $request->jk,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kel_des' => $request->kel_des,
                'kecamatan' => $request->kecamatan,
                'provinsi' => $request->provinsi,
                'kabupaten_kota' => $request->kabupaten_kota,
                'agama' => $request->agama,
                'goldar' => $request->goldar,
                'pekerjaan' => $request->pekerjaan,
                'nik' => $request->nik,
                'foto' => $filepath,
                'status_kawin' => $request->status_kawin,
                'warganegara' => $request->warganegara,
            ]);


            return response()->json(new UserResource(true, 'Berhasil Registrasi', $user, $ktps));
        } else {
            return response()->json(new UserResource(false, 'Gagal Foto', 'Tidak ada Foto'));
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user =
            User::where('email', $request->email)->get()->first();

        // dd(Hash::make($request->password));

        // $nuser = count($user);

        if ($user && password_verify($request->password, $user->password)) {
            return response()->json(new UserResource(true, 'Berhasil Login', $user));
        } else {
            return response()->json(new UserResource(false, 'Gagal Login', 'email atau password salah'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
