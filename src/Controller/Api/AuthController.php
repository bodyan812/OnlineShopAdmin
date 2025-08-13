<?php
 namespace App\Controller\Api;

 use App\Entity\User;
 use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
 use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
 use Symfony\Component\Routing\Annotation\Route;
 use Doctrine\ORM\EntityManagerInterface;

 class AuthController extends AbstractController
 {
     #[Route('/api/login', name: 'api_login', methods: ['POST'])]
     public function login(JWTTokenManagerInterface $JWTManager): JsonResponse
     {
         // Этот метод теперь обрабатывается json_login аутентификатором
         $user = $this->getUser();
         return new JsonResponse([
             'token' => $JWTManager->create($user),
             'user' => [
                 'id' => $user->getId(),
                 'username' => $user->getUsername(),
                 'roles' => $user->getRoles()
             ]
         ]);
     }

     #[Route('/api/register', name: 'api_register', methods: ['POST'])]
     public function register(
         Request $request,
         UserPasswordHasherInterface $passwordHasher,
         EntityManagerInterface $em
     ): JsonResponse {
         $data = json_decode($request->getContent(), true);

         $user = new User();
         $user->setUsername($data['username']);
         $user->setPassword($passwordHasher->hashPassword(
             $user,
             $data['password']
         ));
         $user->setRoles(['ROLE_USER']);
         $user->setIsAdmin(false);

         $em->persist($user);
         $em->flush();

         return new JsonResponse([
             'message' => 'User created successfully',
             'userId' => $user->getId()
         ], 201);
     }
 }
