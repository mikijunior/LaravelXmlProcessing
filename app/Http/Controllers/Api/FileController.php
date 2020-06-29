<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileStoreRequest;
use App\Http\Services\FileService;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;

class FileController extends Controller
{
    /**
     * @var FileService
     */
    private $service;

    public function __construct(FileService $service)
    {
        $this->service = $service;
    }

    public function storeFileData(FileStoreRequest $request): JsonResponse
    {
        $file = $request->file('file');

        if (!$file instanceof UploadedFile) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }

        try {
            return response()->json($this->service->importFileData($file));
        } catch (InvalidArgumentException $exception) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }
    }
}
