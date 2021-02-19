<?php

namespace App\Controller;

use App\DataClass\Mailjet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home_")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        //$mail =new Mailjet();
        //$mail->send('A. C', 'erguculu@gmail.com', 'Mon premier Mail', 'Ca fonctionne' );
        return $this->render('home/index.html.twig');
    }
}
