<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Rekening extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'bank_uuid', 'no_rekening', 'nama', 'created_by', 'edited_by'
    ];

    public function userCreate(){
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function userEdit(){
        return $this->belongsTo(User::class, 'edited_by', 'uuid');
    }

    public function Bank(){
        return $this->belongsTo(Bank::class, 'bank_uuid', 'uuid');
    }
}
