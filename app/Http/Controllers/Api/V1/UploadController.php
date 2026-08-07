<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\UploadService;
use Illuminate\Http\Request;

class UploadController extends BaseController
{
    protected $uploadService;
    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function image(Request $request)
    {
        $arrParams = $request->all();
        return response()->json($this->uploadService->image(["upload_name" => "file_image"] + $arrParams));
    }
}
