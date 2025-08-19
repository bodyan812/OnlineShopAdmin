<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\MediaType;

#[ORM\Entity(repositoryClass: CartItemRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/cart',
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['cart:read']]
        ),
        new Post(
            uriTemplate: '/cart/add',
            security: "is_granted('ROLE_USER')",
            openapi: new Operation(
                requestBody: new RequestBody(
                    content: new \ArrayObject([
                        'application/json' => new MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => new \ArrayObject([
                                    'productId' => new \ArrayObject(['type' => 'integer', 'example' => 1]),
                                    'quantity' => new \ArrayObject(['type' => 'integer', 'example' => 1])
                                ])
                            ]),
                            example: ['productId' => 1, 'quantity' => 2]
                        )
                    ])
                )
            )
        ),
        new Delete(
            uriTemplate: '/cart/remove/{id}',
            security: "is_granted('ROLE_USER')"
        )
    ],
    denormalizationContext: ['groups' => ['cart:write']],
    normalizationContext: ['groups' => ['cart:read']]
)]
class CartItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['cart:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private Cart $cart;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['cart:read'])]
    private Product $product;

    #[ORM\Column(type: 'integer')]
    #[Groups(['cart:read'])]
    private int $quantity = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): static
    {
        $this->cart = $cart;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
