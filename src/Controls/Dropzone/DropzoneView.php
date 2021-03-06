<?php

namespace SuperCMS\Controls\Dropzone;

use Rhubarb\Leaf\Controls\Common\FileUpload\SimpleFileUploadView;
use SuperCMS\Deployment\SuperCmsDeploymentPackage;

class DropzoneView extends SimpleFileUploadView
{
    public $requiresContainerDiv = true;

    /** @var DropzoneModel */
    protected $model;

    protected function printViewContent()
    {
        ?>
        <input type="hidden" class="dropzone-post-url" value="<?=$this->model->postUrl?>">
        <div class="file-input gridly">
            <?php
            foreach($this->model->uploadedFiles as $file) {
                $this->printUploadedImage($file);
            }
            ?>
            <div class="fallback">
                <input name="file" type="file" multiple/>
            </div>
        </div>
        <?php
        print '<div class="dz-template" style="display: none">' . $this->getTemplate() . '</div>';
    }

    private function printUploadedImage(DropzoneUploadedFileDetails $image)
    {
        $imgName = pathinfo($image->tempFilename, PATHINFO_FILENAME);
        print <<<HTML
            <div class="dz-preview dz-processing dz-image-preview brick" data-id="{$image->id}">
                <div class="dz-image">
                    <img data-dz-thumbnail="" src="{$image->tempFilename}" style="width: 100%;"></div>
                <div class="dz-details">
                    <div class="dz-size">
                        <span data-dz-size="">
                            <strong>0.5</strong> MB</span>
                    </div>
                    <div class="dz-filename">
                        <span data-dz-name="">{$imgName}</span>
                    </div>
                </div>
                <div class="dz-success-mark">
                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                        <title>Check</title>
                        <defs></defs>
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"
                           sketch:type="MSPage">
                            <path
                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475"
                                fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
                        </g>
                    </svg>
                </div>
                <div class="dz-close-button"><i class="fa fa-times fa-4x" aria-hidden="true"></i></div>
            </div>
HTML;

    }

    public function getTemplate()
    {
        return <<<HTML
            <div class="dz-preview dz-processing dz-image-preview brick" data-id="">
                <div class="dz-image">
                    <img data-dz-thumbnail="" src="" style="width: 100%;"></div>
                <div class="dz-details">
                    <div class="dz-size">
                        <span data-dz-size="">
                            <strong>0.5</strong> MB</span>
                    </div>
                    <div class="dz-filename">
                        <span data-dz-name=""></span>
                    </div>
                </div>
                <div class="dz-success-mark">
                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg"
                         xmlns:xlink="http://www.w3.org/1999/xlink"
                         xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                        <title>Check</title>
                        <defs></defs>
                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"
                           sketch:type="MSPage">
                            <path
                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475"
                                fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
                        </g>
                    </svg>
                </div>
                <div class="dz-close-button"><i class="fa fa-times fa-4x" aria-hidden="true"></i></div>
            </div>
HTML;

    }

    public function getDeploymentPackage()
    {
        $package = new SuperCmsDeploymentPackage();

        $package->resourcesToDeploy[] = __DIR__ . '/../../../static/js/jquery.js';
        $package->resourcesToDeploy[] = __DIR__ . '/../../../static/js/dropzone.min.js';
        $package->resourcesToDeploy[] = __DIR__ . '/../../../static/js/gridly/jquery.gridly.js';
        $package->resourcesToDeploy[] = __DIR__ . '/' . $this->getViewBridgeName() . '.js';

        return $package;
    }

    protected function getViewBridgeName()
    {
        return 'DropzoneViewBridge';
    }
}
