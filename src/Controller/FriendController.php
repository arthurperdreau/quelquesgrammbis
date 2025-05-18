<?php

namespace App\Controller;

use App\Entity\FriendRequest;
use App\Entity\Friendship;
use App\Entity\Notification;
use App\Entity\Profile;
use App\Repository\FriendRequestRepository;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FriendController extends AbstractController
{
    #[Route('/friend/send/{id}', name: 'app_send_friend_request')]
    public function send(Profile $profile, EntityManagerInterface $manager, FriendRequestRepository $friendRequestRepository): Response
    {
        $sender = $this->getUser()->getProfile();
        $recipent = $profile;
        $friendRequest = new FriendRequest();
        $friendRequest->setSender($sender);
        $friendRequest->setRecipient($recipent);
        if(!$friendRequestRepository->findOneBy(['sender' => $sender, 'recipient' => $recipent]) && !$friendRequestRepository->findOneBy(['sender' => $recipent, 'recipient' => $sender]) && !$this->getUser()->getProfile()->isFriendsWith($sender))
        {
            $friendRequest->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($friendRequest);

            $notif=new Notification();
            $notif->setType(4);
            $notif->setNotifFriendRequest($friendRequest);
            $notif->setNotified($recipent);
            $notif->setCreatedAt(new \DateTimeImmutable());
            $notif->setSeen(false);
            $manager->persist($notif);
            $manager->flush();

        }

        return $this->redirectToRoute('app_posts');
    }

    #[Route('/friend/accept/{id}', name: 'app_accept_friend_request')]
    public function accept(FriendRequest $friendRequest, EntityManagerInterface $manager, FriendshipRepository $friendshipRepository): Response
    {
        if(!$friendRequest){return $this->redirectToRoute('app_myposts');}
        $friendship = new Friendship();
        $friendship->setPersonA($friendRequest->getSender());
        $friendship->setPersonB($friendRequest->getRecipient());

        if(!$friendshipRepository->findOneBy(['personA'=> $friendship->getPersonA(), 'personB'=> $friendship->getPersonB()])
            &&
            !$friendshipRepository->findOneBy(['personB'=> $friendship->getPersonA(), 'personA'=> $friendship->getPersonB()])
        ){
            $friendship->setCreatedAt(new \DateTimeImmutable());
            $manager->remove($friendRequest);
            $manager->persist($friendship);
            $manager->flush();
        }



        return $this->redirectToRoute('app_myposts');
    }

    #[Route('/fiend/decline/{id}', name: 'app_decline_friend_request')]
    public function decline(FriendRequest $friendRequest, EntityManagerInterface $manager): Response
    {
        if(!$friendRequest){return $this->redirectToRoute('app_myposts');}

        $manager->remove($friendRequest);

        $manager->flush();

        return $this->redirectToRoute('app_myposts');
    }

}
