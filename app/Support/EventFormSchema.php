<?php

namespace App\Support;

use App\Models\Event;
use Illuminate\Support\Str;

/**
 * B타입 이벤트 form_schema ↔ Filament Builder 상태 변환.
 *
 * DB 저장 형식: Event::defaultFormSchema() + 커스텀 필드 (flat)
 * Builder 형식: [{ type, data: { key, label, ... } }, ...] (성명·휴대폰 제외)
 */
class EventFormSchema
{
    private const DEFAULT_KEYS = ['name', 'phone'];

    private const BUILDER_TYPES = ['text', 'textarea', 'radio', 'checkbox'];

    /** DB form_schema → Filament Builder blocks (수정 화면 fill) */
    public static function toBuilderBlocks(?array $schema): array
    {
        $blocks = [];

        foreach ($schema ?? [] as $field) {
            $storedKey = (string) ($field['key'] ?? '');
            if (in_array($storedKey, self::DEFAULT_KEYS, true)) {
                continue;
            }

            $type = $field['type'] ?? 'text';
            if (! in_array($type, self::BUILDER_TYPES, true)) {
                $type = 'text';
            }

            $data = is_array($field['data'] ?? null) ? $field['data'] : [];
            $data['label'] = $data['label'] ?? $field['label'] ?? '항목';
            $data['key'] = strtolower((string) ($data['key'] ?? $storedKey ?: Str::snake(Str::ascii($data['label']))));
            $data['required'] = (bool) ($field['required'] ?? $data['required'] ?? false);

            if (isset($data['encrypted'])) {
                $data['encrypted'] = (bool) $data['encrypted'];
            }

            if (in_array($type, ['radio', 'checkbox'], true)) {
                $data['options'] = self::normalizeOptions($data['options'] ?? $field['options'] ?? []);
            }

            $blocks[] = [
                'type' => $type,
                'data' => $data,
            ];
        }

        return $blocks;
    }

    /** Filament Builder blocks → DB form_schema (create/update save) */
    public static function fromBuilderBlocks(array $blocks, ?array $existingSchema = null): array
    {
        $existingByLabel = [];
        $existingByKey = [];

        foreach ($existingSchema ?? [] as $field) {
            if (isset($field['label'], $field['key'])) {
                $existingByLabel[$field['label']] = $field['key'];
            }
            if (isset($field['key'])) {
                $existingByKey[$field['key']] = $field['key'];
                $dataKey = $field['data']['key'] ?? null;
                if ($dataKey) {
                    $existingByKey[$dataKey] = $field['key'];
                }
            }
        }

        $custom = [];

        foreach ($blocks as $block) {
            $type = $block['type'] ?? 'text';
            if (! in_array($type, self::BUILDER_TYPES, true)) {
                $type = 'text';
            }

            $bdata = is_array($block['data'] ?? null) ? $block['data'] : [];
            $label = $bdata['label'] ?? '항목';
            $dataKey = strtolower((string) ($bdata['key'] ?? ''));

            $storedKey = $dataKey !== ''
                ? ($existingByKey[$dataKey] ?? $dataKey)
                : ($existingByLabel[$label]
                    ?? (Str::snake(Str::ascii($label)) ?: ('field_' . count($custom))));

            if (in_array($storedKey, self::DEFAULT_KEYS, true)) {
                $storedKey = 'custom_' . $storedKey;
            }

            if (in_array($type, ['radio', 'checkbox'], true)) {
                $bdata['options'] = self::normalizeOptions($bdata['options'] ?? []);
            }

            $custom[] = [
                'key'      => $storedKey,
                'label'    => $label,
                'type'     => $type,
                'required' => (bool) ($bdata['required'] ?? false),
                'data'     => $bdata,
            ];
        }

        return array_merge(Event::defaultFormSchema(), $custom);
    }

    /** @param  array<int, mixed>  $options */
    private static function normalizeOptions(array $options): array
    {
        $normalized = [];

        foreach ($options as $option) {
            if (is_array($option)) {
                $value = $option['value'] ?? null;
            } else {
                $value = $option;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $normalized[] = ['value' => (string) $value];
        }

        return $normalized;
    }
}
