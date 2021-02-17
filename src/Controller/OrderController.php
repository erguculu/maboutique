<?php

namespace App\Controller;

use App\DataClass\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande", name="order")
     * @param Request $request
     * @param Cart $cart
     * @return Response
     */
    public function index(Request $request, Cart $cart): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('account_address_add');
        }

        $form =$this->createForm(OrderType::class, null, [
            'user' =>$this->getUser(),
        ]);

         return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull(),
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     * @param Request $request
     * @param Cart $cart
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function add(Request $request, Cart $cart, EntityManagerInterface $em): Response
    {
        $form =$this->createForm(OrderType::class, null, [
            'user' =>$this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $date = new DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname(). ' '.$delivery->getLastname();
            $delivery_content .= '<br/>'.$delivery->getPhone();
            if (!$delivery->getCompany()){
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }

            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();
            // Save order in database
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $em->persist($order);


            //Save product details
            foreach ($cart->getFull() as $product){
                $orderDetail = new OrderDetail();
                $orderDetail->setMyOrder($order);
                $orderDetail->setProduct($product['product']->getName());
                $orderDetail->setQuantity($product['quantity']);
                $orderDetail->setPrice($product['product']->getPrice());
                $orderDetail->setTotal($product['product']->getPrice() * $product['quantity']);

                $em->persist($orderDetail);


            }
            //$em->flush();

            return $this->render('order/add.html.twig',[
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
            ]);
        }

        return $this->redirectToRoute('cart');
    }
}
