<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Member;
use App\Entity\User;
use App\Form\ClubType;
use App\Repository\ClubRepository;
use App\Repository\EventRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ClubController extends AbstractController
{
    // Admin route: List clubs (only for admins)
    #[Route('/admin/club', name: 'app_club_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ClubRepository $clubRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        $clubs = $search
            ? $clubRepository->createQueryBuilder('c')
            ->where('c.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult()
            : $clubRepository->findAll();

        return $this->render('club/index.html.twig', ['clubs' => $clubs]);
    }

    // Admin route: Create new club (only for admins)
    #[Route('/admin/club/new', name: 'app_club_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $club = new Club();
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $fileName = uniqid('', true) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $fileName
                );
                $club->setImage($fileName);
            }
            $entityManager->persist($club);
            $entityManager->flush();

            return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('club/new.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
        ]);
    }

    // Admin route: Edit club (only for admins)
    #[Route('/admin/club/{id}/edit', name: 'app_club_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Club $club, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClubType::class, $club);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $fileName = uniqid('', true) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $fileName
                );
                $club->setImage($fileName);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('club/edit.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
        ]);
    }

    // Admin route: Delete club (only for admins)
    #[Route('/admin/club/{id}', name: 'app_club_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Club $club, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $club->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($club);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_club_index', [], Response::HTTP_SEE_OTHER);
    }

    // Admin route: Show club details (only for admins)
    #[Route('/admin/club/{id}', name: 'app_club_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Club $club): Response
    {
        return $this->render('club/show.html.twig', [
            'club' => $club,
        ]);
    }

    // Public route: List clubs (accessible to everyone)
    #[Route('/clubs', name: 'app_club_show2', methods: ['GET'])]
    public function show2(ClubRepository $clubRepository): Response
    {
        return $this->render('club/club.html.twig', [
            'clubs' => $clubRepository->findAll(),
        ]);
    }

    // Public route: Show club details (accessible to everyone)
    #[Route('/club/{id}', name: 'app_club_show_details', methods: ['GET'])]
    public function showDetails(Club $club, EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(['club' => $club]);
        return $this->render('club/show_details.html.twig', [
            'club' => $club,
            'events' => $events,
        ]);
    }
    #[Route('/clubs/{id}/join', name: 'app_join_club')]
    public function joinClub(
        Request $request,
        ClubRepository $clubRepository,
        EntityManagerInterface $em,
        Security $security,
        int $id
    ): Response {
        $club = $clubRepository->find($id);
        if (!$club) {
            throw $this->createNotFoundException('Club not found.');
        }

        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to join a club.');
        }

        // Initialize joinRequests if null
        $joinRequests = $club->getJoinRequest() ?? [];

        // ✅ Rename variable inside the loop to avoid conflict
        $hasPendingRequest = false;
        foreach ($joinRequests as $joinRequest) {
            if (($joinRequest['user_id'] ?? null) == $user->getId() &&
                ($joinRequest['status'] ?? null) === 'pending'
            ) {
                $hasPendingRequest = true;
                break;
            }
        }

        if ($hasPendingRequest) {
            $this->addFlash('danger', 'You have already submitted a request to join this club.');
            return $this->redirectToRoute('app_club_show_details', ['id' => $id]);
        }

        $form = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'data' => $user->getUsername(),
                'disabled' => true,
            ])
            ->add('email', EmailType::class, [
                'data' => $user->getEmail(),
                'disabled' => true,
            ])
            ->add('department', ChoiceType::class, [
                'label' => 'Department',
                'choices' => [
                    'Information Technology' => 'IT',
                    'Mechanical Engineering' => 'MC',
                    'Electrical Engineering' => 'EC',
                    'Management' => 'MG'
                ],
                'placeholder' => 'Select your department',
                'required' => true,
            ])
            ->add('class', TextType::class, [
                'label' => 'Class (e.g., 3IT2)',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter your class',
                    'pattern' => '^\d{1}[A-Za-z]{2}\d{1}$',
                    'title' => 'Please enter class in format like 3IT2'
                ]
            ])
            ->add('reason', TextareaType::class, [
                'label' => 'Why do you want to join?',
                'required' => true,
                'attr' => ['rows' => 4]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send Join Request',
                'attr' => ['class' => 'btn btn-primary']
            ])
            ->getForm();

        $form->handleRequest($request); // ✅ Now $request is still the Symfony Request object
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $newRequest = [
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'department' => $formData['department'],
                'class' => $formData['class'],
                'reason' => $formData['reason'],
                'status' => 'pending',
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
                'updated_at' => null
            ];

            $club->addJoinRequest($newRequest);

            $em->persist($club);
            $em->flush();

            $this->addFlash('success', 'Your request to join the club has been submitted.');
            return $this->redirectToRoute('app_club_show2', ['id' => $id]);
        }

        return $this->render('club/join_club.html.twig', [
            'form' => $form->createView(),
            'club' => $club,
            'hasPendingRequest' => $hasPendingRequest
        ]);
    }


    #[Route('/club/{id}/manage', name: 'app_club_manage')]
    #[IsGranted('ROLE_USER')]
    public function manageClub(
        Club $club,
        MemberRepository $memberRepository,
        EventRepository $eventRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        // Check if user is a manager of this specific club
        $isManager = false;
        if ($user instanceof Member && $user->isManager() && $user->getClub() === $club) {
            $isManager = true;
        }

        if (!$isManager) {
            throw $this->createAccessDeniedException('Only managers of this club can access this page');
        }

        $members = $memberRepository->findBy(['club' => $club]);

        $upcomingEvents = $eventRepository->findBy([
            'club' => $club,
            'status' => 'Upcoming'
        ]);

        $joinRequests = $club->getJoinRequest();

        return $this->render('club/manage.html.twig', [
            'upcomingEvents' => $upcomingEvents,
            'club' => $club,
            'members' => $members,
            'joinRequests' => $joinRequests,
        ]);
    }

    
}