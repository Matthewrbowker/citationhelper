<?php

namespace App\Helpers\CS1;

use App\Helpers\BaseHelper;

class ExternalLinks extends BaseHelper
{
    protected string $category = "CS1 errors: external links";

    public function parse(string $text, bool $external)
    {
        $citations = $this->findCitations($text);

        $output = [];

        foreach($citations as $citation) {
            foreach($citation["parameters"] as $key => $value) {
                if(in_array($key, ["url", "archive-url"])) {
                    continue;
                }

                if(preg_match("#(\[?https?://.*]?)#", $value, $matches)) {
                    if($key == "title" && $external) {
                        $pageTitle = $this->extractPageTitle($matches[1]);
                        $pageTitle = htmlspecialchars_decode($pageTitle);
                        $value = str_replace("#", "\#", $value);
                        $possibleFix = preg_replace("#$key\s?=\s?$value#", "$key=$pageTitle", $citation["content"]);
                    }
                    else {
                        $regexValue = str_replace("[", "\[", $matches[1]);
                        $possibleFix = preg_replace("#\|\s?$key\s?=\s?$regexValue\s?#i", "", $citation["content"]);
                    }


                    $errorString = $matches[1];

                    $output[] = [
                        "possibleFix" => $possibleFix,
                        "content" => $citation["content"],
                        "errorString" => $errorString,
                        "citation" => $citation,
                    ];
                }
            }
        }

        return $output;
    }
}
