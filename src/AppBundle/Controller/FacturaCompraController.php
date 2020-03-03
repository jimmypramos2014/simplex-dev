<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FacturaCompra;
use AppBundle\Entity\Compra;
use AppBundle\Entity\TransferenciaXProducto;
use AppBundle\Entity\Transferencia;
use AppBundle\Entity\DetalleCompra;
use AppBundle\Entity\CompraAnuladaNotaCredito;
use AppBundle\Entity\DetalleCompraAnulada;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Facturacompra controller.
 *
 * @Route("facturacompra")
 */
class FacturaCompraController extends Controller
{
    /**
     * Lists all facturaCompra entities.
     *
     * @Route("/", name="facturacompra_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $dql = "SELECT fc FROM AppBundle:FacturaCompra fc ";
        $dql .= " JOIN fc.compra c";
        $dql .= " JOIN c.empleado e";
        $dql .= " JOIN e.local el";
        $dql .= " JOIN el.empresa em";
        $dql .= " WHERE  fc.estado = 1  ";

        if($empresa != ''){
            $dql .= " AND em.id =:empresa  ";
        }

        $dql .= " ORDER BY fc.fecha DESC ";

        $query = $em->createQuery($dql);

        if($empresa != ''){
            $query->setParameter('empresa',$empresa);         
        }
 
        $facturaCompras =  $query->getResult();   

        //$facturaCompras = $em->getRepository('AppBundle:FacturaCompra')->findBy(array('estado'=>true),array('fecha' => 'DESC'));

        return $this->render('facturacompra/index.html.twig', array(
            'facturaCompras' => $facturaCompras,
            'titulo'         => 'Lista de compras'
        ));
    }

    /**
     * Creates a new facturaCompra entity.
     *
     * @Route("/new", name="facturacompra_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $facturaCompra = new Facturacompra();
        $form = $this->createForm('AppBundle\Form\FacturaCompraType', $facturaCompra);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($facturaCompra);
            $em->flush();

            return $this->redirectToRoute('facturacompra_show', array('id' => $facturaCompra->getId()));
        }

        return $this->render('facturacompra/new.html.twig', array(
            'facturaCompra' => $facturaCompra,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a facturaCompra entity.
     *
     * @Route("/{id}", name="facturacompra_show")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction(Compra $compra)
    {
        $em = $this->getDoctrine()->getManager();

        $detalleCompras = $em->getRepository('AppBundle:DetalleCompra')->findBy(array('compra'=>$compra));

        return $this->render('facturacompra/show.html.twig', array(
            'detalleCompras' => $detalleCompras,
            'titulo'         => 'Detalle de compra'
        ));
    }

    /**
     * Displays a form to edit an existing factura compra entity.
     *
     * @Route("/{id}/edit", name="facturacompra_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN') ")
     */
    public function editAction(Request $request, FacturaCompra $facturaCompra)
    {

        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $fecha = new \DateTime();

        $originalArchivo = null;

        if($facturaCompra->getArchivo()){

            $originalArchivo = $facturaCompra->getArchivo();

            $facturaCompra->setArchivo(
                new File($this->getParameter('archivos_directorio').'/'.$empresa.'/'.$facturaCompra->getArchivo())
            );
        }

        $originalVoucher = null;

        if($facturaCompra->getVoucher()){

            $originalVoucher = $facturaCompra->getVoucher();

            $facturaCompra->setVoucher(
                new File($this->getParameter('archivos_directorio').'/'.$empresa.'/'.$facturaCompra->getVoucher())
            );
        }

        $numeroDocumentoAnterior = $facturaCompra->getNumeroDocumento();
        $detalleCompraAnterior   = $facturaCompra->getCompra()->getDetalleCompra();

        $detalleArray = array();
        $j = 0;
        foreach($detalleCompraAnterior as $detalleAnterior){

            if($detalleAnterior->getProductoXLocal()){

                $detalleArray[$j]['id'] = $detalleAnterior->getProductoXLocal()->getId();
                $detalleArray[$j]['cantidad'] = $detalleAnterior->getCantidad();

                $j++;

            }

        }
        

        $localObj   = $em->getRepository('AppBundle:EmpresaLocal')->find($local);
        $formProducto = $this->createForm('AppBundle\Form\DetalleCompraProductoType', $facturaCompra,array('local'=>$local,'empresa'=>$empresa));

        $tipoCambio = $em->getRepository('AppBundle:TipoCambio')->findOneBy(array('empresa'=>$empresa,'fecha'=>$fecha));

        $editForm = $this->createForm('AppBundle\Form\FacturaCompraType', $facturaCompra,array('empresa'=>$empresa,'local'=>$local));
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $hora = date('H');
            $minuto = date('i');
            $segundo = date('s');

            $intervalo_adicional = 'PT'.$hora.'H'.$minuto.'M'.$segundo.'S';
            $editForm->getData()->getFecha()->add(new \DateInterval($intervalo_adicional));
            
            //Guardamos el archivo
            $fileArchivo = $facturaCompra->getArchivo();

            if($fileArchivo){

                $fileNameArchivo = $this->generateUniqueFileName().'.'.$fileArchivo->guessExtension();

                $fileArchivo->move(
                    $this->getParameter('archivos_directorio').'/'.$empresa,
                    $fileNameArchivo
                );


                $facturaCompra->setArchivo($fileNameArchivo);

            }else{

                $facturaCompra->setArchivo($originalArchivo);
            }

            //Guardamos el voucher de pago
            $fileVoucher = $facturaCompra->getVoucher();

            if($fileVoucher){

                $fileNameVoucher= $this->generateUniqueFileName().'.'.$fileVoucher->guessExtension();

                $fileVoucher->move(
                    $this->getParameter('archivos_directorio').'/'.$empresa,
                    $fileNameVoucher
                );


                $facturaCompra->setVoucher($fileNameVoucher);

            }else{

                $facturaCompra->setVoucher($originalVoucher);
            }


            $dql = "SELECT t FROM AppBundle:Transferencia t ";
            $dql .= " JOIN t.motivoTraslado mt";
            $dql .= " JOIN t.empresa e";
            $dql .= " WHERE  t.estado = 1  AND mt.id = 2  ";

            if($empresa != ''){
                $dql .= " AND e.id =:empresa  ";
            }

            $dql .= " AND t.numeroDocumento =:numeroDocumento ";

            $query = $em->createQuery($dql);

            if($empresa != ''){
                $query->setParameter('empresa',$empresa);         
            }

            $query->setParameter('numeroDocumento',$numeroDocumentoAnterior);
     
            $transferencia =  $query->getOneOrNullResult();

            if($transferencia)
            {

                $transferencia->setNumeroDocumento($editForm->getData()->getNumeroDocumento());
                $em->persist($transferencia);

                foreach($transferencia->getTransferenciaXProducto() as $transferenciaXProducto){

                    $em->remove($transferenciaXProducto);

                }

            }


            foreach($detalleArray as $p=>$det){

                $this->container->get('AppBundle\Util\Util')->disminuirAlmacen($det['id'],$det['cantidad']);
            }


            $total = 0;
            foreach($editForm->getData()->getCompra()->getDetalleCompra() as $detalle){

                if($detalle->getProductoXLocal()){

                    $total = $total + $detalle->getSubtotal();

                    $precio   = $detalle->getPrecio();
                    $cantidad = $detalle->getCantidad();


                    if($transferencia){

                        //Guardamos el detalle de la transferencia
                        $transferenciaXProductoNuevo = new TransferenciaXProducto();
                        $transferenciaXProductoNuevo->setProductoXLocal($detalle->getProductoXLocal());
                        $transferenciaXProductoNuevo->setCantidad($cantidad);
                        $transferenciaXProductoNuevo->setTransferencia($transferencia);
                        $transferenciaXProductoNuevo->setPrecio($precio);
                        $transferenciaXProductoNuevo->setContador($cantidad);
                        $em->persist($transferenciaXProductoNuevo);
                    
                    }

                    $this->container->get('AppBundle\Util\Util')->aumentarAlmacen($detalle->getProductoXLocal()->getId(),$cantidad);

                }

            }

            $facturaCompra->getCompra()->getCompraFormaPago()[0]->setCantidad($total);


            $em->persist($facturaCompra);


            try {

                $em->flush();
                
                $this->addFlash("success", "El registro fue editado exitosamente."); 

                
            } catch (\Exception $e) {

                $this->addFlash("danger", $e." Ocurrió un error inesperado, el registro no se guardó.");
                
            }   

            return $this->redirectToRoute('facturacompra_index');
        }

