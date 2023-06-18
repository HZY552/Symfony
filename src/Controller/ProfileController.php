<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Education;
use App\Entity\FreelancerProfile;
use App\Entity\User;
use App\Form\UpdateFreelanceEducation;
use App\Form\UpdateFreelanceInfo;
use App\Form\UpdateProfileFormType;
use Container8vRJyIR\getDoctrine_Orm_Validator_UniqueService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\Query;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function PHPUnit\Framework\throwException;

class ProfileController extends AbstractController
{

    #[Route('/profile', name: 'app_profile')]
    public function index(Security $security,Request $request,EntityManagerInterface $entityManager): Response
    {
        // test
        if ($security->isGranted('ROLE_USER')){
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);
            $form = $this->createForm(UpdateProfileFormType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->update([
                    'updateat' => new \DateTimeImmutable(),
                    'Phone' => $form->get('Phone')->getData(),
                    'Username' => $form->get('Username')->getData(),
                    'FullName' => $form->get('full_name')->getData(),
                    'Location' => $form->get('location')->getData(),
                ]);

                $entityManager->flush();
            }
            return $this->render('profile/index.html.twig', [
                'isGranted' => $security->isGranted('ROLE_USER'),
                'user' => $this->getUser(),
                'registrationForm' => $form->createView(),
            ]);
        }


