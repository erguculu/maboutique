<?php

namespace App\Controller;

use App\DataClass\Cart;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart): Response
    {

        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.01:8000';

        foreach ($cart->getFull() as $product){
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()],
                    ],
                ],
                'quantity' => $product['quantity'],
            ];

        }
        Stripe::setApiKey('sk_test_51ILR2QK2LbgKQXqtDz237Wlp13yciean9d207Dh0yfITEvTdGw0k58BBW7Kpg7Ozds24aV4M3AFKWyMMjuUaRH5u00t6qBSStl');

        $YOUR_DOMAIN = 'http://127.0.01:8000';

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe,
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);
        $response = new  JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
