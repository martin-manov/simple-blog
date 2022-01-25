<?php

namespace App\Form\Type;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title'
            ])
            ->add('summary', TextType::class, [
                'label' => 'Short summary'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content'
            ])
            ->add('author', TextType::class, [
                'label' => 'Author'
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'label' => 'Select image',
                'required' => false,
                'multiple' => false,
                'attr' => [
                    'accept' => 'image/*',
                ]
            ])
            ->add('commentsEnabled', CheckboxType::class, [
                'label' => 'Allow comments?',
                'required' => false,
            ])
            ->add('published', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Publish article?',
                'required' => false,
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}