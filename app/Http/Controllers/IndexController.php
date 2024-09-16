<?php

namespace App\Http\Controllers;

use function Termwind\render;

class IndexController extends Controller
{
    public function index()
    {
        $dir = app_path('Helpers/CS1');
        $files = array_diff(scandir($dir), ['.', '..']);
        $categories = [];
        foreach($files as $file) {
            $file = str_replace('.php', '', $file);
            $class = "App\\Helpers\\CS1\\$file";
            $helper = new $class();
            $categories["CS1"][$file] = $helper->getCategory();
        }

        return view('index', ["categories" => $categories]);
    }
}
