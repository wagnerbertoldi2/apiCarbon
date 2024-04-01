<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function Psy\debug;
use Illuminate\Support\Facades\DB;

class RandomNumberController{
    public static function generateRandomNumber($user){
        $userId= $user->id;
        $code= mt_rand(100000, 999999);

        DB::table('coderesetpassword')->insert([
            "code" => $code,
            "iduser" => $userId,
            "created_at"=> date("Y-m-d H:i:s")
        ]);

        return $code;
    }
}
