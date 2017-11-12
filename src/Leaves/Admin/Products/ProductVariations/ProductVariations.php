<?php

namespace SuperCMS\Leaves\Admin\Products\ProductVariations;

use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Stem\Filters\Equals;
use SuperCMS\Models\Product\Product;

class ProductVariations extends Leaf
{
    /**
     * @var ProductVariationsModel $model
     */
    protected $model;
    protected $product = null;

    public function __construct(Product $product, $name = null, $initialiseModelBeforeView = null)
    {
        $this->product = $product;

        parent::__construct($name, $initialiseModelBeforeView);
    }

    protected function createModel()
    {
        return new ProductVariationsModel();
    }

    protected function getViewClass()
    {
        return ProductVariationsView::class;
    }

    protected function onModelCreated()
    {
        $this->model->product = $this->product;

        if (!$this->model->selectedVariationId) {
            $this->model->selectedVariationId = $this->model->getVariations()[ 0 ]->UniqueIdentifier;
        }

        $this->model->changeVariationEvent->attachHandler(function ($oldId, $newId) {
            $data = new \stdClass();


            return $data;
        });

        $this->model->deleteVariationEvent->attachHandler(function ($id) {
            $variations = $this->model->getVariations()->filter(new Equals('ProductVariationID', $id));

            if ($variations->count()) {
                $variations[0]->delete();
            }
        });
    }
}
