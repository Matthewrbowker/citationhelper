<?php

namespace App\Helpers\CS1;

use App\Helpers\BaseHelper;

class ExtraTextVolume extends BaseHelper
{
    public string $category = "CS1 errors: extra text: volume";

    public function parse($text, $external): array
    {
        $output = [];
        $citations = $this->findCitations($text);

        foreach ($citations as $citation) {
            if(isset($citation["parameters"]["volume"])) {
                $content = $citation["content"];
                $volume = $citation["parameters"]["volume"];

                // Roman numerals
                if(
                    preg_match("#^M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$#", $volume) ||
                    preg_match("#^\d+$#", $volume)
                ) {
                    continue;
                }
                $output[] = [
                    "possibleFix" => preg_replace("#№\s+#i", "№", $content),
                    "content" => $content,
                    "errorString" => $content,
                    "citation" => $citation,
                ];
            }
        }

        return $output;
    }
}
