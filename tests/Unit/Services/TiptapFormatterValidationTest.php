<?php

namespace Tests\Unit\Services;

use App\Services\TiptapFormatter;
use Tests\TestCase;

class TiptapFormatterValidationTest extends TestCase {

    public function test_isValid_shouldAcceptValidDocument_withNoContent() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'paragraph']]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertTrue($result);
    }

    public function test_isValid_shouldAcceptValidDocument_withSomeTextContent() {
        // given
        $input = ['type' => 'doc', 'content' => [
            ['type' => 'paragraph', 'content' => [['type' => 'heading', 'attrs' => ['level' => 5], 'text' => 'hello']]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
        ]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertTrue($result);
    }

    public function test_isValid_shouldAcceptValidDocument_withSomeObservationContent() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'observation', 'attrs' => ['id' => 17]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([17]));

        // then
        $this->assertTrue($result);
    }

    public function test_isValid_shouldAcceptValidDocument_withSomeRequirementContent() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'requirement', 'attrs' => ['id' => 99, 'passed' => null]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([99]), collect([]));

        // then
        $this->assertTrue($result);
    }

    public function test_isValid_shouldAcceptValidDocument_withMixedContent() {
        // given
        $input = ['type' => 'doc', 'content' => [
            ['type' => 'paragraph'],
            ['type' => 'paragraph', 'content' => [['type' => 'heading', 'attrs' => ['level' => 5], 'text' => 'hello']]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'hello']]],
            ['type' => 'requirement', 'attrs' => ['id' => 99, 'passed' => null]],
            ['type' => 'observation', 'attrs' => ['id' => 17]],
        ]];

        // when
        $result = TiptapFormatter::isValid($input, collect([99]), collect([17]));

        // then
        $this->assertTrue($result);
    }

    public function test_isValid_shouldAcceptValidDocument_withUnknownAttributes() {
        // given
        $input = ['type' => 'doc', 'something' => 'yes', 'content' => [
            ['type' => 'paragraph', 'what-the-hell' => true, 'content' => [['type' => 'heading', 'why' => 'because', 'attrs' => ['level' => 5, 'bold' => -3], 'text' => 'hello']]],
            ['type' => 'paragraph', 'well' => function() {}, 'content' => [['type' => 'text', 'then' => null, 'text' => 'hello']]],
            ['type' => 'requirement', 'Bari' => 'approved', 'attrs' => ['id' => 99, 'passed' => null, 'sparkle' => false]],
            ['type' => 'observation', 'participant' => '123', 'attrs' => ['id' => 17, 'name' => 'Lindo']],
        ]];

        // when
        $result = TiptapFormatter::isValid($input, collect([99]), collect([17]));

        // then
        $this->assertTrue($result);
    }

    public function test_isValid_shouldNotAcceptInvalidDocumentDataType() {
        // given
        $input = (object)['type' => 'doc', 'content' => [['type' => 'paragraph']]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withoutType() {
        // given
        $input = ['content' => [['type' => 'paragraph']]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withWrongType() {
        // given
        $input = ['type' => 'document', 'content' => [['type' => 'paragraph']]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withoutContentArray() {
        // given
        $input = ['type' => 'doc'];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withWrongContentListType() {
        // given
        $input = ['type' => 'doc', 'content' => (object)['type' => 'paragraph']];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withWrongContentType() {
        // given
        $input = ['type' => 'doc', 'content' => ['type' => 'paragraph']];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withNodeWithoutType() {
        // given
        $input = ['type' => 'doc', 'content' => [['attrs' => ['id' => 3]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withNodeWithInvalidType() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 3, 'attrs' => ['id' => 3]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withObservationNodeWithoutAttrs() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'observation']]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([4]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withObservationNodeWithInvalidAttrs() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'observation', 'attrs' => true]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([4]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withObservationNodeWithoutId() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'observation', 'attrs' => ['test' => 'foo']]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([4]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withObservationNodeWithNonexistentId() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'observation', 'attrs' => ['id' => 3]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([]), collect([4]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withRequirementNodeWithoutAttrs() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'requirement']]];

        // when
        $result = TiptapFormatter::isValid($input, collect([12]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withRequirementNodeWithInvalidAttrs() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'requirement', 'attrs' => true]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([12]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withRequirementNodeWithoutId() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'requirement', 'attrs' => ['passed' => null]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([12]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withRequirementNodeWithNonexistentId() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'requirement', 'attrs' => ['id' => 3, 'passed' => null]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([12]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withRequirementNodeWithoutPassed() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'requirement', 'attrs' => ['id' => 12]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([12]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withRequirementNodeWithInvalidPassed() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'requirement', 'attrs' => ['id' => 12, 'passed' => 3.5]]]];

        // when
        $result = TiptapFormatter::isValid($input, collect([12]), collect([]));

        // then
        $this->assertFalse($result);
    }

    public function test_isValid_shouldNotAcceptDocument_withUnserializableNode() {
        // given
        $input = ['type' => 'doc', 'content' => [['type' => 'requirement', 'attrs' => ['id' => 12, 'passed' => 1]]]];
        $input['content'][] = &$input;

        // when
        $result = TiptapFormatter::isValid($input, collect([12]), collect([]));

        // then
        $this->assertFalse($result);
    }
}
