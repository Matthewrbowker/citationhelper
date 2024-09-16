<?php

namespace Tests\Unit\Helpers\CS1;

use App\Helpers\CS1\EmptyCitation;
use Tests\TestCase;

class EmptyCitationTest extends TestCase
{
    public function testConvertsEmptyCitationWithURLOnTheEnd() {
        $this->markTestIncomplete();
        $citation = "<ref>{{cite web}}https://example.com</ref>";
        $expectedText = "<ref>{{cite web|url=https://example.com}}</ref>";

        $helper = new EmptyCitation();
        $output = $helper->parse($citation, false);
        $this->assertCount(1, $output);
        $this->assertEquals($expectedText, $output[0]["possibleFix"]);
    }

}
