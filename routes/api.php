<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\EncryptDecryptPDFController;
use App\Http\Controllers\Api\v1\EncryptController;
use App\Http\Controllers\Api\v1\EncryptImageController;
use App\Http\Controllers\Api\v1\TokenManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
/*
Route::post('encryptData', 'App\Http\Controllers\Api\v1\EncryptController@encryptData');
Route::post('decryptData', 'App\Http\Controllers\Api\v1\EncryptController@decryptData');

Route::post('encryptDataImage', 'App\Http\Controllers\Api\v1\EncryptController@encryptDataImage');
Route::post('decryptDataImage', 'App\Http\Controllers\Api\v1\EncryptController@decryptDataImage');
*/

Route::group(["prefix" => "v1"], function () {
    Route::post("generate-token", [TokenManagementController::class, "generateToken"])->middleware(VerifyToken::class);

    Route::group(['middleware' => 'checkauth'], function () {
        Route::post("encrypt-pdf", [EncryptDecryptPDFController::class, "encrypt_pdf"]);
        Route::post("decrypt-pdf", [EncryptDecryptPDFController::class, "decrypt_pdf"]);
        Route::get("delete-pdf", [EncryptDecryptPDFController::class, "delete_pdf"]);

        Route::post("encryptDataImage", [EncryptImageController::class, "encryptDataImage"]);
        Route::post("decryptDataImage", [EncryptImageController::class, "decryptDataImage"]);

        Route::post("encryptData", [EncryptController::class, "encryptData"]);
        Route::post("decryptData", [EncryptController::class, "decryptData"]);
    });
});
