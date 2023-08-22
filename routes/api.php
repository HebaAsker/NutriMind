<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Chat\ChatController;
use App\Http\Controllers\API\Patient\PatientController;


//--------------------------------Routes for patient app features--------------------------------//
Route::middleware('auth:patient')->group(function(){
    Route::get('/doctors',[PatientController::class,'index']);
    Route::get('/doctor/{id}',[PatientController::class,'show']);
});
//------------------------------End Routes for patient app features------------------------------//




//--------------------------Routes for Chat between doctor and patient--------------------------//
Route::middleware('auth')->group(function(){
    Route::post('/create-chat',[ChatController::class,'create']);
    Route::get('/show-chat-messages',[ChatController::class,'showMessages']);
    Route::get('/show-chats',[ChatController::class,'showChats']);
});


//--------------------------End Routes for Chat between doctor and patient--------------------------//








