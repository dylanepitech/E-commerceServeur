<?php

namespace App\Controller;

use App\Entity\CodePromotion;
use App\Entity\User;
use App\Repository\CodePromotionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CodepromoController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/api/codepromo", name: "create_codepromo", methods: ["POST"])]
    public function createCodePromo(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $userId = $data['user_id'] ?? null;
            $code = $data['code'] ?? null;
            $value = $data['value'] ?? null;
            $expireAt = $data['expire_at'] ?? null;

            if (!$userId || !$code || !$value || !$expireAt) {
                return new JsonResponse(['error' => 'Missing required fields'], 400);
            }

            $user = $entityManager->getRepository(User::class)->find($userId);
            if (!$user) {
                return new JsonResponse(['error' => 'User not found'], 404);
            }

            $codePromo = new CodePromotion();
            $codePromo->setUserId($user);  
            $codePromo->setCode($code);
            $codePromo->setValue($value);
            $codePromo->setCreatedAt(new \DateTimeImmutable());
            $codePromo->setExpireAt(new \DateTimeImmutable($expireAt));

            $entityManager->persist($codePromo);
            $entityManager->flush();

            return new JsonResponse(['message' => ['Code promo cree',"succes"]], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route("/api/codepromo", name: "get_all_codepromos", methods: ["GET"])]
    public function getAllCodePromos(EntityManagerInterface $entityManager, CodePromotionRepository $repository): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $userRoles)) {
                return $this->json(["message" => "Accès refusé"], 403);
            }

            $codePromos = $repository->findAll();
            $data = [];

            foreach ($codePromos as $codePromo) {
                $data[] = [
                    'id' => $codePromo->getId(),
                    'code' => $codePromo->getCode(),
                    'value' => $codePromo->getValue(),
                    'created_at' => $codePromo->getCreatedAt()->format('Y-m-d H:i:s'),
                    'expire_at' => $codePromo->getExpireAt()->format('Y-m-d H:i:s'),
                ];
            }

            return new JsonResponse($data, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    #[Route("/api/codepromo/{userId}", name: "get_codepromo_by_user", methods: ["GET"])]
    public function getCodePromoByUser(int $userId, CodePromotionRepository $repository): JsonResponse
    {
        try {

            $user = $this->getUser();
            if (!$user) {
                return $this->json(['message' => 'Utilisateur non authentifié'], 401);
            }


            $userRoles = $user->getRoles();
            if (!in_array("ROLE_ADMIN", $userRoles)) {
                return $this->json(['message' => 'Accès refusé'], 403);
            }


            $codePromos = $repository->findBy(['userId' => $userId]);

            if (empty($codePromos)) {
                return $this->json(['message' => 'Aucun code promo trouvé pour cet utilisateur'], 404);
            }


            $data = [];
            foreach ($codePromos as $codePromo) {
                if (!$codePromo instanceof CodePromotion) {
                    return $this->json(['message' => 'Erreur de données'], 500);
                }

                $data[] = [
                    'id' => $codePromo->getId(),
                    'code' => $codePromo->getCode(),
                    'value' => $codePromo->getValue(),
                    'created_at' => $codePromo->getCreatedAt()->format('Y-m-d H:i:s'),
                    'expire_at' => $codePromo->getExpireAt()->format('Y-m-d H:i:s'),
                ];
            }

            return new JsonResponse($data, 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Une erreur est survenue  ' . $e->getMessage()], 500);
        }
    }
}
