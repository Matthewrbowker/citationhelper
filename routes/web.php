<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/fix/{style}/{category}/{articleTitle?}", [App\Http\Controllers\FixController::class, "index"])->where("articleTitle", ".*")->name("fix.index");
