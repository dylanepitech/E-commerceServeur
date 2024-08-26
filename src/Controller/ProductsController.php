<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ProductsController extends AbstractController
{
    public $categoriesRepository;
    public $products;
    public function __construct(CategoriesRepository $categoriesRepository, ProductsRepository $productsRepository)
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

        $allProducts = $this->products->findBy(['categories' => $id]);


        $category = $this->categoriesRepository->find($id);


        $productJSON = [];


        if ($category) {

            foreach ($allProducts as $value) {
                $productJSON[] = [
                    "id" => $value->getId(),
                    "categoryId" => $category->getId(),
                    "categoryTitle" => $category->getTitle(),
                    "title" => $value->getTitle(),
                    "description" => $value->getDescription(),
                    "price" => $value->getPrice(),
                    "weight" => $value->getWeight(),
                    "images" => $value->getImages(),
                    "sizes" => $value->getSizes()
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
                "reduction" => $product->getReduction(),
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
        $gem = [3, 4, 7, 10, 11, 14, 19, 21, 23];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];
        $cat = [];

        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);
            foreach ($products as $product) {
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
                    "reduction" => $product->getReduction(),
                ];
            }
        }

        return $this->json($productJSON, 200);
    }
    #[Route('/api/get-pem-products', name: 'app_get_pem_product', methods: ['GET'])]
    public function getPemProduct()
    {
        $gem = [2, 5, 6, 8, 9];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];
        $cat = [];

        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);
            foreach ($products as $product) {
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
                    "reduction" => $product->getReduction(),
                ];
            }
        }

        return $this->json($productJSON, 200);
    }

    #[Route('/api/get-cuisine-products', name: 'app_get_cuisine_product', methods: ['GET'])]
    public function getCuisineProduct()
    {
        $gem = [1, 2, 6, 5, 7, 10, 13, 15, 16, 17, 18, 19, 20];
        $categories = $this->categoriesRepository->findBy(['id' => $gem]);

        $productJSON = [];
        $cat = [];

        foreach ($categories as $category) {
            $products = $this->products->findBy(['categories' => $category], ['id' => 'ASC']);
            foreach ($products as $product) {
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
                    "reduction" => $product->getReduction(),
                ];
            }
        }

        return $this->json($productJSON, 200);
    }
}
