<?php

namespace Tests\API\V1;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helper;
use Tests\TestCase;

abstract class V1TestCase extends TestCase
{
    use Helper, WithFaker, ApiResponse;

    protected function setUp(): void
    {
        ini_set('memory_limit', -1);

        parent::setUp();
        // set your headers here
        $this->withHeaders([
            'App-Secret' => config('xcode.auth.v1.web.secret'),
            'Platform' => 'web',
        ]);
    }

    protected function prepareUrlForRequest($uri): string
    {
        return 'api/v1/' . $uri;
    }
}
