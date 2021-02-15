<?php

namespace App\Controller;

use App\DataClass\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    /**
     * @Route("/nos-produits", name="product_index")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function index(Request $request, ProductRepository $productRepository): Response
    {

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ){
            $products = $productRepository->findWithSearch($search);
        }else{
            $products =$productRepository->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/product/{id}", methods="GET", name="product_show")
     * @param ProductRepository $productRepository
     * @param int $id
     * @return Response
     */
    public function show(ProductRepository $productRepository, int $id): Response
    {
        return $this->render('product/show.html.twig', [
            "product" => $productRepository->findOneBy([
               'id' => $id,
           ])
        ]);
    }

}
