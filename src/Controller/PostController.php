<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\ImageOptimizer;
use App\Service\Like;
use App\Service\Uploaders\PostPictureUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/posts')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, Like $like): Response
    {
        $userLikes = $like->getUserLikesIds($this->getUser());

        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
            'userLikes' => $userLikes,
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository, PostPictureUploader $postPictureUploader, ImageOptimizer $imageOptimizer): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('filename')->getData();
            if ($pictureFile) {
                $pictureFileName = $postPictureUploader->upload($pictureFile);
                $imageOptimizer->resize($this->getParameter("app.post_pictures_directory") . $pictureFileName);
                $post->setFilename($pictureFileName);
                $post->setUser($this->getUser());
                $post->setCreatedAt(new \DateTimeImmutable('now'));
                $post->setUpdatedAt(new \DateTimeImmutable('now'));
            }


            $postRepository->add($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET'])]
    public function show(Post $post, Like $like): Response
    {
        $likesQuantity = $like->countPostLikes($post);
        $userLikes = $like->getUserLikesIds($this->getUser());

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'likesQuantity' => $likesQuantity,
            'userLikes' => $userLikes,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository, PostPictureUploader $postPictureUploader, ImageOptimizer $imageOptimizer): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('filename')->getData();
            if ($pictureFile) {
                $pictureFileName = $postPictureUploader->upload($pictureFile);
                $imageOptimizer->resize($this->getParameter("app.post_pictures_directory") . $pictureFileName);
                $postPictureUploader->delete($post->getFilename());
                $post->setFilename($pictureFileName);
                $post->setUpdatedAt(new \DateTimeImmutable('now'));
            }

            $postRepository->add($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository, PostPictureUploader $postPictureUploader): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $postPictureUploader->delete($post->getFilename());
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/like/{id}', name: 'app_post_like', methods: ['GET'])]
    public function like(Post $post, Like $like, Request $request): Response
    {
        $like->likePost($post);
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);;
    }

    #[Route('/unlike/{id}', name: 'app_post_unlike', methods: ['GET'])]
    public function unlike(Post $post, Like $like, Request $request): Response
    {
        $like->unlikePost($post);
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

}
