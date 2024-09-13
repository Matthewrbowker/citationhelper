<?php

namespace App\Helpers\CS1;

use App\Helpers\BaseHelper;

class MissingTitle extends BaseHelper
{
    public string $category = "CS1 errors: missing title";

    private function checkParameter($parameters, $key) {
        return isset($parameters[$key]) && $parameters[$key] != "";
    }

    public function parse($text, $external): array
    {
        $output = [];
        $citations = $this->findCitations($text);
        $parameter = "title";

        foreach($citations as $citation) {
            $content = $citation["content"];
            $parameters = $citation["parameters"];
            if($citation["type"] == "episode") {
                if($this->checkParameter($parameters, "series")) {
                    continue;
                }
                $parameter = "series";
            }
            /*else if($citation["type"] == "encylopedia") {
                if(
                    $this->checkParameter($parameters, "encyclopedia") &&
                    (
                        $this->checkParameter($parameters, "entry") || )
                if(preg_match($this->generateRegexForListOfAliases(["entry", "title"]), $content)) {
                    continue;
                }
            }*/
            else if(
                $this->checkParameter($parameters, "title") ||
                $this->checkParameter($parameters, "trans-title") ||
                $this->checkParameter($parameters, "script-title")
            ) {
                continue;
            }
            //}


            if($external && isset($parameters["url"])) {
                $pageTitle = $this->extractPageTitle($parameters["url"]);
            }

            if(array_key_exists($parameter, $parameters)) {
                $possibleFix = preg_replace("#$parameter\s?=\s?[|}]#i", "$parameter=$pageTitle |", $content);
            }
            else {
                $possibleFix = substr_replace($content, " |$parameter=$pageTitle", -2, 0);
            }

            $output[] = [
                "possibleFix" => $possibleFix,
                "errorString" => $content,
                "content" => $content,
                "citation" => $citation
            ];
        }

        return $output;
    }
}
