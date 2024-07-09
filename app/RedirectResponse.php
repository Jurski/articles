<?php

namespace App;
class RedirectResponse
{
    private string $location;
    private string $flashMessage;
    private string $flashType;

    public function __construct(string $location, string $flashMessage, string $flashType)
    {
        $this->location = $location;
        $this->flashMessage = $flashMessage;
        $this->flashType = $flashType;
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
}