<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileStoreRequest;
use App\Http\Services\FileService;

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

    public function storeFileData(FileStoreRequest $request)
    {
        $file = $request->file('file');

        $responseArray = $this->service->storeFile($file);
    }
}
