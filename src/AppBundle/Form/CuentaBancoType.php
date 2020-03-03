<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CuentaBancoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('banco',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Entidad bancaria',
                'required'      => true,
                ))
            ->add('codigo',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Código',
                'required'      => false,
                ))
            ->add('numero',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Número de cuenta',
                'required'      => true,
                ))
            ->add('numeroInterbancario',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Código interbancario',
                'required'      => false,
                ))
            ->add('estado',HiddenType::class,array(
                'data'  => true,
                ))            
            ->add('moneda',null,array(
                'attr'=> array('class' => 'form-control','required'=>'required'),
                'label'         => 'Moneda',
                'placeholder'         => 'Seleccionar moneda',
                'required'      => false,
                ))
            ->add('mostrarEnComprobante',CheckboxType::class,array(
                'label'  => 'Mostrar en comprobante',
                'required'      => false,
                ));


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $cuentaBanco = $event->getData();
            $form = $event->getForm();


            if (!$cuentaBanco || null === $cuentaBanco->getId()) {
                $form
                    ->add('mostrarEnComprobante',CheckboxType::class,array(
                        'data'  => false,
                        'label'  => 'Mostrar en comprobante',
                        'required'  => false,
                        ));                    
            }
        });    

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CuentaBanco'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_cuentabanco';
    }


}
