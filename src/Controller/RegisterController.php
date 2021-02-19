<?php

namespace App\Controller;

use App\DataClass\Mailjet;
use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserRepository $userRepository): Response
    {
        $notification =null;

        $user = new User();
        $form =$this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $password = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($password);

            $em->persist($user);

            $em->flush();

            $mail = new Mailjet();

            $content ="Bonjour".$user->getFullName();
            $mail->send($user->getFullName(), $user->getEmail(), 'Bienvenue sur Ma Boutique', $content);

            $notification = "Votre inscription a bien été enregistrée. Vous pouvez dès à présent vous connecter à votre compte";
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}
