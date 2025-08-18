<?php
namespace App\Controller\Api;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/api/cart', name: 'api_cart_')]
class CartController extends AbstractController
{
    #[Route('', name: 'get', methods: ['GET'])]
    public function getCart(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        $cart = $user->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $em->persist($cart);
            $user->setCart($cart);
            $em->flush();
        }

        $cartData = [
            'id' => $cart->getId(),
            'items' => [],
        ];

        foreach ($cart->getItems() as $item) {
            $product = $item->getProduct();
            $cartData['items'][] = [
                'id' => $item->getId(),
                'quantity' => $item->getQuantity(),
                'product' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'image' => $product->getMainImage(),
                ],
            ];
        }

        return $this->json($cartData);
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addToCart(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        $cart = $user->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $em->persist($cart);
            $user->setCart($cart);
        }

        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$productId) {
            return $this->json(['error' => 'Product ID is required'], 400);
        }

        $product = $em->getRepository(Product::class)->find($productId);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }

        $existingItem = null;
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct()->getId() === $product->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + $quantity);
        } else {
            $item = new CartItem();
            $item->setCart($cart);
            $item->setProduct($product);
            $item->setQuantity($quantity);
            $em->persist($item);
        }

        $em->flush();

        return $this->json(['message' => 'Product added to cart']);
    }

    #[Route('/remove/{id}', name: 'remove', methods: ['DELETE'])]
    public function removeFromCart(CartItem $item, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        if ($item->getCart()->getUser() !== $user) {
            return $this->json(['error' => 'Access denied'], 403);
        }

        $em->remove($item);
        $em->flush();

        return $this->json(['message' => 'Product removed from cart']);
    }
}
