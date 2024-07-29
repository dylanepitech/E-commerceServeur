<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{

    #[Route('/api/users', name: 'api_users', methods: ['GET'])]

    // #[IsGranted("ROLE_ADMIN",  message:"Acces non autorisé")]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();


        // if ($user instanceof User) {
        //     $userRoles = $user->getRoles();
        //     if (!in_array("ROLE_ADMIN", $userRoles)) {
        //         return $this->json([
        //             "message" => "Vous n'êtes pas administrateur",
        //         ], 403);
        //     }
        // }

        $users = $userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'is_verified' => $user->isVerified(),
                'picture' => $user->getPicture(),
                'created_at' => $user->getCreatedAt(),
                'updated_at' => $user->getUpdatedAt(),
                'roles' => $user->getRoles(),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/users/{id}', name: 'api_user_show', methods: ['GET'])]
    public function show(UserRepository $userRepository, int $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(["message" => "user introuvable"]);
        }

        $data = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'is_verified' => $user->isVerified(),
            'picture' => $user->getPicture(),
            'created_at' => $user->getCreatedAt(),
            'updated_at' => $user->getUpdatedAt(),
            'roles' => $user->getRoles(),
        ];

        return new JsonResponse($data);
    }

    #[Route('/api/users/', name: 'api_user_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password']) || empty($data['firstname']) || empty($data['lastname'])) {
            return $this->json(['message' => 'Tous les champs sont requis'], 403);
        }

        if (empty($data['email']) || empty($data['password']) || empty($data['firstname']) || empty($data['lastname'])) {
            return $this->json(['message' => 'Tous les champs sont requis'], 403);
        }

        // $user = new User();
        // $user->setEmail($data['email']);
        // $user->setRoles(['ROLE_USER']);
        // $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        // $user->setPassword($hashedPassword);
        // $user->setFirstname($data['firstname']);
        // $user->setLastname($data['lastname']);
        // $user->setCreatedAt(new \DateTimeImmutable());
        // $user->setUpdatedAt(new \DateTimeImmutable());
        // $user->setVerified(false);
        // $user->setPicture($data['picture'] ?? null);

        // $entityManager->persist($user);
        // $entityManager->flush();

        return new JsonResponse(['status' => 'User created!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/users/{id}', name: 'api_user_update', methods: ['PUT'])]
    public function update(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, int $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(["message" => "user introuvable"]);
        }

        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $roles = $data['roles'] ?? null;

        if ($email) {
            $user->setEmail($email);
        }

        if ($password) {
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
        }

        if ($roles) {
            $user->setRoles($roles);
        }

        $entityManager->flush();

        return new JsonResponse(['status' => 'User mis a jour']);
    }

    #[Route('/api/users/{id}', name: 'api_user_delete', methods: ['DELETE'])]
    public function delete(UserRepository $userRepository, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return new JsonResponse(["message" => "user introuvable"]);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'User supprime!']);
    }
}
