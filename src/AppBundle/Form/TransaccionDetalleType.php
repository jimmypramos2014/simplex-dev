<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

class TransaccionDetalleType extends AbstractType
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
            ->add('tipoDocumento',ChoiceType::class,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Tipo Doc.',
                'choices'       => array('Tipo1'=>'t1','Devolución de dinero'=>'devolucion','Nota de crédito'=>'nota_credito'),
                'required'      => true,
                //'data'          => 't1'
                ))
            ->add('numeroDocumento',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Num. Doc.',
                'required'      => false,
                ))
            ->add('monto',null,array(
                'attr'=> array('class' => 'form-control'),
                'label'         => 'Monto',
                'required'      => false,
                //'data'          => '0'
                ))
            //->add('notaCredito')
            ->add('cajaCuentaBanco',EntityType::class,array(
                'attr'=> array('class' => 'form-control'),
                'class' => 'AppBundle:CajaCuentaBanco',
                'label'         => 'Caja o Cuenta',
                'required'      => true,
                'query_builder' => function(EntityRepository $er) use ($options)
                {

                    // $qb = $er->createQueryBuilder('ccb');
                    // $qb->select('ccb','ccb.numero');
                    // //$qb->addSelect('ccb.numero');
                    // //$qb->from("AppBundle:CajaCuentaBanco", "ccb");
                    // $qb->leftJoin('ccb.empresa', 'e');

                    $qb = $er->createQueryBuilder('ccb');
                    $qb->join('ccb.empresa','e');
                    $qb->where('e.id ='.$this->session->get('empresa'));
                    return $qb;
                }                     
                ))
            ;
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TransaccionDetalle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_transacciondetalle';
    }


}
