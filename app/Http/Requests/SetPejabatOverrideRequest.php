<?php

namespace App\Http\Requests;

use App\Models\ExportLog;
use Illuminate\Foundation\Http\FormRequest;

class SetPejabatOverrideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ExportLog::class);
    }

    public function rules(): array
    {
        return [
            'jenis_inventaris_id' => ['required', 'integer', 'exists:jenis_inventaris,id'],
            'pejabat_override'    => ['required', 'array'],
        ];
    }
}
