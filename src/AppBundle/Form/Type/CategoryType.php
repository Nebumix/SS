<?php
namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('parent', EntityType::class, array(
                'class' => 'AppBundle:Category',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.parentId IS NULL')
                        ->orderBy('c.name', 'ASC');
                },

                'placeholder' => 'Nessuna categoria genitore',
                'required' => false,
            ))
/*            ->add('parent', EntityType::class, array(
                'class'       => 'AppBundle:Category',
                'placeholder' => 'Nessuna categoria genitore',

            ))*/
            ->add('save', SubmitType::class)
        ;
    }
}