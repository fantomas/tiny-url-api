<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UrlRedirectController;
use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=UrlRepository::class)
 * @ORM\Table(name="url", uniqueConstraints={@ORM\UniqueConstraint(name="short_uri_idx", columns={"short_uri"})})
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: [
        'get',
        'put',
        'delete',
        'get_url_visits' => [
            'method' => 'GET',
            'path' => '/urls/{id}/visits',
            'controller' => UrlRedirectController::class,
            'normalization_context' => [
                'groups' => ['read_visits'],
            ],
        ],
    ],
    denormalizationContext: ['groups' => 'write'],
    normalizationContext: ['groups' => 'read'],
)]
class Url
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ({"read", "write"})
     */
    private string $short_uri;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read", "write"})
     */
    private string $orig_url;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read_visits"})
     */
    private int|null $visits;

    /**
     * @ORM\Version @ORM\Column(type="integer")
     */
    private int $version;

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

    public function setVisits(int $visits): self
    {
        $this->visits = $visits;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getVersion(): int
    {
        return $this->version;
    }
}