        return $this->render('facturacompra/edit.html.twig', array(
            'edit_form'         => $editForm->createView(),
            'formProducto'      => $formProducto->createView(),
            'local'             => $local,
            'tipoCambio'        => $tipoCambio,
            'facturaCompra'     => $facturaCompra,
            'originalVoucher'   => $originalVoucher,
            'originalArchivo'   => $originalArchivo,
            'titulo'            => 'Editar compra',
        ));
    }

    /**
     * Deletes a facturacompra entity.
     *
     * @Route("/{id}/delete", name="facturacompra_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, FacturaCompra $facturaCompra)
    {
        $em = $this->getDoctrine()->getManager();

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $facturaCompra->setEstado(false);
        $em->persist($facturaCompra);

        //disminuir almacen
        foreach($facturaCompra->getCompra()->getDetalleCompra() as $detalle){

            $this->container->get('AppBundle\Util\Util')->disminuirAlmacen($detalle->getProductoXLocal()->getId(),$detalle->getCantidad());

        }

        $transferencias = $em->getRepository('AppBundle:Transferencia')->findBy(array('numeroDocumento'=>$facturaCompra->getNumeroDocumento(),'empresa'=>$empresa));

        foreach($transferencias as $transferencia){

            $transferencia->setEstado(false);
            $transferencia->setNumeroDocumento('eliminado');
            $em->persist($transferencia);

        }        

        try{

            $em->flush();

            $this->addFlash("success", "El registro fue eliminado exitosamente.");

        }catch(\Exception $e){

            $this->addFlash("danger", "Ocurrió un error inesperado, el registro no pudo ser eliminado.");
        }
        return $this->redirectToRoute('facturacompra_index');
    }


    /**
     * Lists all facturaCompra entities.
     *
     * @Route("/lista/anular", name="facturacompra_lista")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $dql = "SELECT fc FROM AppBundle:FacturaCompra fc ";
        $dql .= " JOIN fc.compra c";
        $dql .= " JOIN c.empleado e";
        $dql .= " JOIN e.local el";
        $dql .= " JOIN el.empresa em";
        $dql .= " WHERE  fc.estado = 1  ";

        if($empresa != ''){
            $dql .= " AND em.id =:empresa  ";
        }

        $dql .= " ORDER BY fc.fecha DESC ";

        $query = $em->createQuery($dql);

        if($empresa != ''){
            $query->setParameter('empresa',$empresa);         
        }
 
        $facturaCompras =  $query->getResult();   

        return $this->render('facturacompra/lista.html.twig', array(
            'facturaCompras' => $facturaCompras,
            'titulo'         => 'Anular compra'
        ));
    }


    /**
     * Lista las Notas de credito por tus compras.
     *
     * @Route("/notacredito", name="facturacompra_notacredito")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function notacreditoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $dql = "SELECT fc FROM AppBundle:FacturaCompra fc ";
        $dql .= " JOIN fc.compra c";
        $dql .= " JOIN c.empleado e";
        $dql .= " JOIN e.local el";
        $dql .= " JOIN el.empresa em";
        $dql .= " WHERE  fc.estado = 1  ";

        if($empresa != ''){
            $dql .= " AND em.id =:empresa  ";
        }

        $dql .= " ORDER BY fc.fecha DESC ";

        $query = $em->createQuery($dql);

        if($empresa != ''){
            $query->setParameter('empresa',$empresa);         
        }
 
        $facturaCompras =  $query->getResult();   

        return $this->render('facturacompra/notacredito.html.twig', array(
            'facturaCompras' => $facturaCompras,
            'titulo'         => 'Notas de crédito'
        ));
    }


    /**
     * Generar una nota de credito  por la anulacion de la compra
     *
     * @Route("/generar/notacredito", name="facturacompra_generar_notacredito")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function generarNotacreditoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $detalle_compra_id = $request->query->get('detalle_compra_id');
        $numero = $request->query->get('numero');

        $detalleCompra = $em->getRepository('AppBundle:DetalleCompra')->find($detalle_compra_id);

        $localObj = $em->getRepository('AppBundle:EmpresaLocal')->find($local);
        $host     = $request->getHost();

        //Seleccionamos los elementos q 
        $dql = "SELECT dca FROM AppBundle:DetalleCompraAnulada dca ";
        $dql .= " JOIN dca.detalleCompra dc ";
        $dql .= " WHERE  dc.id =:detalle_compra_id ";
        $query = $em->createQuery($dql);
        $query->setParameter('detalle_compra_id',$detalleCompra->getId());
        $detalleComprasAnulada =  $query->getResult();

        $facturaCompra = $em->getRepository('AppBundle:FacturaCompra')->findOneBy(array('compra'=>$detalleCompra->getCompra()->getId()));

        //Seleccionamos los componentes que se imprimiran en la nota de credito
        $dql = "SELECT cxd FROM AppBundle:ComponenteXDocumento cxd ";
        $dql .= " JOIN cxd.documento d";
        $dql .= " JOIN d.empresa e";
        $dql .= " WHERE  e.id =:empresa  AND d.codigo = '08' ";

        $query = $em->createQuery($dql);

        $query->setParameter('empresa',$empresa);         
 
        $componentesXDocumento =  $query->getResult();

        $html = $this->render('facturacompra/notacredito.html.twig', array(
                'facturaCompra'=> $facturaCompra,
                'detalleComprasAnulada' => $detalleComprasAnulada,
                'localObj'     => $localObj,
                'componentesXDocumento' => $componentesXDocumento,
                'host' => $host,
                'numero' => $numero
            ))->getContent();

        $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

        return new \Symfony\Component\HttpFoundation\Response(
                $pdf, 200, array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'inline; filename=notacredito.pdf',
                     
        ));

    }


    /**
     * Generar un recibo por la anulacion de la compra
     *
     * @Route("/generar/recibo", name="facturacompra_generar_recibo")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function generarReciboAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $detalle_compra_id = $request->query->get('detalle_compra_id');
        $monto = $request->query->get('monto');

        $detalleCompra = $em->getRepository('AppBundle:DetalleCompra')->find($detalle_compra_id);

        $localObj = $em->getRepository('AppBundle:EmpresaLocal')->find($local);
        $host     = $request->getHost();

        //Seleccionamos los elementos q 
        $dql = "SELECT dca FROM AppBundle:DetalleCompraAnulada dca ";
        $dql .= " JOIN dca.detalleCompra dc ";
        $dql .= " WHERE  dc.id =:detalle_compra_id ";
        $query = $em->createQuery($dql);
        $query->setParameter('detalle_compra_id',$detalleCompra->getId());
        $detalleComprasAnulada =  $query->getResult();

        $facturaCompra = $em->getRepository('AppBundle:FacturaCompra')->findOneBy(array('compra'=>$detalleCompra->getCompra()->getId()));

        //Seleccionamos los componentes que se imprimiran en la nota de credito
        $dql = "SELECT cxd FROM AppBundle:ComponenteXDocumento cxd ";
        $dql .= " JOIN cxd.documento d";
        $dql .= " JOIN d.empresa e";
        $dql .= " WHERE  e.id =:empresa  AND d.codigo = '08' ";

        $query = $em->createQuery($dql);

        $query->setParameter('empresa',$empresa);         
 
        $componentesXDocumento =  $query->getResult();

        $html = $this->render('facturacompra/recibo.html.twig', array(
                'facturaCompra'=> $facturaCompra,
                'detalleComprasAnulada' => $detalleComprasAnulada,
                'localObj'     => $localObj,
                'componentesXDocumento' => $componentesXDocumento,
                'host' => $host,
            ))->getContent();

        $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

        return new \Symfony\Component\HttpFoundation\Response(
                $pdf, 200, array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'inline; filename=recibo.pdf',
                     
        ));


    }


    /**
     * Generar un recibo por la anulacion de la compra
     *
     * @Route("/guardar/notacredito", name="facturacompra_guardar_notacredito")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function guardarNotaCreditoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $factura_compra_id = $request->request->get('appbundle_compraanuladanotacredito')['facturaCompra'];

        $compraAnuladaNotaCredito = $em->getRepository('AppBundle:CompraAnuladaNotaCredito')->findOneBy(array('facturaCompra'=>$factura_compra_id));

        if(!$compraAnuladaNotaCredito){
            

            //Actualizamos inventario
            $facturaCompra = $em->getRepository('AppBundle:FacturaCompra')->find($factura_compra_id);

            //disminuir almacen
            foreach($facturaCompra->getCompra()->getDetalleCompra() as $detalle){

                $this->container->get('AppBundle\Util\Util')->disminuirAlmacen($detalle->getProductoXLocal()->getId(),$detalle->getCantidad());

            }

            $transferencias = $em->getRepository('AppBundle:Transferencia')->findBy(array('numeroDocumento'=>$facturaCompra->getNumeroDocumento(),'empresa'=>$empresa));

            foreach($transferencias as $transferencia){

                $transferencia->setEstado(false);
                $transferencia->setNumeroDocumento('anulado');
                $em->persist($transferencia);

            }        


            $compraAnuladaNotaCredito = new CompraAnuladaNotaCredito();

        }        

        $form = $this->createForm('AppBundle\Form\CompraAnuladaNotaCreditoType', $compraAnuladaNotaCredito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $compraAnuladaNotaCredito->getFacturaCompra()->setEstado(false);
            $compraAnuladaNotaCredito->getFacturaCompra()->setTipoAnulacion('anulacion_notacredito');
            $em->persist($compraAnuladaNotaCredito);

            try {

                $em->flush();
                $this->addFlash("success", "El registro fue guardado exitosamente.");

            } catch (\Exception $e) {

                $this->addFlash("danger", "Hubo un error en el registro.");
            }

            return $this->redirectToRoute('facturacompra_index');

        }else{

            $this->addFlash("danger", "Hubo un error en el registro.");
            return $this->redirectToRoute('facturacompra_index');

        }

    }

    /**
     * Guardar registor de la devolucion cuando se anula la compra
     *
     * @Route("/guardar/devolucion", name="facturacompra_guardar_devolucion")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function guardarDevolucionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $factura_compra_id = $request->request->get('appbundle_compraanuladanotacredito')['facturaCompra'];

        $compraAnuladaNotaCredito = $em->getRepository('AppBundle:CompraAnuladaNotaCredito')->findOneBy(array('facturaCompra'=>$factura_compra_id));

        if(!$compraAnuladaNotaCredito){
            

            //Actualizamos inventario
            $facturaCompra = $em->getRepository('AppBundle:FacturaCompra')->find($factura_compra_id);

            //disminuir almacen
            foreach($facturaCompra->getCompra()->getDetalleCompra() as $detalle){

                $this->container->get('AppBundle\Util\Util')->disminuirAlmacen($detalle->getProductoXLocal()->getId(),$detalle->getCantidad());

            }

            $transferencias = $em->getRepository('AppBundle:Transferencia')->findBy(array('numeroDocumento'=>$facturaCompra->getNumeroDocumento(),'empresa'=>$empresa));

            foreach($transferencias as $transferencia){

                $transferencia->setEstado(false);
                $transferencia->setNumeroDocumento('anulado');
                $em->persist($transferencia);

            }        


            $compraAnuladaNotaCredito = new CompraAnuladaNotaCredito();

        }        

        $form = $this->createForm('AppBundle\Form\CompraAnuladaNotaCreditoType', $compraAnuladaNotaCredito);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $compraAnuladaNotaCredito->getFacturaCompra()->setEstado(false);
            $compraAnuladaNotaCredito->getFacturaCompra()->setTipoAnulacion('anulacion_devolucion');
            $em->persist($compraAnuladaNotaCredito);

            try {

                $em->flush();
                $this->addFlash("success", "El registro fue guardado exitosamente.");

            } catch (\Exception $e) {
                
                $this->addFlash("danger", "Hubo un error en el registro.");
            }

            return $this->redirectToRoute('facturacompra_index');

        }else{

            $this->addFlash("danger", "Hubo un error en el registro.");
            return $this->redirectToRoute('facturacompra_index');

        }

    }



    /**
     * Generar un recibo por la anulacion de la compra
     *
     * @Route("/guardar/detallecompra/notacredito", name="facturacompra_guardar_detallecompra_notacredito")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function guardarDetalleCompraNotaCreditoAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $detalle_compra_id = $request->request->get('appbundle_detallecompraanulada')['detalleCompra'];

        $detalleCompraAnulada = $em->getRepository('AppBundle:DetalleCompraAnulada')->findOneBy(array('detalleCompra'=>$detalle_compra_id));

        if(!$detalleCompraAnulada){
            

            //Actualizamos inventario
            $detalleCompra = $em->getRepository('AppBundle:DetalleCompra')->find($detalle_compra_id);

            //disminuir almacen
            $this->container->get('AppBundle\Util\Util')->disminuirAlmacen($detalleCompra->getProductoXLocal()->getId(),$detalleCompra->getCantidad());

            $facturaCompras = $detalleCompra->getCompra()->getFacturaCompra();
            foreach($facturaCompras as $facturaCompra){
                $numero_documento = $facturaCompra->getNumeroDocumento();
            }
            $transferencias = $em->getRepository('AppBundle:Transferencia')->findBy(array('numeroDocumento'=>$numero_documento,'empresa'=>$empresa));

            foreach($transferencias as $transferencia){

                foreach($transferencia->getTransferenciaXProducto() as $transferenciaXProducto){

                    if($transferenciaXProducto->getProductoXLocal()->getId() == $detalleCompra->getProductoXLocal()->getId()){
                        $em->remove($transferenciaXProducto);
                    }

                }

            }        


            $detalleCompraAnulada = new DetalleCompraAnulada();

        }        

        $form = $this->createForm('AppBundle\Form\DetalleCompraAnuladaType', $detalleCompraAnulada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $detalleCompraAnulada->getDetalleCompra()->setEstado(false);
            $detalleCompraAnulada->getDetalleCompra()->setTipoAnulacion('anulacion_notacredito');
            $em->persist($detalleCompraAnulada);

            try {

                $em->flush();
                $this->addFlash("success", "El registro fue guardado exitosamente.");

            } catch (\Exception $e) {

                $this->addFlash("danger", "Hubo un error en el registro.");
            }

            return $this->redirectToRoute('facturacompra_lista');

        }else{

            $this->addFlash("danger", "Hubo un error en el registro.");
            return $this->redirectToRoute('facturacompra_lista');

        }

    }

    /**
     * Guardar registor de la devolucion cuando se anula un detalle compra
     *
     * @Route("/guardar/detallecompra/devolucion", name="facturacompra_guardar_detallecompra_devolucion")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function guardarDetalleCompraDevolucionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $detalle_compra_id = $request->request->get('appbundle_detallecompraanulada')['detalleCompra'];

        $detalleCompraAnulada = $em->getRepository('AppBundle:DetalleCompraAnulada')->findOneBy(array('detalleCompra'=>$detalle_compra_id));

        if(!$detalleCompraAnulada){
            

            //Actualizamos inventario
            $detalleCompra = $em->getRepository('AppBundle:DetalleCompra')->find($detalle_compra_id);

            //disminuir almacen
            $this->container->get('AppBundle\Util\Util')->disminuirAlmacen($detalleCompra->getProductoXLocal()->getId(),$detalleCompra->getCantidad());

            $facturaCompras = $detalleCompra->getCompra()->getFacturaCompra();
            foreach($facturaCompras as $facturaCompra){
                $numero_documento = $facturaCompra->getNumeroDocumento();
            }
            $transferencias = $em->getRepository('AppBundle:Transferencia')->findBy(array('numeroDocumento'=>$numero_documento,'empresa'=>$empresa));

            foreach($transferencias as $transferencia){

                foreach($transferencia->getTransferenciaXProducto() as $transferenciaXProducto){

                    if($transferenciaXProducto->getProductoXLocal()->getId() == $detalleCompra->getProductoXLocal()->getId()){
                        $em->remove($transferenciaXProducto);
                    }

                }

            }        


            $detalleCompraAnulada = new DetalleCompraAnulada();

        }        

        $form = $this->createForm('AppBundle\Form\DetalleCompraAnuladaType', $detalleCompraAnulada);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $detalleCompraAnulada->getDetalleCompra()->setEstado(false);
            $detalleCompraAnulada->getDetalleCompra()->setTipoAnulacion('anulacion_devolucion');
            $em->persist($detalleCompraAnulada);

            try {

                $em->flush();
                $this->addFlash("success", "El registro fue guardado exitosamente.");

            } catch (\Exception $e) {
                
                $this->addFlash("danger", "Hubo un error en el registro.");
            }

            return $this->redirectToRoute('facturacompra_lista');

        }else{

            $this->addFlash("danger", "Hubo un error en el registro.");
            return $this->redirectToRoute('facturacompra_lista');

        }

    }




    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
    

}
