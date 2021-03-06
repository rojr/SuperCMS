<?php

namespace SuperCMS\Models\Product;

use Rhubarb\Crown\Request\Request;
use Rhubarb\Leaf\Controls\Common\FileUpload\UploadedFileDetails;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use Rhubarb\Stem\Filters\AndGroup;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Filters\Filter;
use Rhubarb\Stem\Filters\OneOf;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Repositories\MySql\Schema\MySqlModelSchema;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\BooleanColumn;
use Rhubarb\Stem\Schema\Columns\ForeignKeyColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;

/**
 *
 *
 * @property int $CategoryID Repository field
 * @property int $ParentCategoryID Repository field
 * @property string $Name Repository field
 * @property string $SeoSafeName Repository field
 * @property string $Image Repository field
 * @property bool $Visible Repository field
 * @property-read Product[]|\Rhubarb\Stem\Collections\RepositoryCollection $Products Relationship
 * @property-read Category[]|\Rhubarb\Stem\Collections\RepositoryCollection $ChildCategories Relationship
 * @property-read Category $ParentCategory Relationship
 * @property-read mixed $PublicUrl {@link getPublicUrl()}
 * @property-read mixed $ParentCategoryIDs {@link getParentCategoryIDs()}
 * @property-read mixed $ThumbnailUrl {@link getThumbnailUrl()}
 */
class Category extends Model
{
    use SetUniqueNameTrait;

    const VERSION = 1;

    protected function createSchema()
    {
        $model = new MySqlModelSchema('tblCategory');

        $model->addColumn(
            new AutoIncrementColumn('CategoryID'),
            new ForeignKeyColumn('ParentCategoryID'),
            new StringColumn('Name', 50),
            new StringColumn('SeoSafeName', 100),
            new StringColumn('Image', 300),
            new BooleanColumn('Visible', false)
        );

        $model->labelColumnName = 'Name';

        return $model;
    }

    public function uploadImage(UploadedFileDetails $uploadData, $save = true)
    {
        if ($uploadData) {
            $uploadPath = APPLICATION_ROOT_DIR . '/static/images/category/' . $this->CategoryID . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 777, true);
            }

            $finalLocation = $uploadPath . sha1($this->UniqueIdentifier) . '-' . $uploadData->originalFilename;
            rename($uploadData->tempFilename, $finalLocation);

            $this->Image = str_replace(APPLICATION_ROOT_DIR, '',realpath($finalLocation));
            if ($save) {
                $this->save();
            }
        }
    }

    public static function find(Filter ...$filters)
    {
        return parent::find(new AndGroup([new Equals('Visible', true), new AndGroup($filters)]));
    }

    public function getProducts()
    {
        $categoryIds = [];

        $getChildIds = function(Category $category) use (&$categoryIds, &$getChildIds) {
            $children = $category->ChildCategories;
            if ($children->count()) {
                foreach ($children as $child) {
                    if ($child->ChildCategories->count()) {
                        $getChildIds($child);
                    }
                    $categoryIds[] = $child->UniqueIdentifier;
                }
            }
            $categoryIds[] = $category->UniqueIdentifier;
        };
        $getChildIds($this);

        return Product::find(new OneOf('CategoryID', $categoryIds));
    }

    public function getParentCategoryIDs()
    {
        $categories = [];
        $categories[] = $this->UniqueIdentifier;
        $currentCategory = $this;
        while ($currentCategory->ParentCategoryID) {
            $currentCategory = $currentCategory->ParentCategory;
            $categories[] = $currentCategory->UniqueIdentifier;
        }

        return $categories;
    }

    public function getPublicUrl()
    {
        return '/category/' . $this->SeoSafeName . '/';
    }

    public function getThumbnailUrl()
    {
        return $this->Image;
    }

    public static function getCategoryFromUrl()
    {
        $request = Request::current();
        $parts = explode('/', $request->uri);

        if (isset( $parts[ 1 ] ) && $parts[ 1 ] == 'category' && isset( $parts[ 2 ] ) && is_string($parts[ 2 ])) {
            try {
                return Category::findFirst(new Equals('SeoSafeName', $parts[2]));
            } catch (RecordNotFoundException $ex) {
                return false;
            }
        }
        return false;
    }
}
