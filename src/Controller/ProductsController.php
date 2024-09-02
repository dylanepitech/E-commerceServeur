<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use App\Repository\ReductionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function getProductByCategory(int $id)
    {
        $allProducts = $this->products->findAll();


        $allProducts = $this->products->findBy(['categories' => $id]);


        $category = $this->categoriesRepository->find($id);


        $productJSON = [];


        if ($category) {

            foreach ($allProducts as $value) {
                $reduction = $value->getReduction();

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
                    "reduction" => $reduction ? $reduction->getReduction() : 0,
                ];
            }


            $productJSON[] = ["total_products" => count($productJSON)];


            return $this->json($productJSON, 200);
        } else {

            return $this->json(["error" => "Category not found"], 404);
        }
    }

    #[Route('/api/get-products', name: 'app_get_all_product', methods: ['GET'])]
    public function getAllProduct()
    {
        $allProducts = $this->products->findAll();
        $productJSON = [];

        foreach ($allProducts as $product) {
            $category = $product->getCategories();
            $productId = $product->getId();
            $reduction = $product->getReduction();


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
                "reduction" => $reduction ? $reduction->getReduction() : 0,
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
    public function getGemProduct()
    {
        $gem = [3, 7, 10, 11, 14, 21];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];
        $cat = [];

        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);

            foreach ($products as $product) {
                $reduction = $product->getReduction();

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
                    "reduction" => $reduction ? $reduction->getReduction() : 0,
                ];
            }
        }

        return $this->json($productJSON, 200);
    }

    #[Route('/api/get-pem-products', name: 'app_get_pem_product', methods: ['GET'])]
    public function getPemProduct()
    {
        $gem = [2, 5, 6, 8, 9, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];


        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);
            foreach ($products as $product) {
                $reduction = $product->getReduction();

                $productJSON[] = [
                    "id" => $product->getId(),
                    "categoryId" => $product->getCategories()->getId(),
                    "categoryTitle" => $product->getCategories()->getTitle(),
                    "title" => $product->getTitle(),
                    "description" => $product->getDescription(),
                    "price" => $product->getPrice(),
                    "weight" => $product->getWeight(),
                    "images" => $product->getImages(),
                    "sizes" => $product->getSizes(),
                    "reduction" => $reduction ? $reduction->getReduction() : 0,
                ];
            }
        }

        return $this->json($productJSON, 200);
    }

    #[Route('/api/get-cuisine-products', name: 'app_get_cuisine_product', methods: ['GET'])]
    public function getCuisineProduct()
    {


        $gem = [1, 27, 26];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];

        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);
            foreach ($products as $product) {
                $reduction = $product->getReduction();

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
                    "reduction" => $reduction ? $reduction->getReduction() : 0,
                ];
            }
        }

        return $this->json($productJSON, 200);
    }

    #[Route('/api/get-products/promotion', name: "app_get_products_promotion", methods: ['GET'])]
    public function getPromotion(ProductsRepository $productsRepository): JsonResponse
    {

        $products = $productsRepository->createQueryBuilder('p')
            ->join('App\Entity\Reduction', 'r', 'WITH', 'r.id_category = p.id')
            ->where('r.id IS NOT NULL')
            ->getQuery()
            ->getResult();

        $productJson = [];

        foreach ($products as $product) {
            $categories = [];

            $categories = $product->getCategories();
            $reduction = $product->getReduction();

            $productJson[] = [
                "id" => $product->getId(),
                "categoriesId" => $categories->getId(),
                "categoriesTitle" => $categories->getTitle(),
                "title" => $product->getTitle(),
                "description" => $product->getDescription(),
                "price" => $product->getPrice(),
                "weight" => $product->getWeight(),
                "images" => $product->getImages(),
                "sizes" => $product->getSizes(),
                "reduction" => $reduction ? $reduction->getReduction() : 0,
            ];
        }

        return $this->json($productJson, 200);
    }
}
