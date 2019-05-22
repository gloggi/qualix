<?php

namespace Tests;

abstract class TestCaseWithKurs extends TestCase
{
    protected $kursId;

    public function setUp(): void {
        parent::setUp();

        $this->kursId = $this->createKurs();
    }
}
