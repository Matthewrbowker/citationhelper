<?php

namespace Tests\Unit\Helpers\CS1;

use App\Helpers\CS1\MissingPipe;
use Tests\TestCase;

class MissingPipeTest extends TestCase
{
    public function testParse()
    {
        $expectedText = "{{cite web |url=https://example.com |title=Example citation |accessdate=19 December 2024}}";
        $content = "{{cite web |url=https://example.com |title=Example citation |accessdate=19 December 2024}}";

        $helper = new MissingPipe();
        $text = "<ref>$content</ref>";
        $output = $helper->parse($text);
        $this->assertCount(0, $output);


        $content = "{{cite web |url=https://example.com title=Example citation |accessdate=19 December 2024}}";
        $text = "<ref>$content</ref>";
        $output = $helper->parse($text);
        $this->assertCount(1, $output);
        $this->assertEquals($content, $output[0]["content"]);
        $this->assertEquals("https://example.com title", $output[0]["errorString"]);
        $this->assertEquals($expectedText, $output[0]["possibleFix"]);

        $content = "{{cite web |url=https://example.com |title=Example citation accessdate=19 December 2024}}";
        $text = "<ref>$content</ref>";
        $output = $helper->parse($text);
        $this->assertCount(1, $output);
        $this->assertEquals($content, $output[0]["content"]);
        $this->assertEquals("Example citation accessdate", $output[0]["errorString"]);
        $this->assertEquals($expectedText, $output[0]["possibleFix"]);
    }

}
