<?php

declare(strict_types=1);

namespace App\Dto\Gandi;

class Address
{
    protected string $source;
    /** @var string[] */
    protected array $destinations = [];

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /** @return string[] */
    public function getDestinations(): array
    {
        return $this->destinations;
    }

    /** @param string[] $destinations */
    public function setDestinations(array $destinations): self
    {
        $this->destinations = $destinations;

        return $this;
    }

    public function addDestination(string $destination): self
    {
        $this->destinations[] = mb_strtolower($destination);
        $this->destinations = array_unique($this->destinations);

        return $this;
    }

    public function removeDestination(string $destination): self
    {
        $key = array_search(mb_strtolower($destination), $this->destinations, true);

        if (true === \is_int($key)) {
            unset($this->destinations[$key]);
        }

        return $this;
    }
}
