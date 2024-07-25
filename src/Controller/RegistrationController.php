<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, EntityManagerInterface $em)
    {
        try {
            $data = json_decode($request->getContent(), true);

            $user = new User();

            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_USER']);
            $passwordHaser = $userPasswordHasherInterface->hashPassword($user, $data['password']);
            $user->setPassword($passwordHaser);

            $em->persist($user);
            $em->flush();

            return $this->json(['message' => 'Utilisateur enregistré avec succès'], 200);
        } catch (\Throwable $th) {
            return $this->json(['message' => "Erreur impossible d'enregistrer l'utilisateur"], 404);
        }
    }
}