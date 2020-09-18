<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected array $filesToDelete;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesToDelete = [];
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        foreach ($this->filesToDelete as $filename) {
            @unlink($filename);
        }
    }
}
