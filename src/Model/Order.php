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

    public function __construct(string $orderId, int $tableNumber, array $items)
    {
        $this->orderId = $orderId;
        $this->tableNumber = $tableNumber;
        $this->items = $items;
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



    public function cook(int $cookTime, array $ingredients)
    {
        $this->cookTime = $cookTime;
        $this->ingredients = $ingredients;
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
}