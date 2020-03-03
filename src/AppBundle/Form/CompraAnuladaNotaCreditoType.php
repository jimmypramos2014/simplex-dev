<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

class CompraAnuladaNotaCreditoType extends AbstractType
{

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em       = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Número',
                'required'      => false,
                ))
            ->add('fechaEmision',null,array(
                'attr'          => array('class' => 'form-control fecha','readonly'=>'readonly'),
                'label'         => 'Fecha de emisión',
                'required'      => false,
                'html5'         => false,
                'widget'        => 'single_text',
                'format'        => $options['date_format'],                
                ))
            ->add('fechaVencimiento',null,array(
                'attr'          => array('class' => 'form-control fecha','readonly'=>'readonly'),
                'label'         => 'Fecha de vencimiento',
                'required'      => false,
                'html5'         => false,
                'widget'        => 'single_text',
                'format'        => $options['date_format'],                
                ))
            ->add('ruc',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'RUC',
                'required'      => false,
                'data'          => $options['ruc']
                ))
            ->add('denominacion',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Denominación',
                'required'      => false,
                'data'          => $options['denominacion']
                ))
            ->add('direccion',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Dirección',
                'required'      => false,
                'data'          => $options['direccion']
                ))
            ->add('observacion',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Observaciones',
                'required'      => false,
                ))
            ->add('motivoEmision',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Motivo de emisión',
                'required'      => false,
                ))
            ->add('numeroDocumentoRelacionado',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Nro.Doc. relacionado',
                'required'      => false,
                'data'          => $options['numdoc_relacionado']
                ))
            ->add('facturaCompra',EntityType::class,array(
                'attr'          => array('class' => 'form-control d-none'),
                'class'         => 'AppBundle:FacturaCompra',
                'label'         => false,
                'required'      => true,
                'data'          => $this->em->getRepository('AppBundle:FacturaCompra')->find($options['factura_compra_id'])  
                ))
            ->add('monto',NumberType::class,array(
                'attr'          => array('class' => 'form-control '),
                'label'         => 'Monto',
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
            'data_class' => 'AppBundle\Entity\CompraAnuladaNotaCredito',
            'date_format' => 'dd/MM/yyyy',
            'factura_compra_id' => '',
            'numdoc_relacionado' => '',
            'ruc' => '',
            'direccion' => '',
            'denominacion' => ''            
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_compraanuladanotacredito';
    }


}
