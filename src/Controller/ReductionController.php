<?php

namespace App\Controller;

use App\Entity\Reduction;
use App\Entity\User;
use App\Repository\ReductionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReductionController extends AbstractController
{

    #[Route('/api/reduction', name: 'app_create_reduction', methods: ["POST"])]
    public function create(Request $request, EntityManagerInterface $entityManager, ReductionRepository $reductionRepository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $idProducts = $data["id_products"] ?? [];
            $reductionValue = $data["reduction"] ?? null;
            $endAt = $data["end_at"] ?? null;

            if (empty($idProducts) || !$reductionValue || !$endAt) {
                return $this->json(["message" => "Veuillez remplir tous les champs"], 400);
            }

            $intReduction = intval($reductionValue);
            $endAtDate = new \DateTimeImmutable($endAt);
            $createdAt = new \DateTimeImmutable();

            foreach ($idProducts as $idProduct) {
                
                $existingReduction = $reductionRepository->findOneBy(['idCategory' => $idProduct]);

                if ($existingReduction) {
                   
                    $existingReduction->setReduction($intReduction);
                    $existingReduction->setEndAt($endAtDate);
                    $existingReduction->setCreatedAt($createdAt);
                } else {
                    
                    $reduction = new Reduction();
                    $reduction->setIdCategory($idProduct);
                    $reduction->setReduction($intReduction);
                    $reduction->setEndAt($endAtDate);
                    $reduction->setCreatedAt($createdAt);

                    $entityManager->persist($reduction);
                }
            }

            $entityManager->flush();

            return $this->json(["data" => ["Reductions mises à jour/créées","success"]], 201);
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue"], 500);
        }
    }



    #[Route('/api/reduction', name: 'app_show_all_reductions', methods: ["GET"])]
    public function show_all(ReductionRepository $reductionRepository): JsonResponse
    {
        try {

            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if ($user instanceof User) {

                if (!in_array("ROLE_ADMIN", $userRoles)) {
                    return $this->json(["message" => "Accès refusé"], 403);
                }
            }

            $reductions = $reductionRepository->findAll();
            $data = [];

            if (!$reductions) {
                return $this->json(["message" => "Aucune reduction trouvee"]);
            }
            foreach ($reductions as $reduction) {
                $data[] = [
                    "id" => $reduction->getId(),
                    "id_products" => $reduction->getIdCategory(),
                    "reduction" => $reduction->getReduction(),
                    "end_at" => $reduction->getEndAt(),
                    "created_at" => $reduction->getCreatedAt()
                ];
            }
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue"]);
        }
        return $this->json(["message" => $data]);
    }

    #[Route('/api/reduction/{id}', name: 'app_show_reductions', methods: ["GET"], requirements: ["id" => "\d+"])]
    public function show(int $id, ReductionRepository $reductionRepository): JsonResponse
    {
        try {


            $reduction = $reductionRepository->find($id);

            if (!$reduction) {
                return $this->json(["message" => "Aucune reduction trouvee"], 404);
            }

            $data = [
                "id" => $reduction->getId(),
                "id_products" => $reduction->getIdCategory(),
                "reduction" => $reduction->getReduction(),
                "end_at" => $reduction->getEndAt(),
                "created_at" => $reduction->getCreatedAt()
            ];
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue"]);
        }
        return $this->json(["message" => $data]);
    }

    #[Route('/api/reduction/{id}', name: 'app_update_reduction', methods: ["PATCH"], requirements: ["id" => "\d+"])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager, ReductionRepository $reductionRepository): JsonResponse
    {
        try {
            $reduction = $reductionRepository->find($id);

            if (!$reduction) {
                return $this->json(["message" => "Aucune reduction trouvee"], 404);
            }

            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if ($user instanceof User) {

                if (!in_array("ROLE_ADMIN", $userRoles)) {
                    return $this->json(["message" => "Accès refusé"], 403);
                }
            }
            $data = json_decode($request->getContent(), true);

            if (isset($data["idCategory"])) {
                $reduction->setIdCategory($data["idCategory"]);
            }
            if (isset($data["reduction"])) {
                $reduction->setReduction($data["reduction"]);
            }
            if (isset($data["end_at"])) {
                $reduction->setEndAt(new \DateTimeImmutable($data["end_at"]));
            }

            $entityManager->flush();

            return $this->json(["message" => "Reduction mis a jour"], 200);
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue"], 500);
        }
    }

    #[Route('/api/reduction/{id}', name: 'app_delete_reduction', methods: ["DELETE"],  requirements: ["id" => "\d+"])]
    public function delete(int $id, EntityManagerInterface $entityManager, ReductionRepository $reductionRepository): JsonResponse
    {
        try {

            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if ($user instanceof User) {

                if (!in_array("ROLE_ADMIN", $userRoles)) {
                    return $this->json(["message" => "Accès refusé"], 403);
                }
            }
            $reduction = $reductionRepository->find($id);

            if (!$reduction) {
                return $this->json(["message" => "Aucune reduction trouvee"], 404);
            }

            $entityManager->remove($reduction);
            $entityManager->flush();

            return $this->json(["message" => "Reduction supprimee"], 200);
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue"], 500);
        }
    }
}
