<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyModel extends Model{
    use HasFactory;

    protected $table = 'property';
    protected $primaryKey = 'id';
    protected $fillable = [
        'Name',
        'Registration',
        'CEP',
        'City',
        'regionId',
        'Number',
        'Complement',
        'NumberOfPeoples',
        'Address',
        'UF',
        'UserId',
        'CategoryId',
        'latitude',
        'longitude',
        'created_at',
        'updated_at'
    ];

    public function user(){
        return $this->belongsTo('App\Models\UserModel');
    }

    public function category(){
        return $this->belongsTo('App\Models\CategoryModel');
    }
}
