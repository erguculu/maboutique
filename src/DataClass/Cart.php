<?php

namespace App\DataClass;



use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{

    private $session;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Cart constructor.
     * @param EntityManagerInterface $em
     * @param SessionInterface $session
     */
    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $em;
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

    public function getFull(): array
    {
        $cartComplete = [];

        if($this->get()){

            foreach ($this->get() as $id => $quantity){
             $product_object = $this->entityManager->getRepository(Product::class)->findOneBy([
                 'id' => $id,
             ]);
             if(!$product_object){
                 $this->delete($id);
                 continue;
             }
             $cartComplete[] = [
               'product' => $product_object,
                'quantity' => $quantity
             ];
            }
        }

        return $cartComplete;
    }

}