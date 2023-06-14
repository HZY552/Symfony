<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Education;
use App\Entity\FreelancerProfile;
use App\Entity\User;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class FreelanceController extends AbstractController
{

    #[Route('/freelance/{id}', name: 'app_freelance')]
    public function index($id,Security $security,Request $request,EntityManagerInterface $entityManager): Response
    {
        $freelancer = $entityManager->getRepository(User::class)->findOneBy(['id' => $id]);

        $profile = $entityManager->getRepository(FreelancerProfile::class)->findOneBy(['userId' => $id]);

        $eduction = $entityManager->getRepository(Education::class)->findBy(['freelancerProfile' => $profile->getId()]);

        $list_comments = $entityManager->getRepository(Comment::class)->findBy(['freelanceprofile' => $profile->getId()]);

        $list_userId = [];

        foreach ($list_comments as $l){
            array_push($list_userId,$l->getUserId());
        }

        $list_name = [];
        $list_avatar = [];

        foreach ($list_userId as $id){
            if ($id != null){
                $resentity = $entityManager->getRepository(User::class)->findOneBy(['id'=>$id]);
                array_push($list_name,$resentity->getUsername());
                array_push($list_avatar,$resentity->getAvatar());
            }else{
                array_push($list_name,"Utilisateur anonyme");
                array_push($list_avatar,"default-avatar.png");
            }
        }

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setContent($form->get('content')->getData());
            if ($security->isGranted('ROLE_USER')){
                $user_id = $entityManager->getRepository(User::class)->findOneBy(['email'=>$security->getUser()->getUserIdentifier()]);
                $comment->setUserId($user_id->getId());
            }else{
                $comment->setUserId(null);
            }
            $comment->setCreateDate(new \DateTimeImmutable());
            $comment->setFreelanceprofile($profile);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_freelance', ['id' => $freelancer->getId()]);
        }


        return $this->render('freelance/index.html.twig', [
            'controller_name' => 'FreelanceController',
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'freelancer' => $freelancer,
            'profile' => $profile,
            'education' => $eduction,
            'form' => $form->createView(),
            'comments' => $list_comments,
            'comments_name' => $list_name,
            'comments_avatar' => $list_avatar,
        ]);
    }
}
