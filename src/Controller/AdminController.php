<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Type\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\ArticleService;
use App\Service\ImageHandlingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     *
     * @param ArticleRepository $repository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();
        return $this->render('article/admin/list.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/create", name="create", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param ArticleService $articleService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function createArticleAction(
        Request $request,
        EntityManagerInterface $em,
        ArticleService $articleService
    ) {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $article->setCreationDate(new \DateTime());
            $articleService->setArticleDate($article, $form);

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render(
            'article/admin/form.html.twig',
            ['form' => $form->createView(), 'article' => $article]
        );
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param int $id
     * @param EntityManagerInterface $em
     * @param ArticleRepository $repository
     * @param ArticleService $articleService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editAction(
        Request $request,
        int $id,
        EntityManagerInterface $em,
        ArticleRepository $repository,
        ArticleService $articleService
    ) {
        if (!$article = $repository->find($id)) {
            throw new NotFoundHttpException('Article not found');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->get('published')->setData(null !== $article->getPublishDate());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $articleService->setArticleDate($article, $form);

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('list');
        }

        return $this->render(
            'article/admin/form.html.twig',
            ['form' => $form->createView(), 'article' => $article]
        );
    }

    /**
     * @Route("/delete/{id}", name="delete")
     *
     * @param int $id
     * @param EntityManagerInterface $em
     * @param ArticleRepository $repository
     * @param ImageHandlingService $imageHandlingService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(
        int $id,
        EntityManagerInterface $em,
        ArticleRepository $repository,
        ImageHandlingService $imageHandlingService
    ): RedirectResponse {
        if (!$article = $repository->find($id)) {
            throw new NotFoundHttpException('Article not found');
        }

        $imageHandlingService->deleteImage($article->getImage());
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('list');
    }
}