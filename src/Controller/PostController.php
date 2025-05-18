<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Notification;
use App\Entity\Post;
use App\Form\ImageForm;
use App\Form\PostForm;
use App\Form\ProfileForm;
use App\Form\SearchForm;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_posts')]
    public function index(PostRepository $postRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts' => $postRepository->findAll(),
            //'myposts'=>$postRepository->findBy(['author'=> $this->getUser()]),

        ]);
    }

    #[Route('/posts/myposts', name: 'app_myposts')]
    public function showmyposts(PostRepository $postRepository, Request $request, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        $profile = $this->getUser()->getProfile();
        $form = $this->createForm(ProfileForm::class, $profile);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($profile);
            $manager->flush();
            return $this->redirectToRoute('app_myposts');
        }
        return $this->render('post/myposts.html.twig', [
            'myposts' => $postRepository->findBy(['author' => $this->getUser()]),
            'form' => $form->createView(),
        ]);

    }



    #[Route('/post/new', name: 'app_new_post', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $post = new Post();
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setAuthor($this->getUser());
            $post->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($post);
            foreach ($this->getUser()->getProfile()->getFriends() as $friend) {
                $notif=new Notification();
                $notif->setType(1);
                $notif->setNotifPost($post);
                $notif->setNotified($friend);
                $notif->setCreatedAt(new \DateTimeImmutable());
                $notif->setSeen(false);
                $manager->persist($notif);
            }
            $manager->flush();

            return $this->redirectToRoute('app_posts',);
        }

        return $this->render('post/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/post/delete/{id}', name: 'app_delete_post', priority: -1)]
    public function delete(Post $post, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $post->getAuthor()) {
            return $this->redirectToRoute('app_posts');
        }
        if($post->getComments()->count() > 0){
            foreach ($post->getComments() as $comment) {
                $manager->remove($comment);
            }
        }
        if($post->getImages()->count() > 0){
            foreach ($post->getImages() as $image) {
                $manager->remove($image);
            }
        }

        if($post->getLikes()->count() > 0){
            foreach ($post->getLikes() as $like) {
                $manager->remove($like);
            }
        }

        $manager->remove($post);
        $manager->flush();

        return $this->redirectToRoute('app_myposts');

    }

    #[Route('/post/edit/{id}', name: 'app_edit_post')]
    public function edit(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $post->getAuthor()) {
            return $this->redirectToRoute('app_posts');
        }

        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('app_posts');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/addimage/{id}', name: 'post_image')]
    public function addImage(Post $post, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $post->getAuthor()) {
            return $this->redirectToRoute('app_posts');
        }

        $image = new Image();
        $form = $this->createForm(ImageForm::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setPost($post);
            $manager->persist($image);
            $manager->flush();

            return $this->redirectToRoute('post_image', ['id' => $post->getId()]);
        }

        return $this->render('post/images.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/remove-image/{id}', name: 'remove_image')]
    public function removeImage(Image $image, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $image->getPost()->getAuthor() ) {
            return $this->redirectToRoute('posts');
        }

        $postId = $image->getPost()->getId();

        $manager->remove($image);
        $manager->flush();

        return $this->redirectToRoute('post_image', ['id' => $postId]);
    }


    #[Route('/post/search', name: 'app_search_post')]
    public function searchpost(SearchForm $searchForm, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(SearchForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->get('post')->getData();
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }
        return $this->render('post/search.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/post/show/{id}', name: 'post_show', priority: -1)]
    public function show(Post $post)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);

    }
}
