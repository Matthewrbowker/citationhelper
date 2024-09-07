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

            $pageTitle = "<>";

            if($external && isset($parameters["url"])) {
                $url = $parameters["url"];
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
                    $pageTitle = $title;
                }
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
