<?php

namespace App\Http\Requests\Concerns;

use App\Models\JenisInventarisData;

/**
 * Trait untuk strip format rupiah dari input sebelum validasi.
 * Menangani field nilai_perolehan (fixed) dan dynamic currency fields.
 */
trait StripsCurrencyInput
{
    /**
     * Konversi string format rupiah (misal "1.500.000,59") ke angka murni ("1500000.59").
     */
    protected function stripRupiahFormat(string $raw): ?string
    {
        return $this->normalizeLocalizedNumberString($raw);
    }

    /**
     * Normalisasi angka lokal (format Indonesia/internasional) menjadi format titik desimal.
     * Contoh:
     * - 1.234,56 => 1234.56
     * - 1,234.56 => 1234.56
     * - 1234,56  => 1234.56
     * - 1234.56  => 1234.56
     */
    protected function normalizeLocalizedNumberString(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }

        $only = preg_replace('/[^0-9\.,\-]/u', '', (string) $raw);
        if ($only === null || $only === '' || $only === '-') {
            return null;
        }

        $isNegative = str_starts_with($only, '-');
        $core = str_replace('-', '', $only);
        if ($core === '') {
            return null;
        }

        $hasComma = str_contains($core, ',');
        $hasDot = str_contains($core, '.');

        if ($hasComma && $hasDot) {
            $lastComma = strrpos($core, ',');
            $lastDot = strrpos($core, '.');
            $decimalSeparator = ($lastComma !== false && $lastDot !== false && $lastComma > $lastDot) ? ',' : '.';
            $thousandSeparator = $decimalSeparator === ',' ? '.' : ',';

            $core = str_replace($thousandSeparator, '', $core);
            $core = str_replace($decimalSeparator, '.', $core);
        } elseif ($hasComma) {
            $parts = explode(',', $core);
            if (count($parts) > 2) {
                $core = implode('', $parts);
            } else {
                $left = $parts[0] ?? '';
                $right = $parts[1] ?? '';
                if ($right !== '' && strlen($right) !== 3) {
                    $core = $left . '.' . $right;
                } else {
                    $core = $left . $right;
                }
            }
        } elseif ($hasDot) {
            $parts = explode('.', $core);
            if (count($parts) > 2) {
                $core = implode('', $parts);
            } else {
                $left = $parts[0] ?? '';
                $right = $parts[1] ?? '';
                if ($right !== '' && strlen($right) !== 3) {
                    $core = $left . '.' . $right;
                } else {
                    $core = $left . $right;
                }
            }
        }

        if ($core === '' || $core === '.') {
            return null;
        }

        $normalized = $isNegative ? '-' . $core : $core;
        return is_numeric($normalized) ? $normalized : null;
    }

    /**
     * Normalisasi dynamic fields berbasis nama field (dipakai di create/store).
     */
    protected function normalizeDynamicNumericFieldsByName(int $jenisId, array $dynamic): array
    {
        if ($jenisId <= 0 || empty($dynamic)) {
            return $dynamic;
        }

        $fields = JenisInventarisData::where('jenis_inventaris_id', $jenisId)
            ->where('field_type', 'custom')
            ->get(['nama_field', 'label_field', 'tipe']);

        foreach ($fields as $field) {
            $nama = (string) $field->nama_field;
            if ($nama === '' || !array_key_exists($nama, $dynamic)) {
                continue;
            }

            $raw = $dynamic[$nama];
            if ($raw === null || (is_string($raw) && trim($raw) === '')) {
                continue;
            }

            $shouldNormalize = in_array($field->tipe, ['number', 'decimal_m2', 'decimal_km', 'decimal_m'], true)
                || JenisInventarisData::isCurrencyField(null, $field->tipe, $field->nama_field, $field->label_field);

            if (!$shouldNormalize) {
                continue;
            }

            $normalized = $this->normalizeLocalizedNumberString((string) $raw);
            $dynamic[$nama] = $normalized;
        }

        return $dynamic;
    }

    /**
     * Normalisasi dynamic fields berbasis ID field (dipakai di edit/update).
     */
    protected function normalizeDynamicNumericFieldsByFieldId(int $jenisId, array $fieldsById): array
    {
        if ($jenisId <= 0 || empty($fieldsById)) {
            return $fieldsById;
        }

        $fieldIds = collect(array_keys($fieldsById))
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0)
            ->values();

        if ($fieldIds->isEmpty()) {
            return $fieldsById;
        }

        $fields = JenisInventarisData::where('jenis_inventaris_id', $jenisId)
            ->where('field_type', 'custom')
            ->whereIn('id', $fieldIds)
            ->get(['id', 'nama_field', 'label_field', 'tipe']);

        foreach ($fields as $field) {
            $key = (string) $field->id;
            if (!array_key_exists($key, $fieldsById) && !array_key_exists($field->id, $fieldsById)) {
                continue;
            }

            $raw = $fieldsById[$key] ?? $fieldsById[$field->id] ?? null;
            if ($raw === null || (is_string($raw) && trim($raw) === '')) {
                continue;
            }

            $shouldNormalize = in_array($field->tipe, ['number', 'decimal_m2', 'decimal_km', 'decimal_m'], true)
                || JenisInventarisData::isCurrencyField(null, $field->tipe, $field->nama_field, $field->label_field);

            if (!$shouldNormalize) {
                continue;
            }

            $normalized = $this->normalizeLocalizedNumberString((string) $raw);
            if (array_key_exists($key, $fieldsById)) {
                $fieldsById[$key] = $normalized;
            } else {
                $fieldsById[$field->id] = $normalized;
            }
        }

        return $fieldsById;
    }

    /**
     * Strip format rupiah dari dynamic fields yang teridentifikasi sebagai currency.
     * Memerlukan jenis_inventaris_id di request.
     */
    protected function stripDynamicCurrencyFields(): void
    {
        $dynamic = $this->input('dynamic', []);
        if (empty($dynamic) || !$this->has('jenis_inventaris_id')) {
            return;
        }

        $jenisId = (int) $this->input('jenis_inventaris_id');
        $fields  = JenisInventarisData::where('jenis_inventaris_id', $jenisId)
            ->where('field_type', 'custom')
            ->whereIn('tipe', ['number', 'text', 'decimal_m2', 'decimal_km', 'decimal_m'])
            ->get();

        $changed = false;
        foreach ($fields as $field) {
            if (!JenisInventarisData::isCurrencyField(
                null,
                $field->tipe,
                $field->nama_field,
                $field->label_field
            )) {
                continue;
            }

            $nama = $field->nama_field;
            if (!isset($dynamic[$nama]) || (string)$dynamic[$nama] === '') {
                continue;
            }

            $stripped        = $this->stripRupiahFormat((string)$dynamic[$nama]);
            $dynamic[$nama]  = $stripped;
            $changed         = true;
        }

        if ($changed) {
            $this->merge(['dynamic' => $dynamic]);
        }
    }
}
