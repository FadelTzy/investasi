<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pengajuanInvestasi extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function oTipe()
    {
        return $this->hasOne(tipeInvest::class, 'id', 'tipe_investasi');
    }
    public function oRiwayat()
    {
        return $this->hasMany(RiwayatInvest::class, 'id_pengajuan', 'id')->where('status','=', 1);
    }
}
