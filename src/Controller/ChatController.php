<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\Profile;
use App\Form\MessageForm;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\VarFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ChatController extends AbstractController
{
    #[Route('/chat/new/{id}', name: 'app_chat_create')]
    public function createChat(Profile $profile, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if(!$profile){return $this->redirectToRoute('app_myposts');}

        $conversation = $this->getUser()->getProfile()->isChattingWith($profile);

        if(!$conversation){
            $conversation = new Conversation();
            $conversation->addParticipant($this->getUser()->getProfile());
            $conversation->addParticipant($profile);
            $conversation->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($conversation);
            $manager->flush();
        }


        return $this->redirectToRoute('app_chat', ['id' => $conversation->getId() ]);
    }

    #[Route('/chat/{id}', name: 'app_chat')]
    public function chat(Conversation $chat, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        if(!$chat){return $this->redirectToRoute('app_myposts');}

        $message = new Message();
        $form = $this->createForm(MessageForm::class, $message);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $message->setAuthor($this->getUser()->getProfile());
            $message->setConversation($chat);
            $message->setCreatedAt(new \DateTimeImmutable());
            $message->setType(1);
            $manager->persist($message);

            $recipient=null;
            foreach ($chat->getParticipants() as $participant) {
                if($participant != $this->getUser()->getProfile()) {

                    $recipient = $participant;
                }
            }


            $notif=new Notification();
            $notif->setType(3);
            $notif->setNotifMessage($message);
            $notif->setNotified($recipient);
            $notif->setCreatedAt(new \DateTimeImmutable());
            $notif->setSeen(false);
            $manager->persist($notif);

            $manager->flush();
            return $this->redirectToRoute('app_chat', ['id' => $chat->getId()]);
        }



        return $this->render('chat/index.html.twig', [
            'chat' => $chat,
            'form' => $form,
        ]);
    }
}
