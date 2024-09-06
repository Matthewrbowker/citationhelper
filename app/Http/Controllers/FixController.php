<?php

namespace App\Http\Controllers;

use EasyWiki;

class FixController extends Controller
{
    public function index($style, $category, $articleTitle = null)
    {
        // Convert category to CamelCase
        $category = str_replace(' ', '', ucwords(str_replace('_', ' ', $category)));


        $helper = "App\\Helpers\\$style\\$category";

        if(!class_exists($helper)) {
            abort(404);
        }

        $helper = new $helper();

        // TODO: THIS
        $url = "https://en.wikipedia.org";
        $wiki = new EasyWiki("$url/w/api.php");

        if($articleTitle == null) {
            $members = $wiki->query([
                "action" => "query",
                "list" => "categorymembers",
                "cmtitle" => "Category:{$helper->getCategory()}",
                "cmlimit" => "1"
            ]);

            if(count($members["query"]["categorymembers"]) == 0
                || !isset($members["query"]["categorymembers"][0]["title"])) {
                return view("fixesNone", ["category" => $helper->getCategory()]);
            }

            $articleTitle = $members["query"]["categorymembers"][0]["title"];
        }

        $wikitext = $wiki->getWikitext($articleTitle);

        $output = $helper->parse($wikitext);

        $fixedWikitext = $helper->fix($wikitext, $output);

        $wikitext = explode("\n", $fixedWikitext);

        return view('fixes', ["articleTitle"=>$articleTitle, "output" => $output, "wikitext" => $wikitext]);
    }
}
