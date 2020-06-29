<?php

namespace App\Http\Services;

use App\Imports\ItemImport;
use App\Models\Category;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;

class FileService
{
    public const IN_STOCK_STRING_MATCH = 'есть';
    public const KEY_PARENT_CATEGORY = 0;
    public const KEY_SECOND_CATEGORY = 1;
    public const KEY_CHILD_CATEGORY = 2;
    public const KEY_BRAND = 3;
    public const KEY_NAME = 4;
    public const KEY_ARTICLE = 5;
    public const KEY_DESCRIPTION = 6;
    public const KEY_PRICE = 7;
    public const KEY_WARRANTY = 8;
    public const KEY_IN_STOCK = 9;
    public const LAST_COLUMN_KEY = 10;

    /**
     * @var int[]
     */
    private $savedCategories = [];

    /**
     * @param UploadedFile $file
     * @return array
     */
    public function importFileData(UploadedFile $file): array
    {
        $fileRealPath = $file->getRealPath();

        if (!$fileRealPath) {
            throw new InvalidArgumentException();
        }

        $lazyCollection = LazyCollection::make(
            (new ItemImport)
                ->toArray($fileRealPath, null, $file->getExtension())[0]
        );

        $entriesCount = $lazyCollection->count();

        $uniqueCount = 0;

        $iterableEntries = $lazyCollection
            ->unique()
            ->chunk(700);

        foreach ($iterableEntries as $entries) {
            $uniqueValues = [];

            foreach ($entries as $index => $entry) {
                $key = (empty($entry[self::KEY_PARENT_CATEGORY]) && !empty($entry[self::LAST_COLUMN_KEY]))
                    ? self::KEY_PARENT_CATEGORY + 1
                    : self::KEY_PARENT_CATEGORY;

                $categoryId = $this->processCategories(
                    $entry[$key],
                    $entry[$key + self::KEY_SECOND_CATEGORY],
                    $entry[$key + self::KEY_CHILD_CATEGORY]
                );

                $uniqueValues[] = [
                    'category_id' => $categoryId,
                    'brand' => (string)$entry[$key + self::KEY_BRAND],
                    'name' => (string)$entry[$key + self::KEY_NAME],
                    'article' => (string)$entry[$key + self::KEY_ARTICLE],
                    'description' => (string)$entry[$key + self::KEY_DESCRIPTION],
                    'price' => (int)$entry[$key + self::KEY_PRICE] ?? 0,
                    'warranty' => (int)$entry[$key + self::KEY_WARRANTY] ?? 0,
                    'in_stock' => strpos($entry[$key + self::KEY_IN_STOCK], self::IN_STOCK_STRING_MATCH) !== false,
                ];

                $uniqueCount++;
            }

            DB::table('items')->insert($uniqueValues);
        }

        return [
            'imported' => $uniqueCount,
            'duplicatedRows' => $entriesCount - $uniqueCount,
        ];
    }

    private function processCategories(?string $mainCategory, ?string $secondCategory, string $childCategory): int
    {
        if ($mainCategory) {
            $parentCategoryKey = $this->setCategory($mainCategory);
        }

        if ($secondCategory) {
            $parentCategoryKey = $this->setCategory($secondCategory, $parentCategoryKey ?? null);
        }

        return $this->setCategory($childCategory, $parentCategoryKey ?? null);
    }

    private function setCategory(string $title, ?int $parentId = null): int
    {
        $key = $title . $parentId;
        if (isset($this->savedCategories[$key])) {
            return $this->savedCategories[$key];
        }

        $savedId = Category::query()->create([
            'title' => $title,
            'parent_id' => $parentId,
        ])->getKey();

        $this->savedCategories[$key] = $savedId;

        return $savedId;
    }
}
