<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 이벤트 모델 (app/Models/Event.php)
 *
 * crew.events 테이블과 매핑. 이벤트 타입(A/B)에 따라 동작 방식이 다르다.
 *
 * [이벤트 타입]
 *   A타입 : 마라톤 대회 등 대외 참가 이벤트 — 관리자가 점수를 직접 부여
 *   B타입 : 크루 자체 모임·신청 이벤트
 *           form_schema(JSONB) 로 동적 입력 필드를 정의
 *           score_rules(JSONB) 로 점수 산정 규칙을 관리
 *
 * [계층 구조]
 *   parent_event_id : 상위 이벤트 참조 (서브 이벤트 지원)
 *   subEvents()     : 하위 이벤트 목록 (HasMany)
 *   parentEvent()   : 상위 이벤트 참조 (BelongsTo)
 *
 * [헬퍼 메서드]
 *   isActive()         : 현재 날짜 기준 진행 중 여부 (status=active + 기간 확인)
 *   isTypeA/B()        : 이벤트 타입 확인
 *   getFieldByKey(key) : form_schema 배열에서 특정 key 의 필드 검색
 */
class Event extends Model
{
    protected $table = 'crew.events';

    protected $fillable = [
        'crew_id', 'name', 'description', 'start_date', 'end_date',
        'target_km', 'status',
        'event_type', 'score_type', 'score_config',
        'parent_event_id', 'target_scope', 'generation',
        'form_schema', 'score_rules', 'max_participants', 'is_registration_open',
        'thumbnail_url', 'location', 'recruit_start_at', 'recruit_end_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date'           => 'date',
            'end_date'             => 'date',
            'recruit_start_at'     => 'datetime',
            'recruit_end_at'       => 'datetime',
            'form_schema'          => 'array',
            'score_rules'          => 'array',
            'score_config'         => 'array',
            'is_registration_open' => 'boolean',
        ];
    }

    public function scores()
    {
        return $this->hasMany(EventScore::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function subEvents()
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    public function parentEvent()
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function isTypeB(): bool { return $this->event_type === 'B'; }
    public function isTypeA(): bool { return $this->event_type === 'A'; }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    // 모집 중 여부 (recruit 기간 기준)
    public function isRecruitOpen(): bool
    {
        if (!$this->is_registration_open) return false;
        $now = now();
        if ($this->recruit_start_at && $now->lt($this->recruit_start_at)) return false;
        if ($this->recruit_end_at   && $now->gt($this->recruit_end_at))   return false;
        return $this->status === 'active';
    }

    // 참가 정원 초과 여부
    public function isCapacityFull(): bool
    {
        if (!$this->max_participants || $this->max_participants === 0) return false;
        return $this->registrations()->count() >= $this->max_participants;
    }

    // form_schema 배열에서 필드 한 개 찾기
    public function getFieldByKey(string $key): ?array
    {
        foreach ($this->form_schema ?? [] as $field) {
            if (($field['key'] ?? '') === $key) return $field;
        }
        return null;
    }

    // 기본 form_schema (성명+휴대폰 자동 포함) 생성 헬퍼
    public static function defaultFormSchema(): array
    {
        return [
            [
                'key'      => 'name',
                'label'    => '성명',
                'type'     => 'text',
                'required' => true,
                'data'     => ['encrypted' => false],
            ],
            [
                'key'      => 'phone',
                'label'    => '휴대폰번호',
                'type'     => 'text',
                'required' => true,
                'data'     => ['encrypted' => true, 'column' => 'phone_enc'],
            ],
        ];
    }
}
