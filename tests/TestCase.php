<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }
}

// Backport assertJsonPath from Laravel 6.x to 5.8
TestResponse::macro('assertJsonPath', function (string $path, $expected) {
    $actual = data_get($this->json(), $path);
    \PHPUnit\Framework\Assert::assertEquals(
        $expected,
        $actual,
        "Failed asserting that JSON path '{$path}' equals expected value."
    );
    return $this;
});
