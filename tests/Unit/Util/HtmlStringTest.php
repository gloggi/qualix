<?php

namespace Tests\Util;

use App\Util\HtmlString;
use Tests\TestCase;

class HtmlStringTest extends TestCase {

    /** @var HtmlString */
    protected $htmlString;

    protected $unescaped = "<div>Two\nLines</div>";
    protected $escaped = "&lt;div&gt;Two\nLines&lt;/div&gt;";

    public function setUp(): void {
        parent::setUp();

        $this->htmlString = new HtmlString;

        $this->mock('translator', function ($mock) {
            $mock->shouldReceive('getFromJson')->andReturn($this->unescaped);
            $mock->shouldReceive('transChoice')->andReturn($this->unescaped);
        });
    }

    public function test_s_shouldNotEscape() {
        // given

        // when
        $result = $this->htmlString->s($this->unescaped)->__toString();

        // then
        $this->assertEquals($this->unescaped, $result);
    }

    public function test_s_shouldNotEscapeHtmlString() {
        // given
        $alreadySafe = (new HtmlString)->s($this->unescaped);

        // when
        $result = $this->htmlString->s($alreadySafe)->__toString();

        // then
        $this->assertEquals($this->unescaped, $result);
    }

    public function test_append_shouldNotEscape() {
        // given

        // when
        $result = $this->htmlString->append($this->unescaped)->__toString();

        // then
        $this->assertEquals($this->unescaped, $result);
    }

    public function test_append_shouldNotEscapeHtmlString() {
        // given
        $alreadySafe = (new HtmlString)->s($this->unescaped);

        // when
        $result = $this->htmlString->append($alreadySafe)->__toString();

        // then
        $this->assertEquals($this->unescaped, $result);
    }

    public function test___s_shouldNotEscape() {
        // given

        // when
        $result = $this->htmlString->__s($this->unescaped)->__toString();

        // then
        $this->assertEquals($this->unescaped, $result);
    }

    public function test_e_shouldEscape() {
        // given

        // when
        $result = $this->htmlString->e($this->unescaped)->__toString();

        // then
        $this->assertEquals($this->escaped, $result);
    }

    public function test_e_shouldNotEscapeHtmlStringAgain() {
        // given
        $alreadySafe = (new HtmlString)->s($this->unescaped);

        // when
        $result = $this->htmlString->e($alreadySafe)->__toString();

        // then
        $this->assertEquals("<div>Two\nLines</div>", $result);
    }

    public function test_appendEscaping_shouldEscape() {
        // given

        // when
        $result = $this->htmlString->appendEscaping($this->unescaped)->__toString();

        // then
        $this->assertEquals($this->escaped, $result);
    }

    public function test_appendEscaping_shouldNotEscapeHtmlStringAgain() {
        // given
        $alreadySafe = (new HtmlString)->s($this->unescaped);

        // when
        $result = $this->htmlString->appendEscaping($alreadySafe)->__toString();

        // then
        $this->assertEquals("<div>Two\nLines</div>", $result);
    }

    public function test___e_shouldEscape() {
        // given

        // when
        $result = $this->htmlString->__e($this->unescaped)->__toString();

        // then
        $this->assertEquals($this->escaped, $result);
    }

    public function test____shouldEscape() {
        // given

        // when
        $result = $this->htmlString->__($this->unescaped)->__toString();

        // then
        $this->assertEquals($this->escaped, $result);
    }

    public function test_trans_choice_e_shouldEscape() {
        // given

        // when
        $result = $this->htmlString->trans_choice_e($this->unescaped, 1)->__toString();

        // then
        $this->assertEquals($this->escaped, $result);
    }

    public function test_trans_choice_shouldEscape() {
        // given

        // when
        $result = $this->htmlString->trans_choice($this->unescaped, 1)->__toString();

        // then
        $this->assertEquals($this->escaped, $result);
    }

    public function test_replace_shouldEscapeReplacement() {
        // given
        $htmlString = (new HtmlString)->s('test:search123');
        $replace = '<div>replacement</div>';

        // when
        $result = $htmlString->replace(':search', $replace)->__toString();

        // then
        $this->assertEquals('test&lt;div&gt;replacement&lt;/div&gt;123', $result);
    }

    public function test_replace_shouldDoMultipleReplacements() {
        // given
        $htmlString = (new HtmlString)->s('test:search1:search23');
        $replace = 'replacement';

        // when
        $result = $htmlString->replace(':search', $replace)->__toString();

        // then
        $this->assertEquals('testreplacement1replacement23', $result);
    }

    public function test_replace_shouldNotDoOverlappingReplacement() {
        // given
        $htmlString = (new HtmlString)->s('test:searcharch123');
        $replace = 'replacement:se';

        // when
        $result = $htmlString->replace(':search', $replace)->__toString();

        // then
        $this->assertEquals('testreplacement:search123', $result);
    }

    public function test_replace_shouldNotEscapeHtmlStringReplacement() {
        // given
        $htmlString = (new HtmlString)->s('test:search123');
        $replace = (new HtmlString)->s('<div>replacement</div>');

        // when
        $result = $htmlString->replace(':search', $replace)->__toString();

        // then
        $this->assertEquals('test<div>replacement</div>123', $result);
    }
}
