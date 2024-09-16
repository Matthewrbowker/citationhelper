<?php

namespace App\Helpers\CS1;

use App\Helpers\BaseHelper;

class ArchivedCopyAsTitle extends BaseHelper
{
    protected string $category = "CS1 maint: archived copy as title";
    public function parse(string $text, bool $external)
    {
        $citations = $this->findCitations($text);
        $output = [];
        foreach($citations as $citation) {
            if(isset($citation["parameters"]["title"]) && $citation["parameters"]["title"] == "Archived copy") {
                $title = "<>";
                if(isset($citation["parameters"]["archiveurl"])) {
                    $url = $citation["parameters"]["archiveurl"];
                }
                elseif(isset($citation["parameters"]["archive-url"])) {
                    $url = $citation["parameters"]["archive-url"];
                }
                elseif(isset($citation["parameters"]["url"])) {
                    $url = $citation["parameters"]["url"];
                }

                if($external && isset($url)) {
                    $title = $this->extractPageTitle($url);
                }

                $possibleFix = preg_replace("#title\s?=\s?Archived copy#", "title=" . $title, $citation["content"]);

                $output[] = [
                    "possibleFix" => $possibleFix,
                    "content" => $citation["content"],
                    "errorString" => "Archived copy",
                    "citation" => $citation,
                ];
            }
        }
        return $output;
    }
}
