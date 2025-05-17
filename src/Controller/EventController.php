<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Member;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController
{
    // Admin route: List events (only for admins)
    #[Route('/admin/event', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    // Admin route: Create new event (only for admins)
    #[Route('admin/event/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Create a safe name for the file
                $fileName = uniqid('', true) . '.' . $imageFile->guessExtension();

                // Move the file to the uploads folder
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $fileName
                );

                // Save the file name into the database
                $event->setImage($fileName);
            }
            if ($event->getSubscriber() === null) {
                $event->setSubscriber([]);
            }
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }


    // Admin route: Edit event (only for admins)
    #[Route('admin/event/edit/{id}', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Create a safe name for the file
                $fileName = uniqid('', true) . '.' . $imageFile->guessExtension();

                // Move the file to the uploads folder
                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $fileName
                );

                // Save the file name into the database
                $event->setImage($fileName);
            }
            if ($event->getSubscriber() === null) {
                $event->setSubscriber([]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    // Admin route: Delete event (only for admins)
    #[Route('admin/event/delete/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('admin/event/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
    // Public route: List events (accessible to everyone)
    #[Route('/events', name: 'app_event_list_public', methods: ['GET'])]
    public function listPublic(EventRepository $eventRepository): Response
    {
        return $this->render('event/list_public.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    // Public route: Show event details (accessible to everyone)
    #[Route('/event/{id}', name: 'app_event_show_public', methods: ['GET'])]
    public function showPublic(Event $event): Response
    {
        return $this->render('event/show_public.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/event/{id}/subscribe', name: 'event_subscribe')]
    public function subscribe(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $isMemberOfClub = false;

        $defaultData = [];

        if ($user) {
            $defaultData = [
                'username' => $user->getUsername(),
                'email' => $user->getEmail()
            ];

            if ($user instanceof Member && $user->getClub() === $event->getClub()) {
                $isMemberOfClub = true;
            }
        } else {
            $this->addFlash('danger', 'You have to login for subscription.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createFormBuilder($defaultData)
            ->add('username', TextType::class, ['disabled' => $user !== null])
            ->add('email', TextType::class, ['disabled' => $user !== null])
            ->add('submit', SubmitType::class, [
                'label' => 'Subscribe to Event',
                'attr' => ['class' => 'btn btn-success mt-3']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $subscribers = $event->getSubscriber() ?? [];

            $alreadySubscribed = false;

            foreach ($subscribers as $s) {
                if ($user && isset($s['user_id']) && $s['user_id'] === $user->getId()) {
                    $alreadySubscribed = true;
                    break;
                } elseif (!$user && isset($s['email']) && $s['email'] === $data['email']) {
                    $alreadySubscribed = true;
                    break;
                }
            }

            if (!$alreadySubscribed) {
                $subscribers[] = [
                    'user_id' => $user?->getId(),
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'isMemberOfClub' => $isMemberOfClub
                ];

                $event->setSubscriber($subscribers);
                $entityManager->persist($event);
                $entityManager->flush();

                $this->addFlash('success', 'You have successfully subscribed to this event.');
            } else {
                $this->addFlash('warning', 'You are already subscribed to this event.');
            }

            return $this->redirectToRoute('app_event_show_public', ['id' => $event->getId()]);
        }

        return $this->render('event/subscribe.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'isMemberOfClub' => $isMemberOfClub,
        ]);
    }
}
