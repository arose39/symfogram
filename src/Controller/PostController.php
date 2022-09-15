<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
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
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findBy([], ['created_at'=>"DESC"]),
            'userLikes' => $like->getUserLikesIds($this->getUser()),
            'currentUser' => $this->getUser(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PostRepository $postRepository, PostPictureUploader $postPictureUploader): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('filename')->getData();
            if ($pictureFile) {
                $pictureFileName = $postPictureUploader->upload($pictureFile);
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
    public function show(Post $post, Like $like, CommentRepository $commentRepository): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'likesQuantity' => $like->countPostLikes($post),
            'userLikes' => $like->getUserLikesIds($this->getUser()),
            'comments' => $commentRepository->findBy(['post' => $post]),
            'currentUser' => $this->getUser(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, PostRepository $postRepository, PostPictureUploader $postPictureUploader): Response
    {
        //Пользователь может редактировать только свой пост
        if ($this->getUser() !== $post->getUser()) {
            return $this->redirect($request->headers->get('referer'));
        }

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('filename')->getData();
            if ($pictureFile) {
                $pictureFileName = $postPictureUploader->upload($pictureFile);
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
        //Пользователь может удалять только свой пост
        if ($this->getUser() !== $post->getUser()) {
            return $this->redirect($request->headers->get('referer'));
        }
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

        return $this->redirect($referer);
    }

    #[Route('/unlike/{id}', name: 'app_post_unlike', methods: ['GET'])]
    public function unlike(Post $post, Like $like, Request $request): Response
    {
        $like->unlikePost($post);

        return $this->redirect($request->headers->get('referer'));
    }
}
