<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EncryptController extends Controller
{
    public function encryptData(Request $request){
        $data = $request->all();
        $newArr = [];
        foreach($data as $key => $val){
            $newArr[$key] = Crypt::encryptString($val);
        }
        return $newArr;
        
    }
    public function decryptData(Request $request){
        $data = $request->all();
        $newArr = [];
        foreach($data as $key => $val){
            $newArr[$key] = Crypt::decryptString($val);
        }
        $aa = json_encode($newArr,JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT); 
        return $aa;
    }
}
