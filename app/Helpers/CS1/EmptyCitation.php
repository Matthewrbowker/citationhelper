<?php

namespace App\Helpers\CS1;

use App\Helpers\BaseHelper;

class EmptyCitation extends BaseHelper
{
    public string $category = "CS1 errors: empty citation";
    public function parse(string $text, bool $external): array
    {
        $output = [];
        $citations = $this->findCitations($text);

        foreach($citations as $citation) {
            $isEmpty = true;
            foreach($citation["parameters"] as $key=>$value) {
                if($value != "") {
                    $isEmpty = false;
                }
            }
            if(!$isEmpty) {
                continue;
            }

            $possibleFix = "";
            if($citation["extension"] != "") {
                $possibleFix = "{{cite web|url=$citation[extension]}}";
            }
            elseif(preg_match("#|url\s+http?#i", $citation["content"])) {
                $possibleFix = str_replace("|url", "|url=", $citation["content"]);
            }

            $output[] = [
                "possibleFix" => $possibleFix,
                "content" => $citation["content"],
                "errorString" => $citation["content"],
                "citation" => $citation,
            ];
        }

        return $output;
    }
}
