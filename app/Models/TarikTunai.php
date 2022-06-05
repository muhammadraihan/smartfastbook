<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class TarikTunai extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'no_ref', 'customer', 'no_kartu', 'bank_uuid', 'biaya_admin', 'admin_bank', 'keterangan', 'nominal', 'created_by', 'edited_by', 'jenis_pembayaran'
    ];

    public function userCreate() {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function userEdit() {
        return $this->belongsTo(User::class, 'edited_by', 'uuid');
    }

    public function kas() {
        return $this->belongsTo(Kas_toko::class, 'jenis_pembayaran', 'uuid');
    }

    public function bank() {
        return $this->belongsTo(Bank::class, 'bank_uuid', 'uuid');
    }
}
