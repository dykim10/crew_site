<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * 로그인 페이지 접근 확인.
     * GET / → 로그인 유도 리다이렉트(302) 또는 로그인 페이지(200) 반환.
     */
    public function test_root_redirects_or_renders(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
