<?php

namespace App\Helpers\CS1;

use App\Helpers\BaseHelper;

class MissingPipe extends BaseHelper
{
    public string $category = "CS1 errors: missing pipe";

    function parse($text): array
    {
        $output = [];
        $citations = $this->findCitations($text);

        foreach ($citations as $citation) {
            $content = $citation["content"];

            // TODO: Make this more robust by checking for valid parameter names.
            if(preg_match("#=([^|]*)=#i", $content, $matches)) {
                foreach($matches as $match) {
                    $possibleLocation = strrchr($match, " ");
                    $possibleLocation = trim($possibleLocation);
                    $possibleFix = "";
                    if($possibleLocation !== false) {
                        $possibleFix = str_replace($possibleLocation, "|$possibleLocation", $content);
                    }

                    $output[] = [
                        "possibleFix" => $possibleFix,
                        "errorString" => $matches[1],
                        "content" => $content,
                    ];
                }
            }
        }

        return $output;
    }
}
