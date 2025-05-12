<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Profile;
use App\Form\ImageForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{


    #[Route('/profile/addimage/{id}', name: 'app_addimageprofile')]
    public function addImageProfile( EntityManagerInterface $manager, Request $request, UserRepository $userRepository): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $image = new Image();
        $form = $this->createForm(ImageForm::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setProfile($this->getUser()->getProfile());

            $manager->persist($image);
            $manager->flush();

            return $this->redirectToRoute('app_myposts');
        }

        return $this->render('profile/addimage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/removepicture/{id}', name: 'add_removepictureprofile')]
    public function removeImage(Image $image, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $profile = $image->getProfile();
        if ($profile) {
            $profile->setPicture(null);
        }

        $manager->remove($image);
        $manager->flush();

        return $this->redirectToRoute('app_addimageprofile',['id' => $this->getUser()->getProfile()->getId()
        ]);
    }

    #[Route('/profile/show/{id}', name: 'app_showprofile', priority: -1)]
    public function showprofile(Profile $profile): Response
    {
        return $this->render('profile/showprofile.html.twig',[
            'profile' => $profile,
        ]);
    }
}
