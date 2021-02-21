<?php

namespace App\Controller;

use App\DataClass\Mailjet;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('hme');
        }

        if($request->get('email')){
            $user = $userRepository->findOneByEmail($request->get('email'));

            //save reset password request in database
            if ($user){
                $resetPassword = new ResetPassword();
                $resetPassword->setUser($user);
                $resetPassword->setToken(uniqid());
                $resetPassword->setCreatedAt(new \DateTime());
                $em->persist($resetPassword);

                $em->flush();

                //send mail for update password

                $url = $this->generateUrl('reset_password', [
                    'token' => $resetPassword->getToken()
                ]);
                $mail = new Mailjet();

                $content ="Bonjour ".$user->getFullName(). " <a href='".$url."'>Pour réinisialiser votre mot de passe merci de cliquer ici</a>";
                $mail->send($user->getFullName(), $user->getEmail(), 'Réinitialiser votre mot de passe', $content);

                $this->addFlash('notice', 'Un email contenant les instructions afin de créer un nouveau mot de passe vous a été envoyé.');

            }else{
                $this->addFlash('notice', 'Cette adresse mail est inconnue.');
            }
        }
        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     * @param string $token
     * @param Request $request
     * @param ResetPasswordRepository $resetPasswordRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function update(string $token, Request $request, ResetPasswordRepository $resetPasswordRepository, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em): Response
    {
        $resetPassword = $resetPasswordRepository->findOneByToken([
            'token' => $token,
        ]);

       if (!$resetPassword){
            return $this->redirectToRoute('reset_password');
        }

        $now = new \DateTime();
        if ($now>$resetPassword->getCreatedAt()->modify('+ 45 minutes')){

            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $newPassword = $form->get('new_password')->getData();
           $password = $encoder->encodePassword($resetPassword->getUser(), $newPassword);
            $resetPassword->getUser()->setPassword($password);
            $em->flush();

            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');

        }
        return $this->render('reset_password/update.html.twig',[
            'form' => $form->createView(),
        ]);

    }

}
