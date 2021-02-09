<?php

namespace App\Controller;

use App\DataClass\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart")
     * @param ProductRepository $productRepository
     * @param Cart $cart
     * @return Response
     */
    public function index(ProductRepository $productRepository, Cart $cart): Response
    {
        $cartComplete = [];

        foreach ($cart->get() as $id => $quantity){
            $cartComplete[] = [
                'product' => $productRepository->findOneBy([
                    'id' => $id,
                ]),
                'quantity' => $quantity,
            ];
        }

        //dd($cartComplete);
        return $this->render('cart/index.html.twig',[
            'cart' => $cartComplete,
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }


    /**
     * @Route("/cart/remove", name="remove_my_cart")
     * @param Cart $cart
     * @param $id
     * @return Response
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('products');
    }
}
