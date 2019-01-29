<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Notification\ContactNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, ContactNotification $notification)
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()){
            $notification->notify($contact);
            $this->addFlash('success', 'votre email a bien été envoyé');
            $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
