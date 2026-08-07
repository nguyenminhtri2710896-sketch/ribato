<?php

namespace App\Services;

use App\Models\UserUploads;
use App\Utilities\FileUpload;
use Illuminate\Support\Facades\Validator;

class UploadService extends AbstractService
{
    public function __construct()
    {
    }


    public function image($arrParams = [])
    {
        $validated = Validator::make(
            $arrParams,
            [
                "upload_name" => "required",
            ],
            [
                "upload_name.required" => __("Vui lòng nhập tên upload ."),
            ]
        );

        if ($validated->errors()->messages()) {
            return $this->setStatusCode(414)->setMessage("")->setData([])->setErrors($validated->errors()->messages())->result();
        }

        $folder   = $arrParams["folder"] ?? "app/image/";

        $uploadFolder = base_path('static/uploads/' . $folder . date('Y') . "/" . date('m') . "/" . date('d') . "/");
        $uploadPath   = 'uploads/' . $folder . date('Y') . "/" . date('m') . "/" . date('d') . "/";
        if (!file_exists($uploadFolder)) {
            mkdir($uploadFolder, 0755, true);
        }

        if (!is_dir($uploadFolder)) {
            return $this->setStatusCode(404)->setMessage("")->setData([])->setErrors([
                [__("Folder  " . $folder . " không tồn tại vui lòng khởi tạo.")]
            ])->result();
        }
        $uploader = new FileUpload($arrParams["upload_name"]);
        // Handle the upload
        $result = $uploader->handleUpload($uploadFolder, ["jpg", "png", "gif", "jpeg"]);
        if (!$result) {
            return $this->setStatusCode(404)->setMessage($uploader->getErrorMsg())->setData([])->setErrors([
                [__("Có lỗi xảy ra khi upload :error.", ["error" => $uploader->getErrorMsg()])]
            ])->result();
        }
        $strFilePath = $uploadPath . $uploader->getSuccessResponse()["file_name"] ?? "";

        try {
            $img = \Image::make(base_path("static/$strFilePath"));
            $img->save(base_path("static/$strFilePath"), 85, 'jpg');
        } catch (\Exception $e) {
        }

        $codeUpload = md5(time() . "|" . rand(100000, 999999) . $strFilePath);

        $arrDataSuccess = $uploader->getSuccessResponse() + [
            'upload_path' => $uploadPath,
            "image_code" => $codeUpload,
            'image_base64' => base64_encode(file_get_contents($uploadFolder . $uploader->getSuccessResponse()["file_name"] ?? "")),
            'file_path' => $strFilePath
        ];
        return $this->setStatusCode(0)->setMessage(__("Upload thành công."))->setData($arrDataSuccess)->result();
    }

}