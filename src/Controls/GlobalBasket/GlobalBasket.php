<?php

namespace SuperCMS\Controls\GlobalBasket;

use Rhubarb\Stem\Aggregates\Sum;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;
use SuperCMS\Models\Shopping\Basket;
use SuperCMS\Session\SuperCMSSession;

class GlobalBasket
{
    private static $instance;

    /**
     * @var Basket
     */
    protected $basket;

    public function __construct()
    {
        $session = SuperCMSSession::singleton();
        try {
            $this->basket = new Basket($session->basketId);
        } catch (RecordNotFoundException $ex) {
            $this->basket = new Basket();
        }

        self::$instance = $this;
    }

    public function getOnlyHTML()
    {
        list($itemAmount) = $this->basket->BasketItems->calculateAggregates(new Sum('Quantity'));
        $itemAmount = intval($itemAmount);
        $itemItems = $itemAmount === 1 ? 'item' : 'items';
        return <<<HTML
        <div id="global-basket">
            <a href="/basket/">You have <i class="fa fa-shopping-cart" aria-hidden="true"></i> <span class="c-basket-count">{$itemAmount}</span> {$itemItems}</a>
        </div>
HTML;
    }

    public function __toString()
    {
        return <<<HTML
        <htmlupdate id="global-basket">
            <![CDATA[{$this->getOnlyHTML()}]]>
        </htmlupdate>
HTML;
    }

    public function reLoadBasket()
    {
        $session = SuperCMSSession::singleton();
        try {
            $this->basket = new Basket($session->basketId);
        } catch (RecordNotFoundException $ex) {
            $this->basket = new Basket();
        }
    }

    public function replace()
    {
        print $this;
    }

    public static function getInstance()
    {
        return self::$instance;
    }
}
