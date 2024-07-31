<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/api/order', name: 'app_show_all_order', methods: ["GET"])]
    public function show_all(OrderRepository $orderRepository): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $userRoles)) {
                return $this->json(["message" => "Accès refusé"], 403);
            }

            $orders = $orderRepository->findAll();
            $data = [];

            foreach ($orders as $order) {
                $data[] = [
                    "id" => $order->getId(),
                    "reception_date" => $order->getReceptionDate(),
                    "order_date" => $order->getOrderDate(),
                    "status" => $order->getStatus(),
                    "user" => [
                        "id" => $order->getIdUser()->getId(),
                        "email" => $order->getIdUser()->getEmail(),
                        "firstname" => $order->getIdUser()->getFirstname(),
                        "lastname" => $order->getIdUser()->getLastname(),
                    ],
                    "cart" => [
                        "id" => $order->getIdCart()->getId(),
                        "products" => $order->getIdCart()->getIdProducts(),
                    ]

                ];
            }
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreurnest survenue"]);
        }
        return $this->json(["message" => $data]);
    }
    #[Route('/api/order/{id}', name: 'app_show_order', methods: ["GET"], requirements: ['id' => '\d+'])]
    public function show(int $id, OrderRepository $orderRepository): JsonResponse
    {
        try {

            $order = $orderRepository->find($id);

            if (!$order) {
                return $this->json(["message" => "Aucune commande trouvee"]);
            }

            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if ($user instanceof User) {

                if ($order->getIdUser()->getId() != $user->getId() && !in_array("ROLE_ADMIN", $userRoles)) {
                    return $this->json(["message" => "Accès refusé"], 403);
                }
            }


            $data = [];

            $data[] = [
                "id" => $order->getId(),
                "reception_date" => $order->getReceptionDate(),
                "order_date" => $order->getOrderDate(),
                "status" => $order->getStatus(),
                "user" => [
                    "id" => $order->getIdUser()->getId(),
                    "email" => $order->getIdUser()->getEmail(),
                    "firstname" => $order->getIdUser()->getFirstname(),
                    "lastname" => $order->getIdUser()->getLastname(),
                ],
                "cart" => [
                    "id" => $order->getIdCart()->getId(),
                    "products" => $order->getIdCart()->getIdProducts(),
                ]

            ];
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreurnest survenue"]);
        }
        return $this->json(["message" => $data]);
    }

    #[Route('/api/order', name: 'app_create_order', methods: ["POST"])]
    public function create(Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository, CartRepository $cartRepository): JsonResponse
    {
        try {

            $user = $this->getUser();

            if ($user instanceof User) {
                $userId = $user->getId();
            } else {
                return $this->json(['message' => "Aucun utilisateur trouvé"], 404);
            }

            $data = json_decode($request->getContent(), true);


            $idCart = $data['idCart'] ?? null;

            $order = new Order();

            if (!$idCart) {
                return $this->json(["message" => "Aucun panier selectionne"], 404);
            }

            $cart = $cartRepository->find($idCart);

            if (!$idCart) {
                return $this->json(["message" => "Aucun panier trouve"], 404);
            }

            $order->setIdCart($cart);
            $order->setIdUser($user);
            $order->setOrderDate(new \DateTimeImmutable());
            $order->setStatus("traitement");

            $entityManager->persist($order);
            $entityManager->flush();
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue", "error" => $th], 500);
        }
        return $this->json(["message" => "Commande reussie"]);
    }

    #[Route('/api/order/{id}', name: 'app_delete_order', methods: ["DELETE"], requirements: ['id' => '\d+'])]
    public function delete(int $id, OrderRepository $orderRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        try {

            $order = $orderRepository->find($id);

            if (!$order) {
                return $this->json(["message" => "Aucune commande trouvee"]);
            }
            
            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if ($user instanceof User) {

                if ($order->getIdUser()->getId() != $user->getId() && !in_array("ROLE_ADMIN", $userRoles)) {
                    return $this->json(["message" => "Accès refusé"], 403);
                }
            }

            $entityManager->remove($order);
            $entityManager->flush();
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreurnest survenue"]);
        }
        return $this->json(["message" => "Commande annulée/supprimée"]);
    }
}