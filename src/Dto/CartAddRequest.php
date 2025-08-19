<?php
namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CartAddRequest
{
    #[Assert\NotNull]
    #[Groups(['cart:write'])]
    public ?int $productId = null;

    #[Assert\NotNull]
    #[Assert\Positive]
    #[Groups(['cart:write'])]
    public ?int $quantity = 1;
}
