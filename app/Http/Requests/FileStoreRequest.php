<?php

namespace App\Http\Requests;

use App\Rules\FileSizeRule;
use Illuminate\Foundation\Http\FormRequest;

class FileStoreRequest extends FormRequest
{
    public const AVAILABLE_MIME_TYPES = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel'
    ];

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'mimes:xls,xlsx',
                'mimetypes:' . implode(',', self::AVAILABLE_MIME_TYPES),
                new FileSizeRule(),
            ],
        ];
    }
}
