<?php

namespace App\Helpers;

abstract class BaseHelper
{
    protected string $category = "";

    public abstract function parse(string $text, bool $external);

    public function getCategory(): string
    {
        return $this->category;
    }

    protected function findCitations($wikitext): array
    {
        $citations = [];
        $wikitext = str_replace("{{!}}", "{!}", $wikitext);
        preg_match_all("#(\{\{cite ([\S\s]*)}})#imU", $wikitext, $matches);

        foreach($matches[0] as $match) {
            $parameters = [];
            $citationParameters = explode("|", $match);
            $citationParameters = array_slice($citationParameters, 1);
            foreach($citationParameters as $i=>$value) {
                if(
                    (str_contains($value, "[[") && !str_contains($value, "]]"))
                ) {
                    foreach(array_slice($citationParameters,$i+1) as $j=>$contents) {
                        if(str_contains($citationParameters[$i], "]]")) {
                            break;
                        }
                        $citationParameters[$i] = "$citationParameters[$i]|$contents";
                        unset($citationParameters[$i+1]);
                    }
                }
                if(
                    (str_contains($value, "{{") && !str_contains($value, "}}"))
                ) {
                    foreach(array_slice($citationParameters,$i+1) as $j=>$contents) {
                        if(str_contains($citationParameters[$i], "}}")) {
                            break;
                        }
                        $citationParameters[$i] = "$citationParameters[$i]|$contents";
                        unset($citationParameters[$i+1]);
                    }
                }
            }

            foreach($citationParameters as $parameter) {
                $parameter = trim($parameter);
                $parameterData = explode("=", $parameter, 2);
                $key = trim($parameterData[0]);
                $value = "";
                if(isset($parameterData[1])) {
                    $value = trim($parameterData[1]);
                    $value = str_replace("}}", "", $value);
                    $value = str_replace("{!}", "{{!}}", $value);
                }
                $parameters[$key] = $value;
            }

            $citation = [];
            $citation["content"] = $match;
            $citation["type"] = preg_split("#[|\s]#", $match)[1];
            $citation["parameters"] = $parameters;
            $citations[] = $citation;
        }

        return $citations;
    }

    public function fix($wikitext, $output) {
        foreach($output as $error) {
            $content = $error["content"];
            $possibleFix = $error["possibleFix"];
            $wikitext = str_replace($content, $possibleFix, $wikitext);
        }

        return $wikitext;
    }
}
