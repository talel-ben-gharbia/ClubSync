<?php

namespace App\Controller;

use App\Repository\ClubRepository;
use App\Repository\EventRepository;
use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard')]
    public function index(
        ClubRepository $clubRepository,
        EventRepository $eventRepository,
        MemberRepository $memberRepository
    ): Response {
        $clubs = $clubRepository->findAll();
        $activeClubs = $clubRepository->count(['status' => 'Active']);
        $inactiveClubs = $clubRepository->count(['status' => 'Inactive']);

        $events = $eventRepository->findAll();
        $upcomingEvents = $eventRepository->count(['status' => 'Upcoming']);
        $ongoingEvents = $eventRepository->count(['status' => 'Ongoing']);
        $completedEvents = $eventRepository->count(['status' => 'Completed']);

        $totalMembers = $memberRepository->count([]);

        $clubStatusData = [
            ['Status', 'Nombre'],
            ['Actifs', $activeClubs],
            ['Inactifs', $inactiveClubs],
        ];

        $eventStatusData = [
            ['Status', 'Nombre'],
            ['À venir', $upcomingEvents],
            ['En cours', $ongoingEvents],
            ['Terminés', $completedEvents],
        ];

        $popularClubs = $clubRepository->findBy([], ['members' => 'DESC'], 5);
        $popularClubsData = [['Club', 'Membres']];
        foreach ($popularClubs as $club) {
            $popularClubsData[] = [$club->getName(), $club->getMembers()];
        }

        return $this->render('dashboard/index.html.twig', [
            'clubStatusData' => json_encode($clubStatusData),
            'eventStatusData' => json_encode($eventStatusData),
            'popularClubsData' => json_encode($popularClubsData),
            'totalClubs' => count($clubs),
            'totalEvents' => count($events),
            'totalMembers' => $totalMembers,
        ]);
    }
}
