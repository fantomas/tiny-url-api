<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UrlRedirectController;
use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
        'url_get_visits' => [
            'method' => 'GET',
            'path' => '/urls/{id}/visits',
            'normalization_context' => [
                'groups' => ['read_visits'],
            ],
        ],
    ],
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
     * @ORM\Column(type="string", length=50)
     * @Groups ({"read"})
     * @Assert\NotBlank
     * @Assert\Regex("/^\w/")
     * @Assert\Length(max = 50)
     */
    private string $shortUri;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"read"})
     * @Assert\NotBlank
     * @Assert\Length(max = 255)
     * @Assert\Url()
     */
    private string $origUrl;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read_visits"})
     */
    private int|null $visits = 0;

    /**
     * @ORM\Version @ORM\Column(type="integer")
     */
    private int $version;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortUri(): string
    {
        return $this->shortUri;
    }

    public function setShortUri(string $shortUri): self
    {
        $this->shortUri = $shortUri;

        return $this;
    }

    public function getOrigUrl(): string
    {
        return $this->origUrl;
    }

    public function setOrigUrl(string $origUrl): self
    {
        $this->origUrl = $origUrl;

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
