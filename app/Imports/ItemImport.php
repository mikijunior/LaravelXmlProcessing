<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ItemImport implements WithStartRow
{
    use Importable;

    public function startRow(): int
    {
        return 2;
    }
}
