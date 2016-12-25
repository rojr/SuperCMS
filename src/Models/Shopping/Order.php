<?php

namespace SuperCMS\Models\Shopping;

use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\Columns\AutoIncrementColumn;
use Rhubarb\Stem\Schema\Columns\ForeignKeyColumn;
use Rhubarb\Stem\Schema\Columns\StringColumn;
use Rhubarb\Stem\Schema\ModelSchema;

/**
 *
 *
 * @property int $OrderID Repository field
 * @property int $BasketID Repository field
 * @property-read OrderItem[]|\Rhubarb\Stem\Collections\RepositoryCollection $OrderItems Relationship
 * @property-read Basket $Basket Relationship
 * @property string $StripeToken Repository field
 * @property string $ClientIP Repository field
 */
class Order extends Model
{
    protected function createSchema()
    {
        $schema = new ModelSchema('tblOrder');

        $schema->addColumn(
            new AutoIncrementColumn('OrderID'),
            new ForeignKeyColumn('BasketID'),
            new StringColumn('StripeToken', 150),
            new StringColumn('ClientIP', '16')
        );

        return $schema;
    }
}