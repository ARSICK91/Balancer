<?php
namespace App\Form;

use App\Entity\Process;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProcessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('need_memory', IntegerType::class, [
                'label' => 'Требуемая память',
                'attr' => [
                    'placeholder' => 'Введите требуемый объем памяти: ',
                    'min' => 1,
                    'class' =>'int-input',

                ],
                'required' => true, 
            ])
            ->add('need_core', IntegerType::class, [
                'label' => 'Требуемое количество ядер',
                'attr' => [
                    'placeholder' => 'Введите требуемое количество ядер: ',
                    'min' => 1, 
                    'class' =>'int-input',

                ],
                'required' => true, 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Process::class,
        ]);
    }
}