<?php

namespace App\Models;

use App\Services\CryptoService;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * 사용자 모델 (app/Models/User.php)
 *
 * public.users 테이블과 매핑. Supabase 의 기존 테이블이므로 DROP/CREATE 금지,
 * 컬럼 추가 시 Schema::table + hasColumn 으로 멱등성을 보장한다.
 *
 * [개인정보 암호화 구조]
 *   email_hash : SHA-256 해시 → 로그인 조회용 (역복호화 불가)
 *   email_enc  : AES 암호화 → getEmailAttribute() 가상 속성으로 투명하게 복호화
 *   name_enc   : AES 암호화 → getNameAttribute()  가상 속성으로 투명하게 복호화
 *   복호화는 CryptoService → CORE API 에 위임 (AES 키를 PHP 측에 보관하지 않음)
 *
 * [역할 계층]
 *   super_admin  : 전체 관리자
 *   region_admin : 지역 관리자 (super_admin 권한 포함)
 *   operator     : 운영자 (super_admin + region_admin 권한 포함)
 *   member       : 일반 구성원
 *   → isAdmin() / isSuperAdmin() / isRegionAdmin() / isOperator() 헬퍼 메서드 제공
 *
 * [Filament 관리자 패널]
 *   FilamentUser / HasName 인터페이스 구현
 *   canAccessPanel(): super_admin / region_admin / operator 만 관리자 패널 접근 허용
 */
class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nickname',
        'email_hash',
        'email_enc',
        'name_enc',
        'password',
        'crew_id',
        'branch_id',
        'group_id',
        'role',
        'is_beta',
        'invite_code',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getEmailAttribute(): ?string
    {
        if (isset($this->attributes['email'])) {
            return $this->attributes['email'];
        }
        return $this->email_enc ? app(CryptoService::class)->decrypt($this->email_enc) : null;
    }

    public function getNameAttribute(): ?string
    {
        if (isset($this->attributes['name'])) {
            return $this->attributes['name'];
        }
        return $this->name_enc ? app(CryptoService::class)->decrypt($this->name_enc) : null;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_beta'           => 'boolean',
            'password'          => 'hashed',
        ];
    }

    public function getFilamentName(): string
    {
        return $this->nickname ?? 'User #' . $this->id;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['super_admin', 'region_admin', 'operator']);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'region_admin', 'operator']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isRegionAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'region_admin']);
    }

    public function isOperator(): bool
    {
        return in_array($this->role, ['super_admin', 'region_admin', 'operator']);
    }
}
