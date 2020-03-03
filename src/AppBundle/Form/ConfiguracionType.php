<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ConfiguracionType extends AbstractType
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
            ->add('logo',FileType::class,array(
                'label'         => 'Logo',
                'attr'=> array('class' => 'form-control'),
                'required'      => false,
                ))
            ->add('nombreCorto',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Nombre corto',
                'required'      => false,
                ))                                          
            ->add('direccionWeb',null,array(
                'attr'=> array('class' => 'form-control '),
                'label'         => 'Dirección web',
                'required'      => false,
                ))           
            ->add('proformaFormato',ChoiceType::class,array(
                'attr'=> array('class' => 'form-control '),
                'label'         => 'Formato de proforma',
                'choices'       => array('A4' => 'A4'),                    
                'required'      => true,
                ))
            ->add('proformaOrientacion',ChoiceType::class,array(
                'attr'=> array('class' => 'form-control '),
                'label'         => 'Orientación de proforma',
                'choices'       => array('Landscape' => 'Landscape'), 
                'required'      => true,
                ))
            ->add('PrefijoCodigoProducto',null,array(
                'attr'=> array('class' => 'form-control ','required'=>'required'),
                'label'         => 'Prefijo para código de productos',
                'required'      => true,
                ))
            ->add('CorreoRemitente',null,array(
                'attr'=> array('class' => 'form-control '),
                'label'         => 'Correo remitente',
                'required'      => false,
                ))
            ->add('estado',HiddenType::class,array(
                'data'  => true,
                ))            
            ->add('imagenProductoDefault',FileType::class,array(
                'label'         => 'Imagen de producto por defecto',
                'attr'=> array('class' => 'form-control'),
                'required'      => false,
                'mapped'    => false
                ))
            ->add('imagenCategoriaDefault',FileType::class,array(
                'label'         => 'Imagen de categoría por defecto',
                'attr'=> array('class' => 'form-control'),
                'required'      => false,
                'mapped'    => false
                ))                                                                                                
            ;


    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Empresa',
            'empresa' => ''
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_configuracion';
    }


}
