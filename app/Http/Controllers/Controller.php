<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;


abstract class Controller
{
    public function coreValidator($request, $data) {
        $validator = Validator::make($request->all(), $data);

        if($validator->fails()){
            return false;
        }else{
            return true;
        }
    }
}
