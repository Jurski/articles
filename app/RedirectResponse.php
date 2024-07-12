<?php

namespace App;
class RedirectResponse
{
    private string $location;
    private string $flashMessage;
    private string $flashType;
    private array $previousData;

    public function __construct(
        string $location,
        string $flashMessage,
        string $flashType,
        array  $previousData = []
    )
    {
        $this->location = $location;
        $this->flashMessage = $flashMessage;
        $this->flashType = $flashType;
        $this->previousData = $previousData;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getFlashMessage(): string
    {
        return $this->flashMessage;
    }

    public function getFlashType(): string
    {
        return $this->flashType;
    }

    public function getPreviousData(): array
    {
        return $this->previousData;
    }
}