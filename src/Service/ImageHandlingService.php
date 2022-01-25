<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageHandlingService
{
    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @param ParameterBagInterface $params
     * @param SluggerInterface $slugger
     */
    public function __construct(ParameterBagInterface $params, SluggerInterface $slugger)
    {
        $this->params = $params;
        $this->slugger = $slugger;
    }

    /**
     * @param UploadedFile|null $image
     * @return string
     */
    public function getUploadedImageName(UploadedFile $image = null): string
    {
        if (null !== $image) {
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = uniqid() . '-' . $safeFilename . '.' . $image->guessExtension();
            $image->move($this->params->get('img_route'), $newFilename);

            return $newFilename;
        }

        return '';
    }

    /**
     * @param string|null $name
     * @return void
     */
    public function deleteImage(string $name = null): void
    {
        if (null !== $name) {
            $fullPath = $this->params->get('img_route') . '/' . $name;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}