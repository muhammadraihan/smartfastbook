<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class EWallet extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'no_ref', 'customer', 'no_hp', 'pemilik', 'ewallet', 'payment_methode', 'bank_uuid', 'biaya_admin', 'nominal', 'keterangan', 'created_by', 'edited_by'
    ];

    public function userCreate(){
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function userEdit(){
        return $this->belongsTo(User::class, 'edited_by', 'uuid');
    }

    public function bank(){
        return $this->belongsTo(Bank::class, 'bank_uuid', 'uuid');
    }

    public function payment(){
        return $this->belongsTo(Payment::class, 'ewallet', 'uuid');
    }
}
