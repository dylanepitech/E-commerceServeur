<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, EntityManagerInterface $entity)
    {
        try {
            $data = json_decode($request->getContent(), true);

            $errors = [];

            if (empty($data['username'])) {
                $errors[] = 'Entrez le username.';
            }

            if (empty($data['lastname'])) {
                $errors[] = 'Entrez le lastname.';
            }

            if (empty($data['email'])) {
                $errors[] = "Entrez l'email";
            }

            if (empty($data['password'])) {
                $errors[] = 'Entrez le password.';
            }

            if (!empty($errors)) {
                return $this->json(['errors' => $errors], 400);
            }

            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles(['ROLE_USER']);
            $passwordHashed = $userPasswordHasherInterface->hashPassword($user, $data['password']);
            $user->setPassword($passwordHashed);
            $user->setUsername($data['username']);
            $user->setLastname($data['lastname']);
            $user->setCreatedAt(new \DateTimeImmutable()); 
            $user->setUpdatedAt(new \DateTimeImmutable()); 
            $user->setVerified(false); 

            $entity->persist($user);
            $entity->flush();

            return $this->json(['message' => 'Utilisateur enregistré avec succès'], 200);
        } catch (\Throwable $th) {
            return $this->json(['message' => "Erreur, impossible d'enregistrer l'utilisateur"], 500);
        }
    }
}
