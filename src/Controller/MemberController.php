<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Member;
use App\Form\MemberType;
use App\Repository\MemberRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/member')]
final class MemberController extends AbstractController
{
    #[Route(name: 'app_member_index', methods: ['GET'])]
    public function index(MemberRepository $memberRepository): Response
    {
        return $this->render('member/index.html.twig', [
            'members' => $memberRepository->findAll(),
        ]);
    }

    #[Route('/club/{id}/handle-join-request', name: 'club_handle_join_request', methods: ['POST'])]
    public function handleJoinRequest(
        Request $request,
        Club $club,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
        $userId = $request->request->get('user_id');
        $action = $request->request->get('action'); // 'accept' or 'reject'

        $user = $userRepository->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('User not found.');
        }

        $joinRequests = $club->getJoinRequest();
        $userRequest = null;

        foreach ($joinRequests as $req) {
            if (($req['user_id'] ?? null) == $userId) {
                $userRequest = $req;
                break;
            }
        }

        if (!$userRequest) {
            return new JsonResponse(['error' => 'User is not in join request list.'], Response::HTTP_BAD_REQUEST);
        }

        if ($action === 'accept') {
            $member = new Member();
            $member->setEmail($user->getEmail());
            $member->setPassword($user->getPassword());
            $member->setRoles($user->getRoles());
            $member->setUsername($user->getUsername());
            $member->setJoinAt(new \DateTime());
            $member->setClub($club);
            $member->setClubRole('member');

            $em->remove($user);
            $em->flush(); // Important: remove before persisting new entity

            $em->persist($member);
        }


        // Remove the join request (whether accepted or rejected)
        $updatedRequests = array_filter($joinRequests, fn ($req) => ($req['user_id'] ?? null) != $userId);
        $club->setJoinRequest(array_values($updatedRequests));

        $em->persist($club);
        $em->flush();

        $message = $action === 'accept' ? 'accepted' : 'rejected';
        return new JsonResponse(['success' => "User has been $message."]);
    }



    #[Route('/{id}', name: 'app_member_show', methods: ['GET'])]
    public function show(Member $member): Response
    {
        return $this->render('member/show.html.twig', [
            'member' => $member,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_member_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Member $member, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MemberType::class, $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_member_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('member/edit.html.twig', [
            'member' => $member,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_member_delete', methods: ['POST'])]
    public function delete(Request $request, Member $member, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $member->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($member);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_member_index', [], Response::HTTP_SEE_OTHER);
    }
}
