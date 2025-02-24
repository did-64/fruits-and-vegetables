<?php

namespace App\Tests\App\Collection;

use App\Collection\VegetableCollection;
use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Exception\CustomHttpException;
use App\Repository\VegetableRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class VegetableCollectionTest extends TestCase
{
    private VegetableRepository $vegetableRepository;
    private VegetableCollection $vegetableCollection;
    protected function setUp():void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->vegetableRepository = $this->createMock(VegetableRepository::class);
        $this->vegetableCollection = new VegetableCollection($entityManager, $this->vegetableRepository);
    }

    public function testAddException()
    {
        $this->expectException(CustomHttpException::class);
        $this->expectExceptionMessage("Item must be a Vegetable");
        $fruit = new Fruit();
        $this->vegetableCollection->add($fruit);
    }

    public function testRemove()
    {
        $vegetable = new Vegetable();
        $this->vegetableRepository->expects($this->once())
            ->method('find')
            ->willReturn($vegetable);
        $result = $this->vegetableCollection->remove(1);
        $this->assertTrue($result);
    }

    public function testList()
    {
        $vegetable1 = (new Vegetable())->setName('Carrot')->setQuantity(3000);
        $vegetable2 = (new Vegetable())->setName('Cucumber')->setQuantity(2000);
        $expectedData = [$vegetable1, $vegetable2];
        // TEST LIST FindAll
        $this->vegetableRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedData);
        $result = $this->vegetableCollection->list(null);
        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(Vegetable::class, $result);
        // END TEST LIST FindAll

        // TEST LIST QueryBuilder
        $queryBuilderMock = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $queryMock = $this->createMock(\Doctrine\ORM\Query::class);

        $this->vegetableRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->with('v')
            ->willReturn($queryBuilderMock);

        $queryBuilderMock->expects($this->once())
            ->method('where')
            ->with('v.name LIKE :query')
            ->willReturnSelf();

        $queryBuilderMock->expects($this->once())
            ->method('setParameter')
            ->with('query', '%c%')
            ->willReturnSelf();

        $queryBuilderMock->expects($this->once())
            ->method('getQuery')
            ->willReturn($queryMock);

        $queryMock->expects($this->once())
            ->method('getResult')
            ->willReturn($expectedData);

        $result = $this->vegetableCollection->list('c');
        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(Vegetable::class, $result);
        // END TEST LIST QueryBuilder
    }

}