<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=UrlRepository::class)
 * @ORM\Table(name="url", uniqueConstraints={@ORM\UniqueConstraint(name="short_uri_idx", columns={"short_uri"})})
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'delete'],
)]
class Url
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $short_uri;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $orig_url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visits;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortUri(): ?string
    {
        return $this->short_uri;
    }

    public function setShortUri(string $short_uri): self
    {
        $this->short_uri = $short_uri;

        return $this;
    }

    public function getOrigUrl(): ?string
    {
        return $this->orig_url;
    }

    public function setOrigUrl(string $orig_url): self
    {
        $this->orig_url = $orig_url;

        return $this;
    }

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(?int $visits): self
    {
        $this->visits = $visits;

        return $this;
    }
}
