<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

class NotaDebitoType extends AbstractType
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
            ->add('numeroGuiaRemision',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Guía de remisión',
                'required'      => false,
            )) 
            ->add('fecha',DateType::class,array(
                'attr'=> array('class' => 'form-control setcurrentdate ','required'=>'required','placeholder'=>'Fecha'),
                'label'         => 'Fecha',
                'format' => $options['date_format'],
                'html5' => false,
                'widget' => 'single_text',
                'required'  => true,
            ))
            ->add('documentoModifica',ChoiceType::class,array(
                'attr'=> array('class' => 'form-control','required'=>'required'),
                'choices'       => array('Boleta' => 'boleta','Factura' => 'factura'),            
                'label'         => 'Documento que modifica',
                'required'      => true,
            ))            
            ->add('estado',HiddenType::class,array(
                'data'  => true,
            ))
            ->add('caja',EntityType::class,array(
                'class'         => 'AppBundle:Caja',
                'label'         => 'Caja',
                'attr'          => array('class' => 'form-control','required'=>'required'),
                'required'      => true,
                'query_builder' => function(EntityRepository $er) use ($options)
                {
                    $qb = $er->createQueryBuilder('c');
                    $qb->leftJoin('c.local','l');
                    $qb->where('c.estado = 1');
                    
                    if($options['caja'] != '')
                    {
                        $qb->andWhere('c.id ='.$options['caja']);
                    }
                    else
                    {
                        $qb->andWhere('l.id ='.$options['local']);
                    }

                    return $qb;
                }                 
            ))     
            ->add('facturaVenta',EntityType::class,array(
                'class' => 'AppBundle:FacturaVenta',
                'label' => 'Documento que se modifica',
                'attr'  => array('class' => 'form-control chosen-select','required'=>'required'),
                'required'  => true,
                'placeholder' => 'Seleccionar',
                'query_builder' => function(EntityRepository $er) use ($options)
                {
                    $qb = $er->createQueryBuilder('f');
                    $qb->join('f.venta','v');
                    $qb->join('f.local','l');
                    $qb->join('l.empresa','e');
                    $qb->where('v.estado = 1');
                    $qb->andWhere('e.id = '.$options['empresa']);
                    $qb->andWhere("f.documento IN ('factura','boleta') ");

                    $qb->orderBy('f.fecha','DESC');

                    return $qb;
                }                    
            ))              
            ->add('notaDebitoTipo',EntityType::class,array(
                'class' => 'AppBundle:NotaDebitoTipo',
                'attr'=> array('class' => 'form-control','required'=>'required'),
                'label'         => 'Tipo',
                'required'      => true,
                'query_builder' => function(EntityRepository $er) use ($options)
                {
                    $qb = $er->createQueryBuilder('e');
                    $qb->where('e.estado = 1');

                    return $qb;
                }                   
            ))
            ->add('moneda',EntityType::class,array(
                'class' => 'AppBundle:Moneda',
                'attr'=> array('class' => 'form-control','required'=>'required'),
                'label'         => 'Moneda',
                'required'      => true,
            ))                           
            ->add('notaDebitoDetalle', CollectionType::class, array(
                'entry_type' => NotaDebitoDetalleType::class,
                'entry_options' => array('label' => false),
                'required'=>false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ->add('tipoVenta',ChoiceType::class,array(
                'attr'=> array('class' => 'form-control','required'=>'required'),
                'choices'       => array('Venta Interna' => 'venta_interna','Exportación' => 'exportacion'),                
                'label'         => 'Tipo de Venta',
                'data'          => 'venta_interna',
                'required'      => true,
            ))
            ->add('ordenCompra',null,array(
                'label'         => 'Orden de compra',
                'attr'          => array('class' => 'form-control'),
                'required'      => false,
            ))
            ->add('fechaVencimiento',DateType::class,array(
                'attr'=> array('class' => 'form-control setcurrentdate ','required'=>'required','placeholder'=>'Fecha Vencimiento'),
                'label'         => 'Fecha Vencimiento',
                'format' => $options['date_format'],
                'html5' => false,
                'widget' => 'single_text',
                'required'  => true,
            ))
            ->add('condicionPago',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Condición de pago',
                'required'      => false,
            ))
            ->add('placaVehiculo',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Placa de vehículo',
                'required'      => false,
            ))
            ->add('observacion',TextareaType::class,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Observación',
                'required'      => false,
            ))                                                                               
            ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $notaDebito = $event->getData();
            $form = $event->getForm();


            if (!$notaDebito || null === $notaDebito->getId()) {
                $form
                    ->add('caja',EntityType::class,array(
                        'class'         => 'AppBundle:Caja',
                        'label'         => 'Caja',
                        'attr'          => array('class' => 'form-control','required'=>'required'),
                        'required'      => true,
                        'data'          => ($options['caja'] != '') ? $this->em->getRepository('AppBundle:Caja')->find($options['caja']) : '',
                        'query_builder' => function(EntityRepository $er) use ($options)
                        {
                            $qb = $er->createQueryBuilder('c');
                            $qb->leftJoin('c.local','l');
                            $qb->where('c.estado = 1');
                            
                            if($options['caja'] != '')
                            {
                                $qb->andWhere('c.id ='.$options['caja']);
                            }
                            else
                            {
                                $qb->andWhere('l.id ='.$options['local']);
                            }

                            return $qb;
                        }                 
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
            'data_class' => 'AppBundle\Entity\NotaDebito',
            'empresa' => '',
            'local' => '',
            'caja' => '',
            'date_format' => 'dd/MM/yyyy'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_notadebito';
    }


}
