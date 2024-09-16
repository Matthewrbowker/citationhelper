<?php

namespace App\Helpers\CS1;

use App\Helpers\BaseHelper;

class URL extends BaseHelper
{
    protected string $category = "CS1 errors: URL";

    public function parse(string $text, bool $external)
    {
        // Regex: https://stackoverflow.com/a/4713820
        // See also: https://gist.github.com/dperini/729294

        $scheme = "[a-z][a-z0-9+.-]*";
        $username = "([^:@/](:[^:@/])?@)?";
        $segment = "([a-z][a-z0-9-]*?[a-z0-9])";
        $domain = "({$segment}\.)*{$segment}";
        $segment = "([0|1][0-9]{2}|2([0-4][0-9]|5[0-5]))";
        $ipv4 = "({$segment}\.{$segment}\.{$segment}\.{$segment})";
        $block = "([a-f0-9]{0,4})";
        $rawIpv6 = "({$block}:){2,8}";
        $ipv4sub = "(::ffff:{$ipv4})";
        $ipv6 = "({$rawIpv6}|{$ipv4sub})";
        $host = "($domain|$ipv4|$ipv6)";
        $port = "(:[\d]{1,5})?";
        $path = "([^?;\#]*)?";
        $query = "(\?[^\#;]*)?";
        $anchor = "(\#.*)?";
        $regex = "#^{$scheme}://{$username}{$host}{$port}(/{$path}{$query}{$anchor}|)$#i";


        $citations = $this->findCitations($text);

        $output = [];

        foreach($citations as $citation) {
            if(isset($citation["parameters"]["url"]) && $citation["parameters"]["url"] != "") {

                if(!preg_match($regex, $citation["parameters"]["url"])) {
                    $output[] = [
                        "possibleFix" => "",
                        "content" => $citation["content"],
                        "errorString" => "url=",
                        "citation" => $citation,
                    ];
                }
            }

            if(isset($citation["parameters"]["archive-url"]) && $citation["parameters"]["archive-url"] != "") {
                if(!preg_match($regex, $citation["parameters"]["archive-url"])) {
                    $output[] = [
                        "possibleFix" => "",
                        "content" => $citation["content"],
                        "errorString" => "archive-url=",
                        "citation" => $citation,
                    ];
                }
            }

            if(isset($citation["parameters"]["archiveurl"]) && $citation["parameters"]["archiveurl"] != "") {
                if(!preg_match($regex, $citation["parameters"]["archiveurl"])) {
                    $output[] = [
                        "possibleFix" => "",
                        "content" => $citation["content"],
                        "errorString" => "archiveurl=",
                        "citation" => $citation,
                    ];
                }
            }
        }

        return $output;
    }
}
