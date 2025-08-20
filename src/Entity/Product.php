<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['product:read']],
    operations: [
        new GetCollection(
            paginationEnabled: true,
            paginationItemsPerPage: 10,
            security: "is_granted('PUBLIC_ACCESS')",
            name: 'api_products_collection'
        ),
        new Get(security: "is_granted('PUBLIC_ACCESS')"),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'category' => 'exact',
    'name' => 'partial'
])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product:read', 'cart:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['product:read', 'cart:read'])]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['product:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['product:read', 'cart:read'])]
    private float $price;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[Groups(['product:read'])]
    private ?Category $category = null;

    #[ORM\OneToMany(
        targetEntity: Media::class,
        mappedBy: 'product',
        cascade: ['persist'],
        orphanRemoval: false
    )]
    #[Groups(['product:read'])]
    private Collection $media;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setProduct($this);
        }
        return $this;
    }
    public function getMainImage(): ?string
    {
        if ($this->media->isEmpty()) {
            return null;
        }
        return $this->media->first()->getWebPath();
    }
    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            if ($medium->getProduct() === $this) {
                $medium->setProduct(null);
            }
        }
        return $this;
    }
}
