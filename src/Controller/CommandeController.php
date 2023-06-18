<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\FreelancerProfile;
use App\Entity\Invoice;
use App\Entity\User;
use App\Form\CommandeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(Security $security,Request $request,EntityManagerInterface $entityManager): Response
    {

        if(!$security->isGranted('ROLE_USER')){
            return $this->redirectToRoute('app_login');
        }else{
            $freelanceId = $request->query->get('freelanceId');
            $freelancer = $entityManager->getRepository(FreelancerProfile::class)->findOneBy(['userId'=>$freelanceId]);
            $usetId = $entityManager->getRepository(User::class)->findOneBy(["email"=>$this->getUser()->getUserIdentifier()])->getId();

            $commande = new Commande();
            $invoice = new Invoice();
            $form = $this->createForm(CommandeFormType::class, $commande);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $freelancer = $entityManager->getRepository(User::class)->findOneBy(['id'=>$freelanceId]);
                $freelance_profile = $entityManager->getRepository(FreelancerProfile::class)->findOneBy(['userId'=>$freelanceId]);
                $buyer = $entityManager->getRepository(User::class)->findOneBy(['id'=>$usetId]);
                $commande->setFreelancer($freelancer);
                $commande->setBuyer($buyer);
                $commande->setCreateDate(new \DateTimeImmutable());
                $commande->setUnitPrice($freelance_profile->getPrice());
                $commande->setState(false);

                $commande->setStartDate($form->get('start_date')->getData());
                $commande->setEndDate($form->get('end_date')->getData());

                $startDate = new \DateTime($form->get('start_date')->getData()->format('Y-m-d'));
                $endDate = new \DateTime($form->get('end_date')->getData()->format('Y-m-d'));
                $interval = $startDate->diff($endDate);
                $days = $interval->days;

                $invoice->setUnitPrice($freelance_profile->getPrice());

                if ($days <= 0){
                    $commande->setTotalPrice($freelance_profile->getPrice() * 1);
                    $invoice->setTotalPrice($freelance_profile->getPrice() * 1);
                }else{
                    $commande->setTotalPrice($freelance_profile->getPrice() * $days);
                    $invoice->setTotalPrice($freelance_profile->getPrice() * $days);
                }

                $invoice->setNCommande($commande);
                $invoice->setPoste($form->get('poste')->getData());
                $invoice->setDate(new \DateTimeImmutable());

                $commande->setPoste($form->get('poste')->getData());
                $commande->setObjet($form->get('objet')->getData());
                $commande->setDiscriptionMission($form->get('discription_mission')->getData());

                $entityManager->persist($invoice);
                $entityManager->persist($commande);
                $entityManager->flush();

                return $this->redirectToRoute('app_invoice',['id'=>$invoice->getId()]);
            }
        }

        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
            'isGranted' => $security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
            'form' => $form->createView(),
            'freelancer' => $freelancer,
        ]);
    }
}
