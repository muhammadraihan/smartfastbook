<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Customer extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'name', 'telephone', 'alamat', 'piutang', 'status'
    ];
}
