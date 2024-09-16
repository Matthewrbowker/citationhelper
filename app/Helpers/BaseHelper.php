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
        // Temporarily replace {{!}} with {!} to avoid conflicts with the regex
        $wikitext = str_replace("{{!}}", "{!}", $wikitext);
        preg_match_all("#(\{\{cite ([\S\s]*)}}(.*))#imU", $wikitext, $matches);
        preg_match_all("#}}(.*)<#", $wikitext, $tagExtensions);

        $tagExtension = "";

        if(isset($tagExtensions[1][0])) {
            $tagExtension = $tagExtensions[1][0];
        }

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
                    // Put back the {{!}} that were replaced
                    $value = str_replace("{!}", "{{!}}", $value);
                }
                $parameters[$key] = $value;
            }

            $citation = [];
            $citation["content"] = $match;
            $citation["type"] = preg_split("#[|\s]#", $match)[1];
            $citation["parameters"] = $parameters;
            $citation["extension"] = $tagExtension;
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

        // Strip out fully empty citations
        $wikitext = str_replace("<ref></ref>", "", $wikitext);

        return $wikitext;
    }

    protected function extractPageTitle($url) {
        if(isset($parameters["url-status"]) && isset($parameters["archive-url"]) && $parameters["url-status"] == "dead") {
            $url = $parameters["archive-url"];
        }
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        // $output contains the output string
        $remotePageOutput = curl_exec($ch);

        $res = preg_match("/<title>(.*)<\/title>/siU", $remotePageOutput, $title_matches);
        if ($res) {
            // Clean up title: remove EOL's and excessive whitespace.
            $title = preg_replace('/\s+/', ' ', $title_matches[1]);
            $title = trim($title);
            if($title == "") {
                return "<>";
            }
            return $title;
        }

        return "<>";
    }
}
