<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\FreelancerProfile;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Form\ContactFormType;
use App\Form\UpdateProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeController extends AbstractController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    #[Route('/', name: 'app_home')]
    public function index(Security $security,Request $request,EntityManagerInterface $entityManager): Response
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

        $users = $entityManager->getRepository(User::class)->findBy(['freelancer' => true]);
        shuffle($users);
        $rdm_id = array_slice($users, 0, 3);
        $list_id = [];

        foreach ($rdm_id as $r){
            array_push($list_id,$r->getId());
        }

        $freelancerProfiles = $entityManager->getRepository(FreelancerProfile::class)->findBy(['userId' => $list_id]);


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'form' => $form->createView(),
            'top_freelances' => $freelancerProfiles,
        ]);


    }
}
