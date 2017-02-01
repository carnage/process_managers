<?php

namespace ProcessManagers\Model;

class Order
{
    private $orderId;
    private $tableNumber;
    private $items = [];
    private $subtotal;
    private $tax;
    private $total;
    private $cookTime;
    private $ingredients = [];
    private $paid;
    private $cookedBy;
    private $dodgy;

    public function __construct(string $orderId, int $tableNumber, array $items, $dodgy = false)
    {
        $this->orderId = $orderId;
        $this->tableNumber = $tableNumber;
        $this->items = $items;
        $this->dodgy = $dodgy;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getTableNumber(): int
    {
        return $this->tableNumber;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getCookTime(): int
    {
        return $this->cookTime;
    }

    /**
     * @return array
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /**
     * @return mixed
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @return mixed
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return mixed
     */
    public function isPaid()
    {
        return $this->paid;
    }

    /**
     * @return mixed
     */
    public function getCookedBy()
    {
        return $this->cookedBy;
    }

    public function cook(int $cookTime, array $ingredients, string $by)
    {
        $this->cookTime = $cookTime;
        $this->ingredients = $ingredients;
        $this->cookedBy = $by;
    }

    public function addPrices($subTotal, $tax, $total)
    {
        $this->subtotal = $subTotal;
        $this->tax = $tax;
        $this->total = $total;
    }

    public function paid()
    {
        $this->paid = true;
    }

    public function isDodgy()
    {
        return $this->dodgy;
    }
}