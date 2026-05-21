<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

abstract class BaseImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, WithValidation
{
    protected array $errors  = [];
    protected int   $success = 0;
    protected int   $failed  = 0;

    /**
     * 청크 단위로 읽어 메모리 효율 확보 (대용량 엑셀 대응)
     */
    public function chunkSize(): int
    {
        return 200;
    }

    /**
     * DB 배치 insert 크기
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * 컬렉션 처리 — 서브클래스에서 구현
     * HeadingRow 가 적용되어 $collection 의 각 row 는 헤더명 기반 associative array
     */
    abstract public function collection(Collection $rows): void;

    /**
     * 유효성 검증 규칙 — 서브클래스에서 구현
     * 예: ['닉네임' => 'required|string|max:50']
     */
    abstract public function rules(): array;

    /**
     * 유효성 검증 오류 메시지 (선택 override)
     */
    public function customValidationMessages(): array
    {
        return [];
    }

    /**
     * 처리 결과 반환
     */
    public function getResult(): array
    {
        return [
            'success' => $this->success,
            'failed'  => $this->failed,
            'errors'  => $this->errors,
        ];
    }

    protected function addError(int $row, string $message): void
    {
        $this->errors[] = "{$row}행: {$message}";
        $this->failed++;
    }
}
