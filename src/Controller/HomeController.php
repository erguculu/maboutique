<?php

namespace App\Controller;

use App\Repository\HeaderRepository;
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
     * @param HeaderRepository $headerRepository
     * @return Response
     */
    public function index(ProductRepository  $productRepository, HeaderRepository $headerRepository): Response
    {
        $products = $productRepository->findByIsBest('IsBest');
        $headers = $headerRepository->findAll();

        return $this->render('home/index.html.twig',[
            'products' => $products,
            'headers' => $headers,
        ]);
    }
}
