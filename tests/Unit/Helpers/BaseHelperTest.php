<?php

namespace Tests\Unit\Helpers;

use App\Helpers\BaseHelper;
use Tests\TestCase;

class BaseHelperTest extends TestCase
{
    public function testFindCitations()
    {
        $object = new class extends BaseHelper {
            public function parse($text)
            {
                throw new Exception("Not tested, I just need to be here...");
            }
        };

        $helper =new \ReflectionClass(get_class($object));
        $method = $helper->getMethod("findCitations");
        $method->setAccessible(true);

        $wikitext = "<ref>content</ref>";
        $citations = $method->invokeArgs($object, [$wikitext]);
        $this->assertEquals(1, count($citations));
        $this->assertEquals("<ref>content</ref>", $citations[0]["full"]);
        $this->assertEquals("", $citations[0]["tagExtension"]);
        $this->assertEquals("content", $citations[0]["content"]);

        $wikitext = "<ref name='contentReference'>content</ref>";
        $citations = $method->invokeArgs($object, [$wikitext]);
        $this->assertEquals(1, count($citations));
        $this->assertEquals("<ref name='contentReference'>content</ref>", $citations[0]["full"]);
        $this->assertEquals("name='contentReference'", $citations[0]["tagExtension"]);
        $this->assertEquals("content", $citations[0]["content"]);

        $wikitext = "<ref group='lower-alpha'>content</ref>";
        $citations = $method->invokeArgs($object, [$wikitext]);
        $this->assertEquals(1, count($citations));
        $this->assertEquals("<ref group='lower-alpha'>content</ref>", $citations[0]["full"]);
        $this->assertEquals("group='lower-alpha'", $citations[0]["tagExtension"]);
        $this->assertEquals("content", $citations[0]["content"]);
    }
}
