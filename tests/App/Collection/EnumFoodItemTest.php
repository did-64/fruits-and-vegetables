<?php

namespace App\Tests\App\Collection;

use App\Collection\EnumFoodItem;
use App\Entity\Fruit;
use App\Exception\CustomHttpException;
use PHPUnit\Framework\TestCase;

class EnumFoodItemTest extends TestCase
{

    public function testGetInstanceFoodItem(): void
    {
        $entity = EnumFoodItem::getInstanceFoodItem('fruit');
        $this->assertInstanceOf(Fruit::class, $entity);
    }

    public function testGetInstanceFoodItemException(): void
    {
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("Invalid Type of Item");
        EnumFoodItem::getInstanceFoodItem('cereal');
    }
}