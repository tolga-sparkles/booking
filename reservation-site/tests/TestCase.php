<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // This explicitly disables the CSRF middleware for all tests,
        // resolving the persistent 419 errors in the test environment.
        $this->withoutMiddleware(
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class
        );
    }
}
