<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home_")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(ProductRepository  $productRepository): Response
    {

        $products = $productRepository->findByIsBest('IsBest');

        return $this->render('home/index.html.twig',[
            'products' => $products,
        ]);
    }
}
