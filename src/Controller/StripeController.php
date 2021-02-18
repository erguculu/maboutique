<?php

namespace App\Controller;

use App\DataClass\Cart;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     * @param Cart $cart
     * @param $reference
     * @param OrderRepository $orderRepository
     * @param $name
     * @param ProductRepository $productRepository
     * @return Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function index(Cart $cart, $reference, OrderRepository $orderRepository, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {

        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.01:8000';

        $order = $orderRepository->findOneBy([
           "reference" => $reference
        ]);
        if(!$order){
            new JsonResponse(['error' => 'order']);
        }

        foreach ($order->getOrderDetails()->getValues() as $product){
            $product_object = $productRepository->findOneBy(['name' => $product->getProduct()]);
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];

        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51ILR2QK2LbgKQXqtDz237Wlp13yciean9d207Dh0yfITEvTdGw0k58BBW7Kpg7Ozds24aV4M3AFKWyMMjuUaRH5u00t6qBSStl');

        $YOUR_DOMAIN = 'http://127.0.01:8000';

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe,
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);

        $em->flush();


        $response = new  JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
