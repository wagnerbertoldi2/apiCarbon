<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table= 'users';
    protected $fillable = [
        'FirstName',
        'LastName',
        'idprofile',
        'CPF',
        'RG',
        'CNPJ',
        'email',
        'password'
    ];

    protected $hidden = [
        'Password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier(){
        return $this->getKey();
    }

    public function getJWTCustomClaims(){
        return [];
    }

    public function getAuthPassword(){
        return $this->password;
    }

    public function getEmailForPasswordReset(){
        return $this->email;
    }

    public function getRememberToken(){
        return $this->remember_token;
    }

    public function setRememberToken($value){
        $this->remember_token = $value;
    }

    public function getRememberTokenName(){
        return 'remember_token';
    }

    public function setEmailAttribute($value){
        $this->attributes['email'] = strtolower($value);
    }

    public function setCpfAttribute($value){
        $this->attributes['CPF'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function setRgAttribute($value){
        $this->attributes['RG'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function setCnpjAttribute($value){
        $this->attributes['CNPJ'] = preg_replace('/[^0-9]/', '', $value);
    }

    public function scopeByEmail($query, $email){
        return $query->where('email', strtolower($email));
    }

    public function scopeByCpf($query, $cpf){
        return $query->where('CPF', preg_replace('/[^0-9]/', '', $cpf));
    }

    public function scopeByRg($query, $rg){
        return $query->where('RG', preg_replace('/[^0-9]/', '', $rg));
    }

    public function scopeByCnpj($query, $cnpj){
        return $query->where('CNPJ', preg_replace('/[^0-9]/', '', $cnpj));
    }
}
