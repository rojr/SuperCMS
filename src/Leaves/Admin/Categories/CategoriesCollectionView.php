<?php

namespace SuperCMS\Leaves\Admin\Categories;

use Rhubarb\Leaf\Table\Leaves\Table;
use SuperCMS\Models\Product\Category;
use SuperCMS\Views\SuperCMSCollectionView;

class CategoriesCollectionView extends SuperCMSCollectionView
{
    protected function getTitle()
    {
        return 'Categories';
    }

    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            $table = new Table(Category::find(), 50, 'Categories')
        );

        $table->addCssClassNames('table', 'table-striped', 'table-bordered', 'table-hover');
        $table->addCssClassNames('table', 'table-striped');

        $table->columns = [
            ' ' => '<img width="64" height="64" src="{Image}">',
            'Name',
            'Parent Category' => 'ParentCategory.Name',
            '' => '<a href="{CategoryID}/edit/" class="btn btn-default go">Edit</a>'
        ];
    }

    protected function printRightButtons()
    {
        print '<a href="add/" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> Add a Category</a>
<a href="hierarchy/" class="btn btn-primary"><i class="fa fa-cog" aria-hidden="true"></i> Edit hierarchy</a>';
    }

    public function printBody()
    {
        print $this->leaves['Categories'];
    }
}
