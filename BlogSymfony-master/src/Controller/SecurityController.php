<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request); //analyser la requete http
        //ensuite
        if($form->isSubmitted() && $form->isValid()){ //si le form est soumis et que ses champs sont valides..
            //..alors on veut que le user persiste ds le tps, l'enregistrer.
            $hash = $encoder->encodePassword($user, $user->getPassword()); //encoder le mdp avant le persist

            $user->setPassword($hash); //on modif donc le mdp pris par le get pour l'encoder avant le persist

            $manager->persist($user);

            $manager->flush();

            return $this->redirectToRoute('security_login'); //redirection 1x que l'utilisateur cest enregistré
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route ("/connexion", name = "security_login")
     */
    public function login(){

        return $this->render('security/login.html.twig');

    }

    /**
     * @Route ("/deconnexion", name = "security_logout")
     */
    public function logout(){

    }
}
