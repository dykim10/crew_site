<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * public 스키마 기반 테이블 복구 마이그레이션.
 *
 * 원래 Supabase 프로젝트 생성 시 존재하던 crews / branches / groups / regions 테이블.
 * migrate:fresh 등으로 삭제된 경우를 대비해 CREATE TABLE IF NOT EXISTS 로 멱등성 보장.
 * 이후 마이그레이션(ALTER TABLE branches ...)이 정상 실행되도록 기반을 마련한다.
 */
return new class extends Migration
{
    public function up(): void
    {
        // crews
        DB::statement("
            CREATE TABLE IF NOT EXISTS public.crews (
                id          BIGSERIAL PRIMARY KEY,
                name        VARCHAR(100) NOT NULL,
                description TEXT,
                created_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // branches (기본 컬럼만 — admin_id/operator_id/status/updated_at 은 이후 마이그레이션에서 추가)
        DB::statement("
            CREATE TABLE IF NOT EXISTS public.branches (
                id         BIGSERIAL PRIMARY KEY,
                crew_id    BIGINT,
                name       VARCHAR(100) NOT NULL,
                created_at TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // groups
        DB::statement("
            CREATE TABLE IF NOT EXISTS public.groups (
                id         BIGSERIAL PRIMARY KEY,
                crew_id    BIGINT,
                branch_id  BIGINT,
                name       VARCHAR(100) NOT NULL,
                created_at TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // regions (crew.users_detail.region_id 외래키 대상)
        DB::statement("
            CREATE TABLE IF NOT EXISTS public.regions (
                id         BIGSERIAL PRIMARY KEY,
                name       VARCHAR(100) NOT NULL,
                created_at TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // 기본 데이터 — PAC-RUN 크루 1개 + 지부 4개 (외래키 참조 오류 방지)
        DB::statement("
            INSERT INTO public.crews (id, name) VALUES (1, 'PAC-RUN')
            ON CONFLICT (id) DO NOTHING
        ");

        $branches = [
            [2, 1, '반포'],
            [3, 1, '연대'],
            [4, 1, '인천'],
            [5, 1, '군포'],
        ];
        foreach ($branches as [$id, $crewId, $name]) {
            DB::statement("
                INSERT INTO public.branches (id, crew_id, name) VALUES ($id, $crewId, '$name')
                ON CONFLICT (id) DO NOTHING
            ");
        }

        $regions = [
            [1, '반포'],
            [2, '연대'],
            [3, '인천'],
            [4, '군포'],
        ];
        foreach ($regions as [$id, $name]) {
            DB::statement("
                INSERT INTO public.regions (id, name) VALUES ($id, '$name')
                ON CONFLICT (id) DO NOTHING
            ");
        }
    }

    public function down(): void
    {
        // 기반 테이블은 내려가지 않음 (연관 데이터 보호)
    }
};
