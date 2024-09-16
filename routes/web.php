<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\IndexController::class, "index"])->name("index");

Route::get("/fix/{style}/{category}/{articleTitle?}", [App\Http\Controllers\FixController::class, "index"])->where("articleTitle", ".*")->name("fix.index");
