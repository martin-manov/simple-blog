<?php

namespace App\Service;

use App\Entity\Article;
use Symfony\Component\Form\FormInterface;

class ArticleService
{
    /**
     * @var ImageHandlingService
     */
    private $imageHandler;

    /**
     * @param ImageHandlingService $imageHandler
     */
    public function __construct(ImageHandlingService $imageHandler)
    {
        $this->imageHandler = $imageHandler;
    }

    /**
     * Set article data from form
     *
     * @param Article $article
     * @param FormInterface $form
     * @return void
     */
    public function setArticleDate(Article $article, FormInterface $form): void
    {
        $datePublished = $form['published']->getData() ? new \DateTime() : null;
        $article->setUpdateDate(new \DateTime());
        $article->setPublishDate($datePublished);

        $imageFile = $form->get('image')->getData();
        $newFilename = $this->imageHandler->getUploadedImageName($imageFile);

        // Old image won't be deleted
        if ('' !== $newFilename) {
            $article->setImage($newFilename);
        }
    }
}