<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EncryptImageController extends Controller
{ 
    public function encryptDataImage(Request $request){
        $data = $request->all();
        $newArr = [];
        foreach($data as $key => $val){
            $newArr[$key] = Crypt::encryptString($val);
        }
        return $newArr;
        
    } 

    public function decryptDataImage(Request $request){
        $data = $request->all();
        $newArr = [];
        foreach($data as $key => $val){
            $newArr[$key] = Crypt::decryptString($val);
        }
        return $newArr;
    }

}
