<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $productJSON = [];

        foreach ($allProducts as $value) {
            $productJSON[] = [
                "id" => $value->getId(),
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
    }
}
