<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TransaccionType extends AbstractType
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
            ->add('tipo',ChoiceType::class,array(
                'attr'=> array('class' => 'form-control','style'=>'pointer-events:none;'),
                'label'         => 'Tipo',
                'choices'       => array('Compra'=>'c','Venta'=>'v','Gasto'=>'g','Compra anulada'=>'c_a','Venta anulada'=>'v_a'),
                'required'      => true,
                'data'          => $options['tipo']
                ))
            ->add('identificador',null,array(
                'attr'=> array('class' => 'form-control d-none'),
                'label'         => false,
                'required'      => false,
                'data'          => $options['identificador']
                ))
            ->add('empresa',EntityType::class,array(
                'class'         => 'AppBundle:Empresa',
                'attr'          => array('class' => 'form-control d-none'),
                'label'         => false,
                'data'          => $this->em->getRepository('AppBundle:Empresa')->find($this->session->get('empresa'))  
                ))
            ->add('transaccionDetalle', CollectionType::class, array(
                'entry_type' => TransaccionDetalleType::class,
                'entry_options' => array('label' => false),
                'required'=>false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))       
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Transaccion',
            'identificador' => 0,
            'tipo' => '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_transaccion';
    }


}
