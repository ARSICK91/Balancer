<?php
namespace App\Form;

use App\Entity\Machine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MachineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название машины',
                'attr' => ['placeholder' => 'Введите название'],
                'required' => true,
            ])
            ->add('total_memory', IntegerType::class, [
                'label' => 'Общая память',
                'attr' => ['placeholder' => 'Введите общий объем памяти'],
                'required' => true, 
            ])
            ->add('total_core', IntegerType::class, [
                'label' => 'Общее количество ядер',
                'attr' => ['placeholder' => 'Введите количество ядер'],
                'required' => true, 
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Machine::class,
        ]);
    }
}
