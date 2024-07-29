<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'api_users', methods: ['GET'])]
    public function index(UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        if (!in_array("ROLE_ADMIN", $userRoles)) {
            return $this->json(["message" => "Accès refusé"], 403);
        }

        $users = $userRepository->findAll();
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "roles" => $user->getRoles(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "picture" => $user->getPicture(),
                "verified" => $user->isVerified(),
                "created_at" => $user->getCreatedAt(),
                "updated_at" => $user->getUpdatedAt(),
                "is_actif" => $user->isActif(),
            ];
        }
        return $this->json(["data" => $data]);
    }

    #[Route('/api/users/{id}', name: 'api_user_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id, UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();
        $userRoles = $user->getRoles();

        if (!in_array("ROLE_ADMIN", $userRoles)) {
            return $this->json(["message" => "Accès refusé"], 403);
        }

        $searchedUser = $userRepository->find($id);

        if (!$searchedUser) {
            return $this->json(["message" => "Aucun utilisateur trouvé"], 404);
        }

        $data = [
            "id" => $searchedUser->getId(),
            "email" => $searchedUser->getEmail(),
            "roles" => $searchedUser->getRoles(),
            "firstname" => $searchedUser->getFirstname(),
            "lastname" => $searchedUser->getLastname(),
            "picture" => $searchedUser->getPicture(),
            "verified" => $searchedUser->isVerified(),
            "created_at" => $searchedUser->getCreatedAt(),
            "updated_at" => $searchedUser->getUpdatedAt(),
        ];

        return $this->json(["data" => $data]);
    }

    #[Route("/api/users/me", name: "api_user_show_me", methods: ["GET"])]
    public function showMe(UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();
        $userInfo = $userRepository->findByEmail($user->getUserIdentifier());

        $data = [
            "id" => $userInfo->getId(),
            "email" => $userInfo->getEmail(),
            "roles" => $userInfo->getRoles(),
            "firstname" => $userInfo->getFirstname(),
            "lastname" => $userInfo->getLastname(),
            "picture" => $userInfo->getPicture(),
            "verified" => $userInfo->isVerified(),
            "created_at" => $userInfo->getCreatedAt(),
            "updated_at" => $userInfo->getUpdatedAt(),
        ];

        return $this->json(["data" => $data]);
    }

    #[Route("/api/users/{id}", name: "api_user_update", methods: ["PATCH"], requirements: ['id' => '\d+'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = $userRepository->find($id);

        if (!$user) {
            return $this->json(["message" => "Aucun utilisateur trouvé"], 404);
        }

        $userConnected = $this->getUser();

        if ($userConnected->getUserIdentifier() !== $user->getEmail() && !in_array("ROLE_ADMIN", $userConnected->getRoles())) {
            return $this->json(["message" => "Accès refusé"], 403);
        }

        $picture = $data["picture"] ?? null;
        $password = $data["password"] ?? null;
        $firstname = $data["firstname"] ?? null;
        $lastname = $data["lastname"] ?? null;
        $roles = $data["roles"] ?? null;

        if ($picture !== null) {
            $user->setPicture($picture);
        }

        if ($password !== null) {
            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
        }

        if ($firstname !== null) {
            $user->setFirstname($firstname);
        }

        if ($lastname !== null) {
            $user->setLastname($lastname);
        }

        if ($roles !== null) {

            if ($roles == "user") {
                $role = ['ROLE_USER'];
            } elseif ($roles == "admin") {
                $role = ['ROLE_ADMIN'];
            }


            $user->setRoles($role);
        }
        $user->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json([
            "message" => "Utilisateur modifié avec succès",
            "data" => [
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "roles" => $user->getRoles(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "picture" => $user->getPicture(),
                "created_at" => $user->getCreatedAt(),
                "updated_at" => $user->getUpdatedAt(),
            ]
        ]);
    }

    #[Route("/api/users/{id}", name: 'api_user_delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $userRepository->find($id);

        if (!$user) {
            return $this->json(["message" => "Aucun utilisateur trouvé"], 404);
        }

        $userConnected = $this->getUser();

        if ($userConnected->getUserIdentifier() !== $user->getEmail() && !in_array("ROLE_ADMIN", $userConnected->getRoles())) {
            return $this->json(["message" => "Accès refusé"], 403);
        }

        $user->setActif(false);
        $hashedPassword = $passwordHasher->hashPassword($user, "deleteduserdefinitively");
        $user->setPassword($hashedPassword);
        $entityManager->flush();

        return $this->json(["message" => "Utilisateur supprimé avec succès"]);
    }

    #[Route("/api/users", name: "api_user_create", methods: ["POST"])]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $userConnected = $this->getUser();

        if (!in_array("ROLE_ADMIN", $userConnected->getRoles())) {
            return $this->json(["message" => "Accès refusé"], 403);
        }

        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;

        if (!$email || !$password || !$firstname || !$lastname) {
            return $this->json(["message" => "Tous les champs sont requis"], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());
        $user->setVerified(false);
        $user->setPicture($data['picture'] ?? null);
        $user->setActif(true);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            "message" => "Utilisateur créé avec succès",
            "data" => [
                "id" => $user->getId(),
                "email" => $user->getEmail(),
                "roles" => $user->getRoles(),
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "created_at" => $user->getCreatedAt(),
                "updated_at" => $user->getUpdatedAt(),
            ]
        ], 201);
    }
}
