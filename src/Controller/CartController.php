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
        //dd($cart->getFull());
        $cartComplete = [];

        if($cart->get()){

            foreach ($cart->get() as $id => $quantity){
                $product_object = $productRepository->findOneBy(['id' => $id,]);
                
                if(!$product_object){
                    $cart->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity,
                ];
            }
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
     * @return Response
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/cart/delete/{id}", name="delete_to_cart")
     * @param Cart $cart
     * @param int $id
     * @return Response
     */
    public function delete(Cart $cart, int $id): Response
    {
        $cart->delete($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease_to_cart")
     * @param Cart $cart
     * @param int $id
     * @return Response
     */
    public function decrease(Cart $cart, int $id): Response
    {
        $cart->decrease($id);
        return $this->redirectToRoute('cart');
    }

}
