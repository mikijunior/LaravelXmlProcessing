<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class FileSizeRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (is_uploaded_file($value)) {
            /** @var UploadedFile $value */
            return $value->getSize() <= $this->getMaxAllowedFileSize();
        }

        return true;
    }

    private function getMaxAllowedFileSize(): int
    {
        return min(
            $this->convertPHPSizeToBytes(ini_get('post_max_size') ?: ''),
            $this->convertPHPSizeToBytes(ini_get('upload_max_filesize') ?: '')
        );
    }

    /**
     * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
     *
     * @param string $size
     * @return integer The value in bytes
     */
    private function convertPHPSizeToBytes(string $size): int
    {
        $sizeSuffix = strtoupper(substr($size, -1));

        if (!in_array($sizeSuffix, ['P', 'T', 'G', 'M', 'K'])) {
            return (int)$size;
        }

        $sizeValue = (int)substr($size, 0, -1);

        switch ($sizeSuffix) {
            case 'P':
                $sizeValue *= 1024;
            case 'T':
                $sizeValue *= 1024;
            case 'G':
                $sizeValue *= 1024;
            case 'M':
                $sizeValue *= 1024;
            case 'K':
                $sizeValue *= 1024;
                break;
        }

        return (int)$sizeValue;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute is invalid. Max allowed file size: ' . $this->getMaxAllowedFileSize();
    }
}
