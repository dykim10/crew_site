<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_home_route_is_registered(): void
    {
        $this->assertTrue(Route::has('home'));
    }

    /**
     * 공개 홈 페이지 접근 확인 (pdo_sqlite + in-memory DB 필요).
     */
    public function test_home_page_renders(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite extension required for HTTP feature tests.');
        }

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PAC', false);
    }
}