        return $this->redirectToRoute('app_home');
    }

    #[Route('/profile/freelance/mes-info', name: 'app_freelance_info')]
    public function freelance_profile(Security $security,Request $request,EntityManagerInterface $entityManager): Response
    {

        if ($security->isGranted('ROLE_USER')){
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);
            $freelanceprofile = $entityManager->getRepository(FreelancerProfile::class)->findOneBy(['userId' => $user->getId()]);
            $form = $this->createForm(UpdateFreelanceInfo::class, $freelanceprofile);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                if (empty($freelanceprofile)){
                    if ($freelanceprofile === null) {
                        $freelanceprofile = new FreelancerProfile();
                        $freelanceprofile->setUser($user);
                        $entityManager->persist($freelanceprofile);
                    }
                    $freelanceprofile -> setSkills($form->get('Skills')->getData());
                    $freelanceprofile -> setPortfolio($form->get('Portfolio')->getData());
                    $freelanceprofile -> setPrice($form->get('Price')->getData());
                    $freelanceprofile -> setDescription($form->get('Description')->getData());

                    $entityManager->persist($freelanceprofile);
                    $entityManager->flush();
                }else{
                    $freelanceprofile->update([
                        'Skills' => $form->get('Skills')->getData(),
                        'Portfolio' => $form->get('Portfolio')->getData(),
                        'Price' => $form->get('Price')->getData(),
                        'Description' => $form->get('Description')->getData(),
                        'UserId' => $user->getId(),
                    ]);

                    $entityManager->flush();
                }

            }
            return $this->render('profile/index.html.twig', [
                'isGranted' => $security->isGranted('ROLE_USER'),
                'user' => $this->getUser(),
                'freelanceprofile' => $freelanceprofile,
                'form_freelance_info' => $form->createView(),
            ]);
        }


        return $this->redirectToRoute('app_home');
    }

    #[Route('/profile/freelance/mes-eduction/{id}', name: 'app_freelance_education')]
    public function freelance_profile_education(Security $security,Request $request,EntityManagerInterface $entityManager,$id): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);

        if ($user->isFreelancer()){
            $freelanceprofile = $entityManager->getRepository(FreelancerProfile::class)->findOneBy(['userId' => $user->getId()]);
            $education = $entityManager->getRepository(Education::class)->findBy(['freelancerProfile' => $freelanceprofile->getId()]);
        }else{
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }


        if ($id != 0 && $id != 4589147){

            $education_form = $entityManager->getRepository(Education::class)->findOneBy(['id' => $id]);
            if ($user->isFreelancer() && $education_form->getFreelancerProfile()->getUser()->getId() === $user->getId()){
                $form = $this->createForm(UpdateFreelanceEducation::class, $education_form);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $freelanceprofile->update([
                        'Degree' => $form->get('degree')->getData(),
                        'School' => $form->get('school')->getData(),
                        'StartYear' => $form->get('startYear')->getData(),
                        'EndYear' => $form->get('endYear')->getData(),
                    ]);

                    $entityManager->flush();

                }

                $form_valid = $form->createView();
            }else{
                throw $this->createAccessDeniedException('You do not have permission to access this page.');
            }

        }elseif ($id == 4589147){
            $education_form = new Education();

            $education_form->setFreelancerProfile($freelanceprofile);
            $entityManager->persist($education_form);

            $form = $this->createForm(UpdateFreelanceEducation::class, $education_form);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $education_form -> setDegree($form->get('degree')->getData());
                $education_form -> setSchool($form->get('school')->getData());
                $education_form -> setStartYear($form->get('startYear')->getData());
                $education_form -> setEndYear($form->get('endYear')->getData());

                $entityManager->persist($education_form);
                $entityManager->flush();
                return $this->redirectToRoute('app_freelance_education', ['id' => 0]);
            }

            $form_valid = $form->createView();
        }else{
            $education_form = null;
            $form_valid = null;
        }


        return $this->render('profile/index.html.twig', [
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'list_education' => $education,
            'form' => $form_valid,
            'education_form' => $education_form,
        ]);
    }
    #[Route('/profile/commandes', name: 'app_user_commandes')]
    public function user_commande(Security $security,EntityManagerInterface $entityManager){
        $userId = $entityManager->getRepository(User::class)->findOneBy(['email'=>$this->getUser()->getUserIdentifier()]);
        if (!$userId->isFreelancer()){
            $commande = $entityManager->getRepository(Commande::class)->findBy(['buyer'=>$userId]);
        }else{
            $commande = $entityManager->getRepository(Commande::class)->findBy(['freelancer'=>$userId]);
        }


        return $this->render('profile/index.html.twig', [
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'commandes' => $commande,
        ]);
    }

    #[Route('/profile/commandes/details/{id}', name: 'app_user_commandes_details')]
    public function user_commande_details(Security $security,EntityManagerInterface $entityManager,$id){
        $userId = $entityManager->getRepository(User::class)->findOneBy(['email'=>$this->getUser()->getUserIdentifier()]);

        $commande = $entityManager->getRepository(Commande::class)->findOneBy(['id'=>$id]);
        if ($userId->getId() === $commande->getBuyer()->getId() || $userId->getId() === $commande->getFreelancer()->getId()){

            $startDate = new \DateTime($commande->getStartDate()->format('Y-m-d'));
            $endDate = new \DateTime($commande->getEndDate()->format('Y-m-d'));
            $interval = $startDate->diff($endDate);
            $days = $interval->days;
            if($days <= 0){
                $days = 1;
            }

            return $this->render('profile/index.html.twig', [
                'isGranted' => $security->isGranted('ROLE_USER'),
                'user' => $this->getUser(),
                'commande' => $commande,
                'days' => $days,
            ]);
        }else{
            throw $this->createAccessDeniedException('You do not have permission to access this page.');
        }

    }

    #[Route('/profile/commandes/delete/{id}', name: 'app_user_commandes_delete')]
    public function user_commande_delete(Security $security,EntityManagerInterface $entityManager,$id){
        $userId = $entityManager->getRepository(User::class)->findOneBy(['email'=>$this->getUser()->getUserIdentifier()]);
        $commande = $entityManager->getRepository(Commande::class)->findOneBy(['id'=>$id]);
        if ($userId->getId() === $commande->getBuyer()->getId() || $userId->getId() === $commande->getFreelancer()->getId()){
            if (!empty($commande)){
                $entityManager->remove($commande);
                $entityManager->flush();
            }
        }else{
            throwException($this->createAccessDeniedException('You do not have permission to access this page.'));
        }
        return $this->redirectToRoute("app_user_commandes");
    }


}
