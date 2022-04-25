<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Kas_toko extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'bank_uuid', 'name', 'no_rek', 'nama_rek', 'saldo', 'created_by', 'edited_by'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_uuid', 'uuid');
    }

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function userEdit()
    {
        return $this->belongsTo(User::class, 'edited_by', 'uuid');
    }
}
