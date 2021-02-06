<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/nos-produits", name="products")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{slug}", name="product_show")
     * @param ProductRepository $productRepository
     * @param $slug
     * @return Response
     */
    public function show(ProductRepository $productRepository, $slug): Response
    {

        return $this->render('product/show.html.twig', [
            'product' => $productRepository->findOneBy($slug),
        ]);
    }
}
