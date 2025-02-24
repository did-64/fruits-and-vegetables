<?php

namespace App\Tests\App\Collection;

use App\Collection\FruitCollection;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Exception\CustomHttpException;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class FruitCollectionTest extends TestCase
{
    private FruitRepository $fruitRepository;
    private FruitCollection $fruitCollection;
    protected function setUp():void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->fruitRepository = $this->createMock(FruitRepository::class);
        $this->fruitCollection = new FruitCollection($entityManager, $this->fruitRepository);
    }

    public function testAddException()
    {
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("Item must be a Fruit");
        $fruit = new Vegetable();
        $this->fruitCollection->add($fruit);
    }

    public function testRemove()
    {
        $fruit = new Fruit();
        $this->fruitRepository->expects($this->once())
            ->method('find')
            ->willReturn($fruit);
        $result = $this->fruitCollection->remove(1);
        $this->assertTrue($result);
    }

    public function testList()
    {
        $fruit1 = (new Fruit())->setName('apple')->setQuantity(3000);
        $fruit2 = (new Fruit())->setName('pineapple')->setQuantity(2000);
        $expectedData = [$fruit1, $fruit2];
        // TEST LIST FindAll
        $this->fruitRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedData);
        $result = $this->fruitCollection->list(null);
        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(Fruit::class, $result);
        // END TEST LIST FindAll

        // TEST LIST QueryBuilder
        $queryBuilderMock = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $queryMock = $this->createMock(\Doctrine\ORM\Query::class);

        $this->fruitRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('f')
            ->willReturn($queryBuilderMock);

        $queryBuilderMock->expects($this->once())
            ->method('where')
            ->with('f.name LIKE :query')
            ->willReturnSelf();

        $queryBuilderMock->expects($this->once())
            ->method('setParameter')
            ->with('query', '%apple%')
            ->willReturnSelf();

        $queryBuilderMock->expects($this->once())
            ->method('getQuery')
            ->willReturn($queryMock);

        $queryMock->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedData);

        $result = $this->fruitCollection->list('apple');
        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(Fruit::class, $result);
        // END TEST LIST QueryBuilder
    }

}