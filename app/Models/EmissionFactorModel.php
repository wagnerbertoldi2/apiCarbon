<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmissionFactorModel extends Model{
    use HasFactory;

    protected $table = 'emissionfactor';

    protected $fillable = [
        'id',
        'Name',
        'UnitId',
        'created_at',
        'updated_at',
        'text'
    ];

    protected $primaryKey='id';
}
