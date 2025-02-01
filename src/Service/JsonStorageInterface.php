<?php

namespace App\Service;

interface JsonStorageInterface
{
    public function loadData(string $jsonData): void;
}