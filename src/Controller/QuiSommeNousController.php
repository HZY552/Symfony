<?php

namespace App\Controller;

use App\Entity\FreelancerProfile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class QuiSommeNousController extends AbstractController
{
    #[Route('/qui_somme_nous', name: 'app_qui_somme_nous')]
    public function index(Security $security,EntityManagerInterface $entityManager): Response
    {
        $userRepository = $entityManager->getRepository(User::class);
        $freelancers = $userRepository->findBy(['freelancer' => true]);
        $list_idUser = [];
        foreach ($freelancers as $f){
            array_push($list_idUser,$f -> getId());
        }

        $users = $entityManager->getRepository(User::class)->findBy(['freelancer' => true]);
        shuffle($users);
        $rdm_id = array_slice($users, 0, 3);
        $list_id = [];
        foreach ($rdm_id as $r){
            array_push($list_id,$r->getId());
        }
        $freelancerProfiles = $entityManager->getRepository(FreelancerProfile::class)->findBy(['userId' => $list_id]);

        return $this->render('qui_somme_nous/index.html.twig', [
            'controller_name' => 'QuiSommeNousController',
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'top_freelance' => $freelancerProfiles
        ]);
    }
}
