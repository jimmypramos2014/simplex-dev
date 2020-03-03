<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TransferenciaDineroType extends AbstractType
{

    protected $session;
    protected $em;

    public function __construct(SessionInterface $session,EntityManagerInterface $em)
    {
        $this->session  = $session;
        $this->em       = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salida',EntityType::class,array(
                'class' => 'AppBundle:CajaCuentaBanco',
                'attr'=> array('class' => 'form-control '),
                'label'         => 'Salida',
                'required'      => true,
                'placeholder'   => 'Seleccionar..',
                'query_builder' => function(EntityRepository $er) use ($options)
                {
                    $qb = $er->createQueryBuilder('ccb');
                    $qb->leftJoin('ccb.empresa','e');
                    $qb->where('e.id = '.$options['empresa']);

                    return $qb;

                }                   
                ))
            ->add('entrada',EntityType::class,array(
                'class' => 'AppBundle:CajaCuentaBanco',
                'attr'=> array('class' => 'form-control '),
                'label'         => 'Entrada',
                'required'      => true,
                'placeholder'   => 'Seleccionar..',
                'query_builder' => function(EntityRepository $er) use ($options)
                {
                    $qb = $er->createQueryBuilder('ccb');
                    $qb->leftJoin('ccb.empresa','e');
                    $qb->where('e.id = '.$options['empresa']);

                    return $qb;

                }                   
                ))                                        
            ->add('monto',null,array(
                'attr'=> array('class' => 'form-control solonumeros'),
                'label'         => 'Monto a transferir',
                'required'      => true,
                ))                                 
            ;


    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TransferenciaDinero',
            'empresa' => ''
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_transferencia_dinero';
    }


}
