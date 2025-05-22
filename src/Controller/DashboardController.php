<?php

namespace App\Controller;

use App\Repository\ClubRepository;
use App\Repository\EventRepository;
use App\Repository\MemberRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_dashboard')]
    public function index(
        ClubRepository $clubRepository,
        EventRepository $eventRepository,
        MemberRepository $memberRepository,
        UserRepository $userRepository
    ): Response {
        // Club statistics
        $clubs = $clubRepository->findAll();
        $activeClubs = $clubRepository->count(['status' => 'Active']);
        $inactiveClubs = $clubRepository->count(['status' => 'Inactive']);

        // Event statistics
        $events = $eventRepository->findAll();
        $upcomingEvents = $eventRepository->count(['status' => 'Upcoming']);
        $ongoingEvents = $eventRepository->count(['status' => 'Ongoing']);
        $completedEvents = $eventRepository->count(['status' => 'Completed']);

        // Member statistics
        $totalMembers = $memberRepository->count([]);
        $totalUsers = $userRepository->count([]);

        // Prepare data for Google Charts
        $clubStatusData = [
            ['Status', 'Count'],
            ['Active', $activeClubs],
            ['Inactive', $inactiveClubs],
        ];

        $eventStatusData = [
            ['Status', 'Count'],
            ['Upcoming', $upcomingEvents],
            ['Ongoing', $ongoingEvents],
            ['Completed', $completedEvents],
        ];

        // Get popular clubs (top 5 by member count)
        $popularClubs = $clubRepository->findBy([], ['members' => 'DESC'], 5);
        $popularClubsData = [['Club', 'Members']];
        foreach ($popularClubs as $club) {
            $popularClubsData[] = [$club->getName(), $club->getMembers()];
        }

        // Monthly events data (for line chart)
        $monthlyEventsData = [['Month', 'Events']];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Simulate monthly data (in a real app, you would query this from the database)
        foreach ($months as $index => $month) {
            // Random number between 1 and 10 for demo purposes
            $monthlyEventsData[] = [$month, rand(1, 10)];
        }

        return $this->render('dashboard/index.html.twig', [
            'clubStatusData' => json_encode($clubStatusData),
            'eventStatusData' => json_encode($eventStatusData),
            'popularClubsData' => json_encode($popularClubsData),
            'monthlyEventsData' => json_encode($monthlyEventsData),
            'totalClubs' => count($clubs),
            'totalEvents' => count($events),
            'totalMembers' => $totalMembers,
            'totalUsers' => $totalUsers,
        ]);
    }
}