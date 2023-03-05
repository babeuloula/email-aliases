<?php

declare(strict_types=1);

namespace App\Entity;

use App\Dto\Gandi\Address;
use App\Repository\AliasRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AliasRepository::class)]
class Alias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected int $id;

    #[ORM\Column(length: 255)]
    #[Groups('gandi')]
    protected string $source;

    #[ORM\Column(length: 255)]
    protected string $domain;

    #[ORM\Column(type: Types::JSON)]
    #[Groups('gandi')]
    protected array $destinations = [];

    #[ORM\Column(options: ['default' => true])]
    protected bool $enabled;

    #[ORM\Column]
    protected ?\DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    protected ?\DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setEnabled(true);
    }

    public static function fromAddress(Address $address, string $domain): self
    {
        return (new self())
            ->setDomain($domain)
            ->setSource($address->getSource())
            ->setDestinations($address->getDestinations())
        ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
        $this->destinations = array_values(
            array_filter(
                $destinations,
                static function (string $destination): bool {
                    return 0 < mb_strlen($destination);
                }
            )
        );

        return $this;
    }
}
