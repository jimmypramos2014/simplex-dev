<?php

namespace AppBundle\Controller;

use AppBundle\Entity\NotaDebito;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Notadebito controller.
 *
 * @Route("notadebito")
 */
class NotaDebitoController extends Controller
{
    /**
     * Lists all notaDebito entities.
     *
     * @Route("/", name="notadebito_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $caja           = $session->get('caja');
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $notaCreditos = $em->getRepository('AppBundle:NotaDebito')->findBy(array('local'=>$local));


        return $this->render('notadebito/index.html.twig', array(
            'notaCreditos' => $notaCreditos,
            'titulo'       => 'Lista de notas de débito',
        ));
    }

    /**
     * Creates a new notaDebito entity.
     *
     * @Route("/new", name="notadebito_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $caja           = $session->get('caja');
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $fecha = new \DateTime();

        $categorias      = $this->container->get('AppBundle\Util\Util')->obtenerCategorias($local);
        $formClientePv   = $this->createForm('AppBundle\Form\ClientePvType',null);
        $productoXLocals = $this->container->get('AppBundle\Util\Util')->productosMasVendidosXLocal($local,null);
        $tipoCambio      = $em->getRepository('AppBundle:TipoCambio')->findOneBy(array('empresa'=>$empresa,'fecha'=>$fecha));
        $tiposImpuesto   = $em->getRepository('AppBundle:TipoImpuesto')->findAll();

        $notaCredito = new NotaDebito();
        $form = $this->createForm('AppBundle\Form\NotaDebitoType', $notaCredito,array('empresa'=>$empresa,'local'=>$local,'caja'=>$caja));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hora = date('H');
            $minuto = date('i');
            $segundo = date('s');

            $intervalo_adicional = 'PT'.$hora.'H'.$minuto.'M'.$segundo.'S';
            $notaCredito->getFecha()->add(new \DateInterval($intervalo_adicional));

            $cliente_id     = $request->request->get('cliente_select');
            $cliente        = $em->getRepository('AppBundle:Cliente')->find($cliente_id);

            $notaCredito->setCliente($cliente);            
            $notaCredito->setLocal($form->getData()->getCaja()->getLocal());

            //Registramos el tipo de cambio si fue seleccionada como moneda el dolar
            $valor_tipo_cambio = null;
            if($notaCredito->getMoneda()->getId() == 2)
            {
                $fecha = $notaCredito->getFecha();

                $tipoCambioObj       = $em->getRepository('AppBundle:TipoCambio')->findOneBy(array('empresa'=>$empresa,'fecha'=>$fecha));
                $valor_tipo_cambio   = ($tipoCambioObj)? $tipoCambioObj->getVenta() : '';

                if($valor_tipo_cambio == '')
                {
                    $this->addFlash("danger", " No se pudo registrar el pedido . El tipo de cambio para la fecha seleccionada no existe. Fecha seleccionada: ".$fecha->format('d/m/Y'));
                    return $this->redirectToRoute('facturaventa_new');
                }
                
            }

            $documento = $notaCredito->getFacturaVenta()->getDocumento();

            $notaCredito->setValorTipoCambio($valor_tipo_cambio);
            $notaCredito->setDocumentoModifica($documento);

            //dump($notaCredito->getFacturaVenta()->getDocumento());die();

            //Buscamos cuantas notas de credito existen actualmente en este local
            $repoNotaCredito = $em->getRepository(NotaDebito::class);
            $totalNotaCredito = $repoNotaCredito->createQueryBuilder('a')
                ->where('a.local = '.$local)
                ->andWhere("a.documentoModifica = '".$documento."'")
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult();

            $totalNotaCredito++;

            switch ($documento) {
                case 'factura':
                    $numero = ($notaCredito->getCaja()->getLocal()->getSerieNotadebitoFactura()) ? $notaCredito->getCaja()->getLocal()->getSerieNotadebitoFactura().'-'.$totalNotaCredito : $totalNotaCredito;
                    $notaCredito->setSerie($notaCredito->getCaja()->getLocal()->getSerieNotadebitoFactura());
                    break;
                case 'boleta':
                    $numero = ($notaCredito->getCaja()->getLocal()->getSerieNotadebitoBoleta()) ? $notaCredito->getCaja()->getLocal()->getSerieNotadebitoBoleta().'-'.$totalNotaCredito : $totalNotaCredito;
                    $notaCredito->setSerie($notaCredito->getCaja()->getLocal()->getSerieNotadebitoBoleta());
                    break;                           
                default:
                    $numero = ($notaCredito->getCaja()->getLocal()->getSerieNotadebitoBoleta()) ? $notaCredito->getCaja()->getLocal()->getSerieNotadebitoBoleta().'-'.$totalNotaCredito : $totalNotaCredito;
                    $notaCredito->setSerie($notaCredito->getCaja()->getLocal()->getSerieNotadebitoBoleta());
                    break;
            }

            $notaCredito->setNumero($totalNotaCredito);

            //Guardamos los totales
            $productos_array = array();
            $total_gravada      = 0;
            $total_igv          = 0;
            $total              = 0;
            $total_gratuita     = 0;
            $total_descuento    = 0;
            $total_anticipo     = 0;
            $total_inafecta     = 0;
            $total_exonerada    = 0;            
            $total_otros_cargos = 0;

            $j=0;

            foreach($notaCredito->getNotaDebitoDetalle() as $detalle)
            {
                if($detalle->getCantidad() <= 0)
                {
                    $this->addFlash("danger"," Ocurrió un error, ningún producto puede ser emitido con cantidad cero (0). Vuelva a registrar la nota.");
                    return $this->redirectToRoute('notadebito_index');                    
                }

                $cantidad  = $detalle->getCantidad();
                $precio    = $detalle->getPrecio();
                $subtotal  = $detalle->getSubtotal();
                $descuento = ($detalle->getDescuento()) ? $detalle->getDescuento() : 0;
                $precio_vu = $precio/1.18;

                $tipo_de_igv = $detalle->getTipoImpuesto()->getId();
                $igv_valor   = $detalle->getTipoImpuesto()->getValor();

                $valor_unitario  = ($cantidad > 0) ? $precio_vu : 0;
                $igv             = $cantidad * $valor_unitario * $igv_valor;

                $precio_unitario = ($cantidad > 0) ? $precio : 0;

                switch ($tipo_de_igv) {
                    case 1:
                        $total_igv       = $total_igv + $igv;
                        $total_gravada   = $total_gravada + $valor_unitario * $cantidad;
                        $total           = $total + $subtotal;                          
                        break;
                    case 2:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;    
                        break;
                    case 3:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;    
                        break;
                    case 4:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;    
                        break;
                    case 5:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;    
                        break;
                    case 6:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;    
                        break;
                    case 7:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;    
                        break;
                    case 8:
                        $total_exonerada = $total_exonerada + $precio_unitario * $cantidad;
                        $total           = $total + $precio_unitario * $cantidad;  
                        break;
                    case 9:
                        $total_inafecta  = $total_inafecta + $precio_unitario * $cantidad;
                        $total           = $total + $total_inafecta;
                        break;
                    case 10:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;
                        break;
                    case 11:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;
                        break;
                    case 12:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;
                        break;
                    case 13:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;
                        break;
                    case 14:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;
                        break;
                    case 15:
                        $total_gratuita  = $total_gratuita + $precio_unitario * $cantidad;
                        break;
                    case 16:
                        $total           = $total + $precio_unitario * $cantidad;
                        $total_inafecta  = $total_inafecta + $precio_unitario * $cantidad;
                        break;                                                                                                                                                                     
                    default:
                        $total_igv       = $total_igv + $igv;
                        $total_gravada   = $total_gravada + $valor_unitario * $cantidad;
                        $total           = $total + $subtotal;                               
                        break;
                }

                $total_descuento = $total_descuento + $descuento;

                //Armamos el array q se va enviar a nubefact
                $tipo           = $detalle->getProductoXLocal()->getProducto()->getTipo();
                $codigo         = $detalle->getProductoXLocal()->getProducto()->getCodigo();
                $descripcion    = $detalle->getProductoXLocal()->getProducto()->getNombre();
                $precio         = $detalle->getPrecio();
                $subtotal       = $detalle->getSubtotal();
                $descuento      = $detalle->getDescuento();
                $tipoImpuesto   = $detalle->getTipoImpuesto()->getId();
                $impuestoValor  = $detalle->getTipoImpuesto()->getValor();

                $productos_array[$j]['id'] = $detalle->getProductoXLocal()->getId();
                $productos_array[$j]['tipo'] = $tipo;
                $productos_array[$j]['codigo'] = $codigo;
                $productos_array[$j]['descripcion'] = $descripcion;
                $productos_array[$j]['cantidad'] = $cantidad;
                $productos_array[$j]['precio'] = $precio;
                $productos_array[$j]['precio_vu'] = $precio_vu;
                $productos_array[$j]['subtotal'] = $subtotal;
                $productos_array[$j]['descuento'] = $descuento;
                $productos_array[$j]['tipoImpuesto'] = $tipoImpuesto;
                $productos_array[$j]['impuestoValor'] = $impuestoValor;

                $j++;              

            }

            $notaCredito->setTotal($total);
            $notaCredito->setTotalGravada($total_gravada);
            $notaCredito->setTotalExonerada($total_exonerada);
            $notaCredito->setTotalGratuita($total_gratuita);
            $notaCredito->setDescuentoXItem($total_descuento);
            $notaCredito->setDescuentoGlobal(0);


            $em->persist($notaCredito);



            try {

                $em->flush();

                if($documento == 'boleta' || $documento == 'factura' )
                {

                    //Enviamos la nota a nubefact
                    $respuesta = $this->container->get('AppBundle\Util\Util')->enviarNotaDebito($notaCredito,$productos_array);
                    $respuesta = json_decode($respuesta, true);
                    

                    if(isset($respuesta['errors']))
                    {
                        $this->addFlash("danger"," Ocurrió un error, el registro no fue enviado.".$respuesta['errors']);
                        return $this->redirectToRoute('notadebito_index');
                    }
                    else
                    {
                        $notaCredito->setEnlacePdf($respuesta['enlace_del_pdf']);
                        $notaCredito->setEnlaceCdr($respuesta['enlace_del_cdr']);
                        $notaCredito->setEnlaceXml($respuesta['enlace_del_xml']);
                        $em->persist($notaCredito);

                        try {

                            $em->flush();
                            
                        } catch (\Exception $e) {

                            $this->addFlash("danger", $e." Ocurrió un error inesperado, la nota no fue recibida en SUNAT.");
                            return $this->redirectToRoute('notadebito_index');
                            
                        }
                        
                    }     

                }


                $this->addFlash("success", "La Nota de Débito fue emitida exitosamente.");
                
            } catch (\Exception $e) {

                $this->addFlash("danger", $e." Ocurrió un error inesperado, el registro no se emitió.");
                
            }

            return $this->redirectToRoute('notadebito_generapdf_electronico',array('id'=>$notaCredito->getId()));
            

        }

        return $this->render('notadebito/new.html.twig', array(
            'titulo'            => 'Registrar Nota de Débito',
            'notaCredito'       => $notaCredito,
            'form'              => $form->createView(),
            'categorias'        => $categorias,
            'formClientePv'     => $formClientePv->createView(),
            'productoXLocals'   => $productoXLocals,
            'tipoCambio'        => $tipoCambio,
            'tiposImpuesto'     => $tiposImpuesto            
        ));
    }

    /**
     * Finds and displays a notaDebito entity.
     *
     * @Route("/{id}", name="notadebito_show")
     * @Method("GET")
     */
    public function showAction(NotaDebito $notaCredito)
    {
        $deleteForm = $this->createDeleteForm($notaCredito);

        return $this->render('notadebito/show.html.twig', array(
            'notaCredito' => $notaCredito,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing notaDebito entity.
     *
     * @Route("/{id}/edit", name="notadebito_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, NotaDebito $notaCredito)
    {
        $editForm = $this->createForm('AppBundle\Form\NotaDebitoType', $notaCredito);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('notadebito_edit', array('id' => $notaCredito->getId()));
        }

        return $this->render('notadebito/edit.html.twig', array(
            'notaCredito' => $notaCredito,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a notaDebito entity.
     *
     * @Route("/{id}", name="notadebito_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, NotaDebito $notaCredito)
    {

        return $this->redirectToRoute('notadebito_index');
    }

    /**
     * Genera documento PDF de venta cuando es nota de credito electronica.
     *
     * @Route("/{id}/generapdfelectronico", name="notadebito_generapdf_electronico")
     * @Method({"GET", "POST"})
     */
    public function generaPDFElectronicoAction(Request $request, NotaDebito $notaCredito)
    {

        $em = $this->getDoctrine()->getManager();
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');

        
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');
        $host           = $request->getHost();

        $localObj  = $em->getRepository('AppBundle:EmpresaLocal')->find($local);


        if($notaCredito->getDocumentoModifica() == 'boleta')
        {

            $f = $this->generateUniqueFileName().'.pdf';
            $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;

            switch ($localObj->getBoletaFormato()) {
                case 'A4':

                    $html = $this->render('notadebito/notadebitoFacturaElectronicaA4.html.twig', array(
                            'notaCredito' => $notaCredito,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 8,'margin-top' => 3,'margin-bottom' => 3));

                break;
                case 'TICKET':

                    $html = $this->render('notadebito/notadebitoBoletaElectronicaTicket.html.twig', array(
                            'notaCredito' => $notaCredito,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' =>  200,'page-width' => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0));

                break;                
            }


            $notaCredito->setEnlacePdfFerretero($ruta_pdf);

            $em->persist($notaCredito);

            try {

                $em->flush();
                
            } catch (\Exception $e) {

                return $e;
                
            }


            return $this->redirectToRoute('notadebito_index');

        }elseif($notaCredito->getDocumentoModifica() == 'factura'){


            $f = $this->generateUniqueFileName().'.pdf';
            $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;    

            switch ($localObj->getFacturaFormato()) {
                case 'A4':

                    $html = $this->render('notadebito/notadebitoFacturaElectronicaA4.html.twig', array(
                            'notaCredito' => $notaCredito,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 10,'margin-top' => 3,'margin-bottom' => 3));

                break;
                case 'TICKET':

                    $html = $this->render('notadebito/notadebitoFacturaElectronicaTicket.html.twig', array(
                            'notaCredito' => $notaCredito,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' =>  200,'page-width' => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                break;                
            }


            $notaCredito->setEnlacePdfFerretero($ruta_pdf);

            $em->persist($notaCredito);

            try {

                $em->flush();
                
            } catch (\Exception $e) {

                return $e;
                
            }

            return $this->redirectToRoute('notadebito_index');


        }


        return new \Symfony\Component\HttpFoundation\Response(
                $pdf, 200, array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'inline; filename="'.$f.'"',
                     
        ));

    }


    /**
     * Anula una nota de debito
     *
     * @Route("/{id}/{msj}/anular", name="notadebito_anular")
     * @Method({"GET","POST"})
     * 
     */
    public function anularAction(Request $request, NotaDebito $notaCredito,$msj)
    {
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $em = $this->getDoctrine()->getManager();

        $localObj = $em->getRepository('AppBundle:EmpresaLocal')->find($local);


        $data_json = $this->generarArchivoJson($notaCredito,$localObj,$msj);
        $respuesta = $this->enviarArchivoJson($data_json,$localObj);
        $leer_respuesta = json_decode($respuesta, true);
        

        $sunat_ticket_numero = '';
        if(!isset($leer_respuesta['errors']))
        {
            $enlacepdf = $leer_respuesta['enlace_del_pdf'];                

            $aceptada_por_sunat  = $leer_respuesta['aceptada_por_sunat'];
            $sunat_description   = $leer_respuesta['sunat_description'];

            $notaCredito->setEstado(false);
            $notaCredito->setMotivoAnulacion($msj);
            $notaCredito->setEnlacePdf($enlacepdf);
            $em->persist($notaCredito);


            
            try {

                $em->flush();
                $this->addFlash("success", "La Nota de Débito fue anulada exitosamente.");

                
            } catch (\Exception $e) {

                $this->addFlash("danger", $e." Ocurrió un error inesperado, el registro no fue anulado.");
                return $this->redirectToRoute('notadebito_index');    
            }

        }
        else
        {

            $this->addFlash("danger", " Ocurrió un error inesperado, el registro no fue anulado.".$leer_respuesta['errors']);
            return $this->redirectToRoute('notadebito_index');    

        }


        return $this->redirectToRoute('notadebito_index');
    }

    private function generarArchivoJson($notaCredito,$localObj,$msj='ERROR DEL SISTEMA')
    {
        $em = $this->getDoctrine()->getManager();


        $serie  = $notaCredito->getSerie();
        $numero = $notaCredito->getNumero();


        switch ($notaCredito->getFacturaVenta()->getDocumento()) {
            case 'factura':
                $tipo_de_comprobante = 4;
                break;
            case 'boleta':
                $tipo_de_comprobante = 4;
                break;            
            default:
                $tipo_de_comprobante = '';
                break;
        }


        $data = array(
            "operacion"                         => "generar_anulacion",
            "tipo_de_comprobante"               => "".$tipo_de_comprobante."",
            "serie"                             => "".$serie."",
            "numero"                            => $numero,
            "motivo"                            => $msj,
            "codigo_unico"                      => ""             
        );
            

        $data_json = json_encode($data);

        return $data_json;
    }


    private function enviarArchivoJson($data_json,$localObj)
    {

        //Invocamos el servicio de NUBEFACT
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$localObj->getUrlFacturacion());
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Token token="'.$localObj->getTokenFacturacion().'"',
            'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        curl_close($ch);

        return $respuesta;
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
