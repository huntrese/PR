<?php

use App\Livewire\Welcome;
use App\Events\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', Welcome::class);
Route::get('/chatroom', function(Request $request){
    dd($request);
    if(! auth()->user()){
        return redirect()->route("login");
    }
    return view('chatroom');
});



Route::post('/send-chat-message',function (Request $request){
    $request->validate([
        "text-value" => 'required',
    ]);
    
    if (!trim(strip_tags($request["text-value"]))){
        return response()->noContent();
    }
    $user = auth()->user()->toArray()['name'];
    // dd($user);

    broadcast(new ChatMessage(['username'=> $user,'text-value'=>$request['text-value']]))->toOthers();
    return response()->noContent();

});
// Route::get('/send-chat-message',function (Request $request){

//     $user = auth()->user()->toArray();
//     dd($user);

//     broadcast(new ChatMessage(['username'=>$user = Auth::user()->name,'text-value'=>$request['text-value']]))->toOthers();
//     return response()->noContent();

// });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::post('/lab-3/post', function(Request $request){
    // dd($request);

    return response()->json(json_encode($request->input("json_data")));
});
