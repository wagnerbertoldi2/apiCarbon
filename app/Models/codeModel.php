<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class codeModel extends Model
{
    use HasFactory;

    protected $table = 'coderesetpassword';
    protected $fillable = ['code', 'iduser'];
    protected $primaryKey = 'id';
}
