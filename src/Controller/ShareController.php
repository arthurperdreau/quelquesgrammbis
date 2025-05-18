<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\Profile;
use App\Entity\Share;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShareController extends AbstractController
{
    #[Route('/sharewith/{id}', name: 'app_share_post')]
    public function shareWith(Post $post): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if(!$post){
            return $this->redirectToRoute('app_posts');
        }
        return $this->render('share/index.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/share/{id}/{recipientId}', name: 'app_share')]
    public function share(Post $post, $recipientId, ProfileRepository $profileRepository, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if(!$post){
            return $this->redirectToRoute('app_posts');
        }

        $recipient = $profileRepository->find($recipientId);

        $share = new Share();
        $share->setSharedPost($post);
        $share->setSender($this->getUser()->getProfile());
        $share->setRecipient($recipient);
        $share->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($share);
        $manager->flush();
        $conversation = $this->getUser()->getProfile()->isChattingWith($recipient);

        if(!$conversation){
            $conversation = new Conversation();
            $conversation->addParticipant($this->getUser()->getProfile());
            $conversation->addParticipant($recipient);
            $conversation->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($conversation);
            $manager->flush();
        }
        $message= new Message();
        $message->setAuthor($this->getUser()->getProfile());
        $message->setConversation($conversation);
        $message->setContent(strval($post->getId()));
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setType(2);
        $manager->persist($message);



        $manager->flush();
        return $this->redirectToRoute('app_share_post', ['id' => $post->getId()]);

    }
}
