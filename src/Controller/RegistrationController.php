<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;
    private Security $security;
    private TranslatorInterface $translator;
    private string $avatarDirectory;
    private $tokenStorage;
    private $eventDispatcher;

    public function __construct(TokenStorageInterface $tokenStorage,EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, Security $security, TranslatorInterface $translator, string $avatarDirectory,EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->security = $security;
        $this->translator = $translator;
        $this->avatarDirectory = $avatarDirectory;
        $this->eventDispatcher = $eventDispatcher;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $user->setFreelancer(0);
            $avatarFile = $form->get('avatar')->getData();

            if ($avatarFile) {
                // Generate a unique filename
                $newFilename = uniqid().'.'.$avatarFile->guessExtension();

                // Move the file to the desired directory
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir').$this->avatarDirectory,
                    $newFilename
                );

                // Update the user's avatar path
                $user->setAvatar($newFilename);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('Registration successful! Welcome, %email%', ['%email%' => $user->getEmail()]));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'isGranted' => $this->security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/register/freelance', name: 'app_register_freelance')]
    public function register_freelance(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());
            $user->setFreelancer(1);
            $user->setRoles(['ROLE_FREELANCE']);
            $avatarFile = $form->get('avatar')->getData();

            if ($avatarFile) {
                // Generate a unique filename
                $newFilename = uniqid().'.'.$avatarFile->guessExtension();

                // Move the file to the desired directory
                $avatarFile->move(
                    $this->getParameter('kernel.project_dir').$this->avatarDirectory,
                    $newFilename
                );

                // Update the user's avatar path
                $user->setAvatar($newFilename);
            }else{
                $user->setAvatar("default-avatar.png");
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('Registration successful! Welcome, %email%', ['%email%' => $user->getEmail()]));

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'isGranted' => $this->security->isGranted('ROLE_USER'),
            'user' => $this->getUser(),
        ]);
    }
}

