<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Transaccion;
use AppBundle\Entity\TransaccionDetalle;
use AppBundle\Entity\TransferenciaDinero;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Caja y banco controller.
 *
 * @Route("cajaybanco")
 */
class CajaybancoController extends Controller
{


    /**
     * Transferencias de dinero entre cajas y cuentas bancarias
     *
     * @Route("/", name="cajaybanco_index")
     * @Method({"GET","POST"})
     * 
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $cajasycuentas = $em->getRepository('AppBundle:CajaCuentaBanco')->findBy(array('empresa'=>$empresa));

        return $this->render('cajaybanco/index.html.twig', array(
            'titulo'      => 'Reporte',
            'cajasycuentas'         => $cajasycuentas,
        ));        

    }


    /**
     * Procesar pago de transaccion
     *
     * @Route("/procesarpagoatransaccion", name="cajaybanco_procesarpagotransaccion")
     * @Method({"GET","POST"})
     * 
     */
    public function procesarPagoTransaccionAction(Request $request)
    {
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $fecha = new \Datetime();
        $em = $this->getDoctrine()->getManager();

        $identificador = $request->request->get('identificador');
        $tipo          = $request->request->get('tipo');
        //$transaccion   = $em->getRepository('AppBundle:Transaccion')->findOneBy(array('identificador'=>$identificador,'tipo'=>$tipo));

        $dql = "SELECT t FROM AppBundle:Transaccion t ";
        $dql .= " JOIN t.empresa e";
        $dql .= " WHERE  t.identificador =:identificador  AND t.tipo =:tipo AND e.id =:empresa ";

        $query = $em->createQuery($dql);

        $query->setParameter('identificador',$identificador);
        $query->setParameter('tipo',$tipo);         
        $query->setParameter('empresa',$empresa);
 
        $transaccion =  $query->getOneOrNullResult();    

        if(!$transaccion){
            $transaccion  = new Transaccion();
        }

        $originalTags = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($transaccion->getTransaccionDetalle() as $tag) {
            $originalTags->add($tag);
        }

        $form = $this->createForm('AppBundle\Form\TransaccionType', $transaccion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $transaccion->setFecha($fecha);

            // remove the relationship between the tag and the Task
            foreach ($originalTags as $tag) {

                if (false === $transaccion->getTransaccionDetalle()->contains($tag)) {
                    // remove the Task from the Tag
                    //$tag->getTransaccion()->removeElement($transaccion);

                    // if it was a many-to-one relationship, remove the relationship like this
                    $tag->setTransaccion(null);

                    $em->persist($tag);

                    // if you wanted to delete the Tag entirely, you can also do that
                    // $entityManager->remove($tag);
                }

            }


            $em->persist($transaccion);

            try {

                $em->flush();
                $this->addFlash("success", "El pago(s) fue realizado exitosamente.");
                
            } catch (\Exception $e) {
                
                $this->addFlash("danger", $e." Ocurrió un error inesperado, el registro no se guardó.");
            }
            
            if($tipo == 'v'){
                return $this->redirectToRoute('detalleventa_lista');
            }

            if($tipo == 'c'){
                return $this->redirectToRoute('facturacompra_index');
            }

            if($tipo == 'g'){
                return $this->redirectToRoute('gasto_index');
            }

            if($tipo == 'c_a'){
                return $this->redirectToRoute('facturacompra_lista');
            }


            if($tipo == 'v_a'){
                return $this->redirectToRoute('detalleventa_lista');
            }                           
        }


        if($tipo == 'v'){
            return $this->redirectToRoute('detalleventa_lista');
        }

        if($tipo == 'c'){
            return $this->redirectToRoute('facturacompra_index');
        }

        if($tipo == 'g'){
            return $this->redirectToRoute('gasto_index');
        }


        if($tipo == 'c_a'){
            return $this->redirectToRoute('facturacompra_lista');
        }
            
     
        if($tipo == 'v_a'){
            return $this->redirectToRoute('detalleventa_lista');
        }          
    }

    /**
     * Transferencias de dinero entre cajas y cuentas bancarias
     *
     * @Route("/transferencias", name="cajaybanco_transferencias")
     * @Method({"GET","POST"})
     * 
     */
    public function transferenciasAction(Request $request)
    {

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $fecha = new \Datetime();

        $transferenciaDinero = new TransferenciaDinero();        

        $form = $this->createForm('AppBundle\Form\TransferenciaDineroType',$transferenciaDinero,array('empresa'=>$empresa));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $transferenciaDinero->setFecha($fecha);
            $em->persist($transferenciaDinero);

            // $salida  = $form->getData()['salida'];
            // $entrada = $form->getData()['entrada'];
            // $monto   = $form->getData()['monto'];

            // $cajaCuentaBancoSalida  = $em->getRepository('AppBundle:CajaCuentaBanco')->find($salida);
            // $cajaCuentaBancoEntrada = $em->getRepository('AppBundle:CajaCuentaBanco')->find($entrada);

            // $monto_salida  = $cajaCuentaBancoSalida->getMonto() - $monto;
            // $monto_entrada = $cajaCuentaBancoEntrada->getMonto() + $monto;

            // $cajaCuentaBancoSalida->setMonto($monto_salida);
            // $em->persist($cajaCuentaBancoSalida);

            // $cajaCuentaBancoEntrada->setMonto($monto_entrada);
            // $em->persist($cajaCuentaBancoEntrada);

            try {

                $em->flush();
                $this->addFlash("success", "La transferencia fue realizada exitosamente.");
                
            } catch (\Exception $e) {

                $this->addFlash("danger", $e." Ocurrió un error inesperado, la transferencia fue truncada.");

            }


            return $this->redirectToRoute('cajaybanco_transferencias');
        }


        return $this->render('cajaybanco/transferencias.html.twig', array(
            'titulo'        => 'Transferencia de dinero',
            'form'          => $form->createView(),
            'local'         => $local,
        ));        

    }

}
