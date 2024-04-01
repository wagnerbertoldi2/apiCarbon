<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitModel extends Model{
    use HasFactory;

    protected $table = 'unit';
    protected $primaryKey = 'id';
    protected $fillable = ['Name', 'InternalName', 'created_at', 'updated_at'];
}
