<?php

namespace App\Controller;

use App\Emails\EmailService;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MailingController extends AbstractController
{
    protected $emailService;
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    #[Route('/send-promotion-code', name: 'app_send_promotion', methods: ['POST'])]
    public function sendPromotion(Request $request, UserRepository $userRepository)
    {
        try {
            $data = json_decode($request->getContent(), true);


            if (is_array($data['userId'])) {
                foreach ($data['userId'] as  $userId) {
                    $user = $userRepository->find($userId);
                    $this->emailService->sendEmail(
                        $user->getEmail(),
                        "L'équipe Achideco vous offre un code promotionel !",
                        "emails/promotionCode.html.twig",
                        [
                            "firstname" => $user->getFirstname(),
                            "lastname" => $user->getLastname(),
                            "value" => $data['value'],
                            "code" => $data['code']
                        ]
                    );
                }
            } else {
                $user = $userRepository->find($data['userId']);

                $this->emailService->sendEmail(
                    $user->getEmail(),
                    "L'équipe Achideco vous offre un code promotionel !",
                    "emails/promotionCode.html.twig",
                    [
                        "firstname" => $user->getFirstname(),
                        "lastname" => $user->getLastname(),
                        "value" => $data['value'],
                        "code" => $data['code']
                    ]
                );
            }

            return $this->json(["message" => "Email envoyé"], 200);
        } catch (\Throwable $th) {
            return $this->json(["message" => "impossible d'envoyer l'email"], 404);
        }
    }
}
