<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\User;
use App\Form\UserProfileType;
use App\Form\ChangePasswordType;
use App\Repository\ClubRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/user/dashboard')]
class UserDashboardController extends AbstractController
{
    #[Route('', name: 'app_user_dashboard')]
    public function index(
        ClubRepository $clubRepository,
        EventRepository $eventRepository
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        // Get joined clubs
        $joinedClubs = [];
        if ($user instanceof Member) {
            $joinedClubs[] = $user->getClub();
        }

        // Get upcoming events the user is subscribed to
        $upcomingEvents = [];
        $pastEvents = [];
        $allEvents = $eventRepository->findAll();

        foreach ($allEvents as $event) {
            $subscribers = $event->getSubscriber() ?? [];
            $isSubscribed = false;

            foreach ($subscribers as $subscriber) {
                if (isset($subscriber['user_id']) && $subscriber['user_id'] == $user->getId()) {
                    $isSubscribed = true;
                    break;
                }
            }

            if ($isSubscribed) {
                $eventDate = $event->getStartDate();
                $today = new \DateTime();

                if ($eventDate > $today) {
                    $upcomingEvents[] = $event;
                } else {
                    $pastEvents[] = $event;
                }
            }
        }

        // Get pending join requests
        $pendingRequests = [];
        $clubs = $clubRepository->findAll();

        foreach ($clubs as $club) {
            $joinRequests = $club->getJoinRequest() ?? [];

            foreach ($joinRequests as $request) {
                if (isset($request['user_id']) && $request['user_id'] == $user->getId() &&
                    isset($request['status']) && $request['status'] === 'pending') {
                    $request['club'] = $club;
                    $pendingRequests[] = $request;
                }
            }
        }

        return $this->render('user_dashboard/index.html.twig', [
            'user' => $user,
            'joinedClubs' => $joinedClubs,
            'upcomingEvents' => $upcomingEvents,
            'pastEvents' => $pastEvents,
            'pendingRequests' => $pendingRequests,
        ]);
    }

    #[Route('/profile', name: 'app_user_dashboard_profile')]
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Your profile has been updated successfully.');
            return $this->redirectToRoute('app_user_dashboard_profile');
        }

        return $this->render('user_dashboard/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/change-password', name: 'app_user_dashboard_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Check if current password is correct
            if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $this->addFlash('danger', 'Current password is incorrect.');
                return $this->redirectToRoute('app_user_dashboard_change_password');
            }

            // Hash the new password
            $hashedPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            $this->addFlash('success', 'Your password has been changed successfully.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user_dashboard/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/leave-club/{id}', name: 'app_user_dashboard_leave_club')]
    public function leaveClub(
        int $id,
        ClubRepository $clubRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user instanceof Member) {
            $this->addFlash('danger', 'You are not a member of any club.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $club = $clubRepository->find($id);

        if (!$club) {
            throw $this->createNotFoundException('Club not found.');
        }

        if ($user->getClub()->getId() !== $club->getId()) {
            $this->addFlash('danger', 'You are not a member of this club.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        if ($user->getClubRole() === 'manager') {
            $this->addFlash('danger', 'As a manager, you cannot leave the club. Please contact an administrator.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        // Create a new User entity to replace the Member
        $newUser = new User();
        $newUser->setEmail($user->getEmail());
        $newUser->setUsername($user->getUsername());
        $newUser->setPassword($user->getPassword());
        $newUser->setRoles($user->getRoles());

        // Remove the Member and persist the new User
        $entityManager->remove($user);
        $entityManager->flush();

        $entityManager->persist($newUser);
        $entityManager->flush();

        // Update the session with the new user
        // Note: In a real application, you would need to handle this differently
        // as the user would be logged out when their entity is removed

        $this->addFlash('success', 'You have successfully left the club.');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/cancel-request/{clubId}', name: 'app_user_dashboard_cancel_request')]
    public function cancelRequest(
        int $clubId,
        ClubRepository $clubRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $club = $clubRepository->find($clubId);

        if (!$club) {
            throw $this->createNotFoundException('Club not found.');
        }

        $joinRequests = $club->getJoinRequest() ?? [];
        $updatedRequests = [];
        $requestFound = false;

        foreach ($joinRequests as $request) {
            if (isset($request['user_id']) && $request['user_id'] == $user->getId() &&
                isset($request['status']) && $request['status'] === 'pending') {
                $requestFound = true;
                continue;
            }
            $updatedRequests[] = $request;
        }

        if (!$requestFound) {
            $this->addFlash('danger', 'No pending request found for this club.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $club->setJoinRequest($updatedRequests);
        $entityManager->flush();

        $this->addFlash('success', 'Your join request has been cancelled.');
        return $this->redirectToRoute('app_user_dashboard');
    }
}
