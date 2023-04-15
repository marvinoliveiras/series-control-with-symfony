<?php

namespace App\Form;

use App\DTO\SeriesCreationInputDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SeriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('seriesName')
            ->add('seasonsQuantity',
                type: NumberType::class,
                options: ['label' => 
                    'Quantity of Seasons'
            ])->add('episodesPerSeason',
                type: NumberType::class,
                options: ['label' => 
                    'Episodes per season'
            ])->add('coverImage',
                type: FileType::class,
                options: [
                    'label' => "Cover Image",
                    'mapped' => false,
                    'required' => false,
                    'constraints' => new File(mimeTypes: 'image/*')
            ])->add('save',
                SubmitType::class,
                ['label' => $options['is_edit']
                    ? 'Edit'
                    : 'Save'
                ]
            )->setMethod($options['is_edit']
            ? 'PATCH'
            : 'POST'
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SeriesCreationInputDTO::class,
            'is_edit' => false
        ]);
    }
}
