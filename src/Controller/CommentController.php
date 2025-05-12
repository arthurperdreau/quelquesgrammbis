<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentForm;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name: 'app_new_comment')]
    public function index(Request $request, EntityManagerInterface $manager, Post $post): Response
    {
        if(!$this->getUser() || !$post){
            return $this->redirectToRoute('app_login');
        }
        $comment = new Comment();
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $manager->persist($comment);
            $manager->flush();
        }

        return $this->render('comment/index.html.twig', [ 'post' => $post, 'id' => $post->getId(),
            'form' => $form->createView()]);
    }

    #[Route('/comment/delete/{id}', name: 'app_delete_comment', priority: -1)]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {
        if(!$this->getUser() || !$comment){
            return $this->redirectToRoute('app_login');
        }
        $post = $comment->getPost();

        if($comment->getAuthor() === $this->getUser()){
            $manager->remove($comment);
            $manager->flush();
        }

        return $this->redirectToRoute('app_new_comment', ['post' => $post, 'id' => $post->getId()]);
    }

    #[Route('/comment/edit/{id}', name: 'app_edit_comment')]
    public function edit(Request $request, EntityManagerInterface $manager, Comment $comment): Response
    {
        if(!$this->getUser() || !$comment){
            return $this->redirectToRoute('app_login');
        }
        if($this->getUser() !== $comment->getAuthor()){
            return $this->redirectToRoute("app_posts");
        }
        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('app_new_comment', ['id' => $comment->getPost()->getId(),
                'post' => $comment->getPost()]);
        }
        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
