<?php

namespace App\Tests\App\Collection;

use App\Collection\FoodCollectionManager;
use App\Collection\FoodCollectionManagerInterface;
use App\Collection\FruitCollection;
use App\Collection\VegetableCollection;
use App\Exception\CustomHttpException;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class FoodCollectionManagerTest extends TestCase
{
    private $fruitCollection;
    private FoodCollectionManager $manager;

    public function setUp(): void
    {
        $this->fruitCollection = $this->createMock(FruitCollection::class);
        $vegetableCollection = $this->createMock(VegetableCollection::class);
        $this->manager = new FoodCollectionManager(
            $this->fruitCollection,
            $vegetableCollection
        );
    }

    public function testListFood(){
        $returnList=  [[ 'id' => 1, 'name' => 'Apples', 'quantity' => 2000.00], [ 'id' => 2, 'name' => 'Pears', 'quantity' => 3500.00]];
        $this->fruitCollection->method('list')->willReturn($returnList);
        $result = $this->manager->listFood('fruit', null);
        $this->assertIsArray($result);
        $this->assertEquals($returnList, $result);
    }

    public function testListFoodWithQueryParam(){
        $list = [[ 'id' => 1, 'name' => 'Apples', 'quantity' => 2000.00]];
        $this->fruitCollection->method('list')
            ->with('apple')
            ->willReturn($list);

        $result = $this->manager->listFood('fruit', 'apple');
        $this->assertIsArray($result);
        $this->assertEquals($list, $result);
    }

    public function testListFoodThrowsException(){
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("Invalid Type of item");
        $this->manager->listFood('foo', null);
    }

    public function testAddFoodQuantityException(){
        $data = (object)['type' => 'fruit', 'name' => 'Grapes', 'quantity' => '20', 'unit' => 'kg'];
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("The value must be a number");
        $this->manager->addFood($data);
    }

    public function testAddFoodUnitException(){
        $data = (object)['type' => 'fruit', 'name' => 'Grapes', 'quantity' => 20, 'unit' => 'bar'];
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("Unsupported unit: bar");
        $this->manager->addFood($data);
    }

    public function testAddFoodNameException(){
        $data = (object)['type' => 'fruit', 'name' => '', 'quantity' => 20, 'unit' => 'kg'];
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("The value must be filled and type of string");
        $this->manager->addFood($data);
    }

    public function testRemoveFood()
    {
        $this->fruitCollection->method('remove')
            ->willReturn(true);
        $res = $this->manager->removeFood('fruit', 1);
        $this->assertTrue($res);
    }

    public function testRemoveFoodUnfoundIdException()
    {
        $this->fruitCollection->method('remove')
            ->willReturn(false);
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("No item found");
        $this->manager->removeFood('fruit', 999);
    }
}