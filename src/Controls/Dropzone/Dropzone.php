<?php

namespace SuperCMS\Controls\Dropzone;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Leaf\Controls\Common\FileUpload\SimpleFileUpload;
use Rhubarb\Leaf\Controls\Common\FileUpload\UploadedFileDetails;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\Equals;
use SuperCMS\Models\Product\ProductImage;

class Dropzone extends SimpleFileUpload
{
    /**
     * @var DropzoneModel $model
     */
    protected $model;

    /** @var Event */
    public $deleteImageEvent;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->deleteImageEvent = new Event();
    }

    protected function getViewClass()
    {
        return DropzoneView::class;
    }

    protected function parseRequest(WebRequest $request)
    {
        $fileData = $request->filesData;

        $response = null;

        if (!empty( $fileData ) && reset($fileData)) {
            $fileData = reset($fileData);
            if (is_array($fileData[ "name" ])) {
                foreach ($fileData[ "name" ] as $index => $name) {
                    if ($fileData[ "error" ][ $index ] == UPLOAD_ERR_OK) {
                        $realIndex = str_replace("_", "", $index);
                        $response = $this->fileUploadedEvent->raise(
                            new DropzoneUploadedFileDetails($name, $fileData[ "tmp_name" ][ $index ]),
                            $realIndex
                        );
                    }
                }
            } else {
                if ($fileData[ "error" ] == UPLOAD_ERR_OK) {
                    $response = $this->fileUploadedEvent->raise(
                        new DropzoneUploadedFileDetails($fileData[ "name" ], $fileData[ "tmp_name" ]),
                        $this->model->leafIndex
                    );
                }
            }
        }

        if ($request->post('_leafEventName') == 'FilesUploaded') {
            $this->model->fileUploadedEvent->raise(json_decode($request->post('_leafEventArguments')[ 0 ]));
        }

        if ($request->post('_leafEventName') == 'deleteImage') {
            $data = json_decode($request->post('_leafEventArguments')[0]);
            $this->deleteImageEvent->raise($data);
        }

        if ($request->post('_leafEventName') == 'imageReorder') {
            $data = json_decode($request->post('_leafEventArguments')[0]);
            $this->model->imageReorderEvent->raise($data);
        }

        return $response;
    }

    public $FilesUploadedEvent = null;

    protected function createModel()
    {
        $req = Request::current();

        $model = new DropzoneModel();

        if (isset( $req->urlPath )) {
            $model->postUrl = $req->urlPath;
        }

        $model->fileUploadedEvent->attachHandler(function ($path) {
            preg_match('/variation=[0-9]+/', $path, $matches);
            if (isset($matches[0])) {
                $id = str_replace('variation=', '', $matches[0]);
                $images = [];
                foreach (ProductImage::find(new Equals('ProductVariationID', $id))->addSort('Priority') as $image) {
                    $images[] = new DropzoneUploadedFileDetails($image->ProductImageID, $image->ImagePath, $image->UniqueIdentifier);
                }
                $this->setUploadedFiles($images);
                $this->reRender();
            }
        });

        $model->imageReorderEvent->attachHandler(function ($images) {
            $i = 0;
            foreach($images as $image) {
                try {
                    $imageObj = new ProductImage($image);
                    $imageObj->Priority = $i++;
                    $imageObj->save();
                } catch (RecordNotFoundException $ex) {
                }
            }
        });

        return $model;
    }

    /**
     * @param UploadedFileDetails[] $files
     */
    public function setUploadedFiles($files)
    {
        $this->model->uploadedFiles = $files;
    }

    public function setPostParams($param)
    {
        $req = Request::current();
        if (isset( $req->urlPath )) {
            $this->model->postUrl = $req->urlPath . $param;
        }
    }
}
