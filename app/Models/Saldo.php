<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Saldo extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'no_ref', 'kas_uuid', 'nominal', 'keterangan', 'created_by', 'edited_by'
    ];

    public function userCreate(){
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function userEdit(){
        return $this->belongsTo(User::class, 'edited_by', 'uuid');
    }

    public function kas(){
        return $this->belongsTo(Kas_toko::class, 'kas_uuid', 'uuid');
    }
}
