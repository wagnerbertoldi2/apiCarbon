<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmissionFactorModel extends Model{
    use HasFactory;

    protected $table = 'EmissionFactor';

    protected $fillable = [
        'id',
        'Name',
        'UnitId',
        'created_at',
        'updated_at'
    ];

    protected $primaryKey='id';
}
