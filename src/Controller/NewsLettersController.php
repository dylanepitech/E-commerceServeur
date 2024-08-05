<?php

namespace App\Controller;

use App\Entity\NewsLetters;
use App\Repository\NewsLettersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class NewsLettersController extends AbstractController
{
    #[Route('/api/newsletter', name: 'app_news_letters', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $em, UserRepository $userRepository, NewsLettersRepository $newsLettersRepository)
    {
        try {
            $data = json_decode($request->getContent(), true);

            $user = $userRepository->findOneBy(['email' => $data['email']]);
            $subscriber = $newsLettersRepository->findOneBy(['email' => $data['email']]);

            if ($user || $subscriber) {
                return $this->json(['message' => "Email déja présent en base de donnée"], 403);
            }

            $newsLetter = new NewsLetters();
            $newsLetter->setEmail($data['email']);
            $newsLetter->setCreatedAt(new \DateTimeImmutable('now'));

            $em->persist($newsLetter);
            $em->flush();
            return $this->json(['message' => "Email enregistrer dans la base de donner"], 200);
        } catch (\Throwable $th) {
            return $this->json(['message' => "Erreur serveur"], 500);
        }
    }
}
