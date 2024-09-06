<?php

namespace App\Helpers;

abstract class BaseHelper
{
    protected string $category = "";

    public abstract function parse($text);

    public function getCategory(): string
    {
        return $this->category;
    }

    protected function findCitations($wikitext): array
    {
        $lines = explode("\n", $wikitext);
        $citations = [];
        foreach ($lines as $line) {
            preg_match("#(<ref(.*)>(.*)</ref>)#i", $line, $matches);
            if (count($matches) > 0) {
                $citation = [];
                $citation["full"] = $matches[1];
                $citation["tagExtension"] = trim($matches[2]);
                $citation["content"] = $matches[3];
                $citations[] = $citation;
            }
        }
        return $citations;
    }

    public function fix($wikitext, $output) {
        $citations = $this->findCitations($wikitext);
        foreach ($output as $error) {
            $content = $error["content"];
            $possibleFix = $error["possibleFix"];
            foreach ($citations as $citation) {
                if($citation["content"] == $content) {
                    $wikitext = str_replace($citation["full"], "<ref>$possibleFix</ref>", $wikitext);
                }
            }
        }
        return $wikitext;
    }
}
