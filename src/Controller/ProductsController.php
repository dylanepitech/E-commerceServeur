<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\SousCategory;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use App\Repository\ReductionRepository;
use App\Repository\SousCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProductsController extends AbstractController
{
    public $categoriesRepository;
    public $products;
    public function __construct(CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository, ReductionRepository $reductionRepository)
    {
        $this->products = $productsRepository;
        $this->categoriesRepository = $categoriesRepository;
    }
    #[Route('/api/get-category', name: 'app_get_category')]
    public function SendAllCategory()
    {

        $allCategory = $this->categoriesRepository->findAll();

        $categoryJSON = [];

        foreach ($allCategory as $value) {
            $categoryJSON[] = [
                "id" => $value->getId(),
                "title" => $value->getTitle(),
            ];
        }

        return $this->json($categoryJSON, 200);
    }

    #[Route('/api/get-product/{id}', name: 'app_get_product_by_id', methods: ['GET'])]
    public function getProductByCategory(int $id, ReductionRepository $reductionRepository)
    {
        $allProducts = $this->products->findAll();
        $reductions = $reductionRepository->findAll();


        $reductionsByProductId = [];

        if ($reductions) {
            foreach ($reductions as $reduction) {
                $productId = $reduction->getIdCategory();


                if (!isset($reductionsByProductId[$productId])) {
                    $reductionsByProductId[$productId] = $reduction->getReduction();
                }
            }
        }

        $allProducts = $this->products->findBy(['categories' => $id]);


        $category = $this->categoriesRepository->find($id);


        $productJSON = [];


        if ($category) {

            foreach ($allProducts as $value) {
                $productId = $value->getId();

                $productReduction = isset($reductionsByProductId[$productId]) ? $reductionsByProductId[$productId] : 0;
                $productJSON[] = [
                    "id" => $value->getId(),
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    "title" => $value->getTitle(),
                    "description" => $value->getDescription(),
                    "price" => $value->getPrice(),
                    "weight" => $value->getWeight(),
                    "images" => $value->getImages(),
                    "sizes" => $value->getSizes(),
                    "reduction" => $productReduction,
                ];
            }


            $productJSON[] = ["total_products" => count($productJSON)];


            return $this->json($productJSON, 200);
        } else {

            return $this->json(["error" => "Category not found"], 404);
        }
    }

    #[Route('/api/get-products', name: 'app_get_all_product', methods: ['GET'])]
    public function getAllProduct(ReductionRepository $reductionRepository)
    {
        $allProducts = $this->products->findAll();
        $reductions = $reductionRepository->findAll();


        $reductionsByProductId = [];

        if ($reductions) {
            foreach ($reductions as $reduction) {
                $productId = $reduction->getIdCategory();


                if (!isset($reductionsByProductId[$productId])) {
                    $reductionsByProductId[$productId] = $reduction->getReduction();
                }
            }
        }

        $productJSON = [];

        foreach ($allProducts as $product) {
            $category = $product->getCategories();
            $productId = $product->getId();

            $productReduction = isset($reductionsByProductId[$productId]) ? $reductionsByProductId[$productId] : 0;

            $productJSON[] = [
                "id" => $product->getId(),
                "categoryId" => $category ? $category->getId() : null,
                "categoryTitle" => $category ? $category->getTitle() : null,
                "title" => $product->getTitle(),
                "description" => $product->getDescription(),
                "price" => $product->getPrice(),
                "weight" => $product->getWeight(),
                "images" => $product->getImages(),
                "sizes" => $product->getSizes(),
                "reduction" => $productReduction,
            ];
        }


        $productJSON[] = ["total_products" => count($productJSON)];

        return $this->json($productJSON, 200);
    }





    #[Route('/api/get-topfive', name: 'app_get_topfive', methods: ["GET"])]
    public function getTopFIve(): JsonResponse
    {

        $topFive = [1, 2, 3, 4, 5, 6];


        $categories = $this->categoriesRepository->findBy(['id' => $topFive]);

        $productJSON = [];


        foreach ($categories as $category) {

            $product = $this->products->findOneBy(['categories' => $category], ['id' => 'ASC']);


            if ($product) {

                $images = $product->getImages();
                $firstImage = null;

                if (!empty($images)) {

                    $firstImageKey = array_key_first($images);

                    $firstImage = $images[$firstImageKey][0]['image'] ?? null;
                }

                $productJSON[] = [
                    "title" => $category->getTitle(),
                    "image" => $firstImage,
                ];
            }
        }



        return $this->json(["data" => $productJSON]);
    }


    #[Route('/api/get-gem-products', name: 'app_get_gem_product', methods: ['GET'])]
    public function getGemProduct(ReductionRepository $reductionRepository)
    {

        $reductions = $reductionRepository->findAll();


        $reductionsByProductId = [];

        if ($reductions) {
            foreach ($reductions as $reduction) {
                $productId = $reduction->getIdCategory();


                if (!isset($reductionsByProductId[$productId])) {
                    $reductionsByProductId[$productId] = $reduction->getReduction();
                }
            }
        }
        $gem = [3, 7, 10, 11, 14, 21];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];
        $cat = [];

        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);

            foreach ($products as $product) {
                $productId = $product->getId();
                $productReduction = isset($reductionsByProductId[$productId]) ? $reductionsByProductId[$productId] : 0;

                $productJSON[] = [
                    "id" => $product->getId(),
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    "title" => $product->getTitle(),
                    "description" => $product->getDescription(),
                    "price" => $product->getPrice(),
                    "weight" => $product->getWeight(),
                    "images" => $product->getImages(),
                    "sizes" => $product->getSizes(),
                    "reduction" => $productReduction,
                ];
            }
        }

        return $this->json($productJSON, 200);
    }

    #[Route('/api/get-pem-products', name: 'app_get_pem_product', methods: ['GET'])]
    public function getPemProduct(ReductionRepository $reductionRepository)
    {
        $reductions = $reductionRepository->findAll();


        $reductionsByProductId = [];

        if ($reductions) {
            foreach ($reductions as $reduction) {
                $productId = $reduction->getIdCategory();


                if (!isset($reductionsByProductId[$productId])) {
                    $reductionsByProductId[$productId] = $reduction->getReduction();
                }
            }
        }

        $gem = [2, 5, 6, 8, 9, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];


        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);
            foreach ($products as $product) {
                $productId = $product->getId();
                $productReduction = isset($reductionsByProductId[$productId]) ? $reductionsByProductId[$productId] : 0;

                $productJSON[] = [
                    "id" => $product->getId(),
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    "title" => $product->getTitle(),
                    "description" => $product->getDescription(),
                    "price" => $product->getPrice(),
                    "weight" => $product->getWeight(),
                    "images" => $product->getImages(),
                    "sizes" => $product->getSizes(),
                    "reduction" => $productReduction,
                ];
            }
        }

        return $this->json($productJSON, 200);
    }

    #[Route('/api/get-cuisine-products', name: 'app_get_cuisine_product', methods: ['GET'])]
    public function getCuisineProduct(ReductionRepository $reductionRepository)
    {
        $reductions = $reductionRepository->findAll();


        $reductionsByProductId = [];

        if ($reductions) {
            foreach ($reductions as $reduction) {
                $productId = $reduction->getIdCategory();


                if (!isset($reductionsByProductId[$productId])) {
                    $reductionsByProductId[$productId] = $reduction->getReduction();
                }
            }
        }

        $gem = [1, 27, 26];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];
        $cat = [];

        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);
            foreach ($products as $product) {
                $productId = $product->getId();
                $productReduction = isset($reductionsByProductId[$productId]) ? $reductionsByProductId[$productId] : 0;

                $productJSON[] = [
                    "id" => $product->getId(),
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    "title" => $product->getTitle(),
                    "description" => $product->getDescription(),
                    "price" => $product->getPrice(),
                    "weight" => $product->getWeight(),
                    "images" => $product->getImages(),
                    "sizes" => $product->getSizes(),
                    "reduction" => $productReduction,
                ];
            }
        }

        return $this->json($productJSON, 200);
    }

    #[Route('/api/get-products/promotion', name: "app_get_products_promotion", methods: ['GET'])]
    public function getPromotion(ProductsRepository $productsRepository): JsonResponse
    {

        $products = $productsRepository->createQueryBuilder('p')
            ->where('p.reduction IS NOT NULL')
            ->getQuery()
            ->getResult();

        $productJson = [];

        foreach ($products as $key => $product) {
            $productJson[] = [
                "id" => $product->getId(),
                "categoryId" => $product->getCategories(),
                "categoryTitle" => "pem",
                "title" => $product->getTitle(),
                "description" => $product->getDescription(),
                "price" => $product->getPrice(),
                "weight" => $product->getWeight(),
                "images" => $product->getImages(),
                "sizes" => $product->getSizes(),
                "reduction" => $product->getReduction()
            ];
        }

        return $this->json($productJson, 200);
    }

    #[Route('/api/sous-category', name: "app_create_sous_category", methods: ['POST'])]
    public function createSousCategory(Request $request, EntityManagerInterface $entityManager, SousCategoryRepository $sousCategoryRepository): JsonResponse
    {
        try {

            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $userRoles)) {
                return $this->json(["message" => "Accès refusé"], 403);
            }

            $data = json_decode($request->getContent(), true);

            $title = $data["title"] ?? null;
            $link = $data["link"] ?? null;

            if (!$title || !$link) {
                return $this->json(["message" => "Tous les champs sont requis"], 400);
            }

            $existingTitle = $sousCategoryRepository->findOneBy(["title" => $title]);
            $existingLink = $sousCategoryRepository->findOneBy(["link" => $link]);

            if ($existingLink || $existingTitle) {
                return $this->json(["message" => "Link ou title deja pris"], 400);
            }

            $sousCategory = new SousCategory();
            $sousCategory->setTitle($title);
            $sousCategory->setLink($link);
            $sousCategory->setIdProducts([]);

            $entityManager->persist($sousCategory);
            $entityManager->flush();
        } catch (\Throwable $th) {
            return $this->json(["data" => "Une erreur est survenue"]);
        }
        return $this->json(["data" => ["Sous category cree", "success"]], 200);
    }

    #[Route('/api/sous-category', name: "app_get_sous_category", methods: ['GET'])]
    public function getSousCategory(SousCategoryRepository $sousCategoryRepository): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $userRoles)) {
                return $this->json(["message" => "Accès refusé"], 403);
            }

            $sousCat = $sousCategoryRepository->findAll();

            if (!$sousCat) {
                return $this->json(["message" => "Aucune sous category"], 404);
            }

            $data = [];

            foreach ($sousCat as $key => $value) {
                $data[] = [
                    "id" => $value->getId(),
                    "title" => $value->getTitle(),
                    "link" => $value->getLink(),
                    "products" => $value->getIdProducts()
                ];
            }

            return $this->json(["data" => $data]);
        } catch (\Throwable $th) {
            return $this->json(["data" => "Une erreur est survenue"]);
        }
    }

    #[Route('/api/sous-category/{id}', name: "app_update_sous_category", methods: ['PATCH'])]
    public function updateSousCategory(int $id, Request $request, EntityManagerInterface $entityManager, SousCategoryRepository $sousCategoryRepository): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $userRoles)) {
                return $this->json(["message" => "Accès refusé"], 403);
            }

            $data = json_decode($request->getContent(), true);

            $category = $data["category"] ?? null;

            if (!$category) {
                return $this->json(["message" => "Aucune categorie selectionnee"], 400);
            }

            $sousCat = $sousCategoryRepository->find($id);

            if (!$sousCat) {
                return $this->json(["message" => "Aucune sous category ne corresponds"], 400);
            }

            $sousCat->setIdProducts($category);

            $entityManager->flush();
            $sousCat = $sousCategoryRepository->findAll();

            $data = [];
            foreach ($sousCat as $key => $value) {
                $data[] = [
                    "id" => $value->getId(),
                    "title" => $value->getTitle(),
                    "link" => $value->getLink(),
                    "products" => $value->getIdProducts()
                ];
            }

            return $this->json(["data" => $data]);
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue"]);
        }
    }

    #[Route('/api/sous-category/{id}', name: "app_update_sous_category_name", methods: ['PATCH'])]
    public function updateSousCategoryName(int $id, Request $request, EntityManagerInterface $entityManager, SousCategoryRepository $sousCategoryRepository): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $userRoles)) {
                return $this->json(["message" => "Accès refusé"], 403);
            }

            $data = json_decode($request->getContent(), true);

            $title = $data["title"] ?? null;
            $link = $data["link"] ?? null;


            if ($title === null && $link === null) {
                return $this->json(["message" => "Aucune donnée modifiée"], 400);
            }

            $sousCat = $sousCategoryRepository->find($id);

            if (!$sousCat) {
                return $this->json(["message" => "Aucune sous-catégorie ne correspond"], 400);
            }


            if ($title !== null) {
                $sousCat->setTitle($title);
            }

            if ($link !== null) {
                $sousCat->setLink($link);
            }

            $entityManager->flush();

            $sousCat = $sousCategoryRepository->findAll();

            $data = [];
            foreach ($sousCat as $value) {
                $data[] = [
                    "id" => $value->getId(),
                    "title" => $value->getTitle(),
                    "link" => $value->getLink(),
                    "products" => $value->getIdProducts()
                ];
            }

            return $this->json(["data" => $data]);
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue"], 500);
        }
    }

    #[Route('/api/sous-category/{id}', name: "app_delete_sous_category", methods: ['DELETE'])]
    public function deleteSousCategory(int $id, EntityManagerInterface $entityManager, SousCategoryRepository $sousCategoryRepository): JsonResponse
    {
        try {
            $user = $this->getUser();
            $userRoles = $user->getRoles();

            if (!in_array("ROLE_ADMIN", $userRoles)) {
                return $this->json(["message" => "Accès refusé"], 403);
            }

            $sousCat = $sousCategoryRepository->find($id);

            if (!$sousCat) {
                return $this->json(["message" => "Aucune sous-catégorie ne correspond"], 400);
            }

            $entityManager->remove($sousCat);
            $entityManager->flush();

            return $this->json(["message" => ["Sous-catégorie supprimée","success"]]);
        } catch (\Throwable $th) {
            return $this->json(["message" => "Une erreur est survenue lors de la suppression"], 500);
        }
    }
}
