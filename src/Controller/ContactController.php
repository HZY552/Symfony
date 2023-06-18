<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(Security $security,EntityManagerInterface $entityManager,Request $request): Response
    {


        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contact->setName($form->get('name')->getData());
            $contact->setEmail($form->get('email')->getData());
            $contact->setMessage($form->get('message')->getData());

            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('Registration successful! %email%', ['%email%' => $contact->getEmail()]));

            return $this->redirectToRoute('app_home');
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }
}
