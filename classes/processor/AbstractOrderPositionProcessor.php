<?php namespace Lovata\OrdersShopaholic\Classes\Processor;

use Lovata\OrdersShopaholic\Classes\Item\CartPositionItem;

/**
 * Class AbstractOrderPositionProcessor
 * @package Lovata\OrdersShopaholic\Classes\Processor
 * @author Andrey Kharanenka, a.khoronenko@lovata.com, LOVATA Group
 */
abstract class AbstractOrderPositionProcessor
{
    /** @var CartPositionItem */
    protected $obCartPosition;

    /**
     * @param CartPositionItem $obCartPosition
     */
    /**
     * AbstractOrderPositionProcessor constructor.
     * @param CartPositionItem $obCartPosition
     */
    public function __construct($obCartPosition)
    {
        $this->obCartPosition = $obCartPosition;
        $this->init();
    }

    /**
     * Validate cart position
     * @return bool
     */
    abstract public function validate();

    /**
     * Check availability cart position
     * @return bool
     */
    abstract public function check();

    /**
     * Get cart position data
     * @return array
     */
    abstract public function getData();

    /**
     * Init method
     */
    protected function init()
    {
    }
}
