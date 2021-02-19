<?php

namespace App\Controller;

use App\DataClass\Cart;
use App\DataClass\Mailjet;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate", methods={"GET", "POST"})
     * @param OrderRepository $orderRepository
     * @param $stripeSessionId
     * @param EntityManagerInterface $em
     * @param Cart $cart
     * @return Response
     */
    public function index(OrderRepository $orderRepository, $stripeSessionId, EntityManagerInterface $em, Cart $cart): Response
    {
        $order = $orderRepository->findOneBy(['stripeSessionId'=>$stripeSessionId]);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home_index');
        }

        if (!$order->getIsPaid(0)){

            $cart->remove();
            $order->setIsPaid(1);
            $em->flush();
            $mail = new Mailjet();

            $content ="Bonjour".$order->getUser()->getFullName(). 'Merci pour votre commande';
            $mail->send($order->getUser()->getFullName(), $order->getUser()->getEmail(), 'Votre commande est bien validée', $content);

        }

        return $this->render('order_validate/index.html.twig',[
            'order' => $order,
        ]);
    }
}
