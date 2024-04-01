<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodModel extends Model{
    use HasFactory;

    protected $table = 'period';

    protected $fillable = [
        'Name',
        'InternalName',
        'created_at',
        'updated_at',
    ];

    protected $primaryKey = 'id';
}
