<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Transaksi extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'no_ref', 'customer', 'bank_tujuan', 'no_rek', 'nama_rekening','bank_uuid', 'biaya_admin', 'admin_bank', 'keterangan', 'nominal', 'created_by', 'edited_by'
    ];

    public function userCreate() {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function userEdit() {
        return $this->belongsTo(User::class, 'edited_by', 'uuid');
    }

    public function tujuan() {
        return $this->belongsTo(Bank::class, 'bank_tujuan', 'uuid');
    }

    public function bank() {
        return $this->belongsTo(Bank::class, 'bank_uuid', 'uuid');
    }
}
