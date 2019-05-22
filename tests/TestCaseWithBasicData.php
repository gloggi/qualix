<?php

namespace Tests;

abstract class TestCaseWithBasicData extends TestCaseWithKurs
{
    protected $tnId;
    protected $blockId;

    public function setUp(): void {
        parent::setUp();

        $this->tnId = $this->createTN('Pflock');
        $this->blockId = $this->createBlock('Block 1', '1.1', '01.01.2019', null);
    }
}
