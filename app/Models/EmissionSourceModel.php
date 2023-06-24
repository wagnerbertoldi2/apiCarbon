<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmissionSourceModel extends Model{
    use HasFactory;

    protected $table = 'emissionsource';

    protected $fillable= [
        'Name',
        'EmissionFactorId',
        'PropertyId',
        'PeriodId'
    ];

    protected $primaryKey = 'id';

    //foreign key with PropertyModel
    public function property(){
        return $this->belongsTo(PropertyModel::class);
    }

    //foreign key with EmissionFactorModel
    public function emissionFactor(){
        return $this->belongsTo(EmissionFactorModel::class);
    }

    //foreign key with PeriodModel
    public function period(){
        return $this->belongsTo(PeriodModel::class);
    }
}
