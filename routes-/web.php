<?php

use Illuminate\Support\Facades\Route;
use App\Models\Video;
/*
Route::get('/', function(){
    $videos = Video::all();
    foreach($videos as $video){
        echo $video->title.'<br>';
echo $video->user->email.'<br>';
foreach($video->comments as $comment){
    echo $comment->body;
}
echo "<hr>";
}
    die();
    return view('welcome');
});
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('videos', '\App\Http\Controllers\VideoController');
/*Route::get('/home', array(
    'as' => 'home',
    'uses' => 'HomeController@index'
));*/

