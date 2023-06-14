<?php

namespace App\Controller;

use App\Entity\FreelancerProfile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;

class RechercherController extends AbstractController
{
    #[Route('/rechercher/{key}', name: 'app_rechercher')]
    public function index(string $key,Security $security,EntityManagerInterface $entityManager,Request $request): Response
    {
        $pcValue = $request->query->get('pc');
        if (!empty($pcValue)){
            if($pcValue == "true"){
                $freelancers = $entityManager->createQueryBuilder()
                    ->select('user','freelanceprofile')
                    ->from(User::class,'user')
                    ->join('user.freelancerProfile','freelanceprofile')
                    ->where('user.freelancer = :is_freelancer')
                    ->andWhere('user.username like :key OR freelanceprofile.skills LIKE :key')
                    ->setParameters([
                        'is_freelancer' => true,
                        'key' => '%'.$key.'%'
                    ])
                    ->orderBy('freelanceprofile.price', 'DESC')
                    ->getQuery()
                    ->getResult();

            }else{
                $freelancers = $entityManager->createQueryBuilder()
                    ->select('user','freelanceprofile')
                    ->from(User::class,'user')
                    ->join('user.freelancerProfile','freelanceprofile')
                    ->where('user.freelancer = :is_freelancer')
                    ->andWhere('user.username like :key OR freelanceprofile.skills LIKE :key')
                    ->setParameters([
                        'is_freelancer' => true,
                        'key' => '%'.$key.'%'
                    ])
                    ->orderBy('freelanceprofile.price', 'ASC')
                    ->getQuery()
                    ->getResult();
            }

        }else{
            $freelancers = $entityManager->createQueryBuilder()
                ->select('user','freelanceprofile')
                ->from(User::class,'user')
                ->join('user.freelancerProfile','freelanceprofile')
                ->where('user.freelancer = :is_freelancer')
                ->andWhere('user.username like :key OR freelanceprofile.skills LIKE :key')
                ->setParameters([
                    'is_freelancer' => true,
                    'key' => '%'.$key.'%'
                ])
                ->getQuery()
                ->getResult();
        }

        $resultCount = count($freelancers);

        return $this->render('rechercher/index.html.twig', [
            'controller_name' => 'RechercherController',
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'freelances' => $freelancers,
            'key' => $key,
            'resultCount' => $resultCount,
        ]);
    }
    #[Route('/rechercher/', name: 'app_rechercher_null')]
    public function nullRechercher(Security $security,EntityManagerInterface $entityManager,Request $request): Response
    {
        $pcValue = $request->query->get('pc');
        if (!empty($pcValue)){
            if($pcValue == "true"){
                $freelancers = $entityManager->createQueryBuilder()
                    ->select('user','freelanceprofile')
                    ->from(User::class,'user')
                    ->join('user.freelancerProfile','freelanceprofile')
                    ->where('user.freelancer = :is_freelancer')
                    ->setParameters([
                        'is_freelancer' => true
                    ])
                    ->orderBy('freelanceprofile.price', 'DESC')
                    ->getQuery()
                    ->getResult();
            } else{
                $freelancers = $entityManager->createQueryBuilder()
                    ->select('user','freelanceprofile')
                    ->from(User::class,'user')
                    ->join('user.freelancerProfile','freelanceprofile')
                    ->where('user.freelancer = :is_freelancer')
                    ->setParameters([
                        'is_freelancer' => true
                    ])
                    ->orderBy('freelanceprofile.price', 'ASC')
                    ->getQuery()
                    ->getResult();
            }

        }else{
            $freelancers = $entityManager->createQueryBuilder()
                ->select('user','freelanceprofile')
                ->from(User::class,'user')
                ->join('user.freelancerProfile','freelanceprofile')
                ->where('user.freelancer = :is_freelancer')
                ->setParameters([
                    'is_freelancer' => true
                ])
                ->getQuery()
                ->getResult();
        }


        $resultCount = count($freelancers);

        return $this->render('rechercher/index.html.twig', [
            'controller_name' => 'RechercherController',
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'freelances' => $freelancers,
            'key' => 'sans attribut',
            'resultCount' => $resultCount,
        ]);
    }
}
