<?php

namespace App\Collection;

use App\Entity\FoodItem;
use App\Entity\Fruit;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;

class FruitCollection implements FoodCollectionInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FruitRepository $fruitRepository
    ) {}

    public function add(FoodItem $item): void
    {
        if (!$item instanceof Fruit) {
            throw new \InvalidArgumentException('Item must be a Fruit');
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function remove(string $name): void
    {
        $fruit = $this->fruitRepository->findOneBy(['name' => $name]);
        if ($fruit) {
            $this->entityManager->remove($fruit);
            $this->entityManager->flush();
        }
    }

    public function list(): array
    {
        return $this->fruitRepository->findAll();
    }

}