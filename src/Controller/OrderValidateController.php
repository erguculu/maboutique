<?php

namespace App\Controller;

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
     * @return Response
     */
    public function index(OrderRepository $orderRepository, $stripeSessionId, EntityManagerInterface $em): Response
    {
        $order = $orderRepository->findOneBy(['stripeSessionId'=>$stripeSessionId]);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home_index');
        }

        if (!$order->getIsPaid(0)){
            $order->setIsPaid(1);
            $em->flush();

        }

        return $this->render('order_validate/index.html.twig',[
            'order' => $order,
        ]);
    }
}
