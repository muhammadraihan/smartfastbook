<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class SaldoKeluar extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'jenis_transaksi', 'kas_uuid', 'nominal', 'created_by', 'edited_by' 
    ];

    public function userCreate() {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function userEdit() {
        return $this->belongsTo(User::class, 'edited_by', 'uuid');
    }

    public function kas() {
        return $this->belongsTo(Kas_toko::class, 'kas_uuid', 'uuid');
    }
}
