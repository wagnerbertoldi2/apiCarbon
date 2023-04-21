<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CategoryModel extends Model{
    use HasFactory;

    protected $table = "Category";
    protected $primaryKey = 'id';
    protected $fillable = [
        'Name',
    ];

}
