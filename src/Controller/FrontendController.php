<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FrontendController extends AbstractController
{
    /**
     * @Route("/", name="index")
     *
     * @param ArticleRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(ArticleRepository $repository): \Symfony\Component\HttpFoundation\Response
    {
        $articles = $repository->findPublished();
        return $this->render('article/list.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/{id}", name="view")
     *
     * @param Request $request
     * @param int $id
     * @param ArticleRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, int $id, ArticleRepository $repository): \Symfony\Component\HttpFoundation\Response
    {
        if (!$article = $repository->find($id)) {
            throw new NotFoundHttpException('Article not found');
        }

        return $this->render('article/view.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/comment/{articleId}", name="comment")
     *
     * @param Request $request
     * @param int $articleId
     * @param EntityManagerInterface $em
     * @param ArticleRepository $articleRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function commentAction(
        Request $request,
        int $articleId,
        EntityManagerInterface $em,
        ArticleRepository $articleRepository
    ): \Symfony\Component\HttpFoundation\Response {
        if (!$article = $articleRepository->find($articleId)) {
            throw new NotFoundHttpException('Article does not exist');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setPostedDate(new \DateTime());
            $comment->setArticle($article);
            $article->addComment($comment);

            $em->persist($comment);
            $em->persist($article);

            $em->flush();

            return $this->redirectToRoute('view', ['id' => $articleId]);
        }

        return $this->render(
            'article/comment-form.html.twig',
            ['form' => $form->createView(), 'articleId' => $articleId]
        );
    }
}