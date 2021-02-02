<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    /**
     * @Route("/compte/modifier_mon-mot-de-passe", name="account_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(Request $request,UserPasswordEncoderInterface $encoder, EntityManagerInterface $em): Response
    {
        $notification = null;

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $oldPassword = $form->get('old_password')->getData();

            if($encoder->isPasswordValid($user, $oldPassword)){
                $newPassword = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($user, $newPassword);
                
                $user->setPassword($password);
                $em->flush();
                $notification = "Votre mot de passe a bien été mis à jour";
            }else{
                $notification = "Votre mot de passe actuel n'est pas le bon";
            }
        }
        return $this->render('account/password.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification,
        ]);
    }
}
