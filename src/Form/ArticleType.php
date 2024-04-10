<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // No need for SubmitType, handle by CRUD template
        $builder
        ->add('title', TextType::class, [
            'required' => true,
            'attr' => ['placeholder' => 'Title of your article...']
        ])
        ->add('date', TextType::class, [
            'required' => true,
            'attr' => ['placeholder' => 'DD/MM/YYYY']
        ])
        ->add('content', TextareaType::class, [
            'required' => true,
            'attr' => ['placeholder' => 'Write your article here...']
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
