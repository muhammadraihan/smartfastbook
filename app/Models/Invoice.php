<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Invoice extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'no_invoice', 'perusahaan_uuid', 'tujuan', 'alamat', 'deskripsi', 'jumlah' ,'harga_satuan', 'total' ,'pajak', 'sub_total', 'catatan', 'tanggal_invoice', 'jatuh_tempo', 'created_by', 'edited_by'
    ];

    public function userCreate() {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function userEdit() {
        return $this->belongsTo(User::class, 'edited_by' ,'uuid');
    }

    public function perusahaan() {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_uuid', 'uuid');
    }
}
