<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmissionModel extends Model{
    use HasFactory;

    protected $table = 'emission';

    protected $fillable = [
        'Attachment',
        'Amount',
        'EmissionSourceId',
        'Month',
        'Year',
        'Semester',
        'created_at',
        'updated_at'
    ];

    protected $primaryKey = 'id';

    //foriegn key with emission source
    public function emissionSource(){
        return $this->belongsTo(EmissionSourceModel::class, 'EmissionSourceId');
    }
}
