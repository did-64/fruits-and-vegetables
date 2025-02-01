<?php

namespace App\Service;

class StorageService
{
    protected string $request = '';


    public function __construct(
        private JsonStorageInterface $jsonStorage
    )
    {}

    public function setRequest(string $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): string
    {
        return $this->request;
    }

    public function submitRequest(): void
    {
        $this->jsonStorage->loadData($this->request);
    }
}
