<?php

namespace App\Http\Controllers\Traits;

trait HasSkin
{
    /**
     * 로그인 사용자의 skin_select 값을 뷰 디렉토리명으로 변환.
     * '_skin_v1' → 'skin_v1', '_skin_v2' → 'skin_v2'
     */
    protected function skinDir(): string
    {
        $skin = auth()->user()?->detail?->skin_select ?? '_skin_v1';

        return ltrim($skin, '_'); // '_skin_v1' → 'skin_v1'
    }
}
