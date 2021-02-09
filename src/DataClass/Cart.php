<?php

namespace App\DataClass;



use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    private $session;

    /**
     * Cart constructor.
     * @param $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id] =1;
        }

        $this->session->set('cart', $cart);
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove()
    {
        return $this->session->get('cart');
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);


        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);

        if($cart[$id] > 1){
            $cart[$id]--;

        }else{

            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart );
    }
/**
    public function getFull(ProductRepository $productRepository, int $id): array
    {
        $cartComplete = [];

        if($this->get()){

            foreach ($this->get() as $id => $quantity){
                $cartComplete[] = [
                    'product' => $productRepository->findOneBy([
                        'id' => $id,
                    ]),
                    'quantity' => $quantity,
                ];
            }
        }

        return $cartComplete;
    }
*/
}