<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel", methods={"GET", "POST"})
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

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order,
        ]);
    }
}
