<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(NotificationRepository $notificationRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('notification/index.html.twig', [
            'notifs' =>$notificationRepository-> findBy(['notified' => $this->getUser()->getProfile(), 'seen' => false]),
        ]);
    }

    #[Route('/notification/{id}/seen', name: 'app_notification_seen')]
    public function hideNotification(Notification $notification, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $notification->setSeen(true);
        $manager->flush();
        return $this->redirectToRoute('app_notification');

    }
}
