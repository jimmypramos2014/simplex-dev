<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FacturaVenta;
use AppBundle\Entity\PedidoVenta;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\FacturaVentaNubefactError;
use AppBundle\Entity\GuiaRemision;
use AppBundle\Entity\Transferencia;
use AppBundle\Entity\TransferenciaXProducto;
use AppBundle\Entity\Venta;
use AppBundle\Entity\DetalleVenta;
use AppBundle\Entity\VentaFormaPago;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Facturaventum controller.
 *
 * @Route("facturaventa")
 */
class FacturaVentaController extends Controller
{
    const RUTA  = 'https://api.nubefact.com/api/v1/4c9c2b7e-af22-493f-ac90-259e4c8553b3';
    const TOKEN = '27a2a514ea1b459d930707cd9c14d409889a28efcc204354ac8131b39118d4f4';

    /**
     * Lists all facturaVentum entities.
     *
     * @Route("/", name="facturaventa_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $caja           = $session->get('caja');
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $pedidoVentas = $em->getRepository('AppBundle:PedidoVenta')->findBy(array('local'=>$local,'despachado'=>false));

        return $this->render('facturaventa/index.html.twig', array(
            'pedidoVentas' => $pedidoVentas,
            'titulo' => 'Lista de ventas (Editable)'
        ));
    }

    /**
     * Creates a new facturaVentum entity.
     *
     * @Route("/new", name="facturaventa_new")
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

        $pedidoVenta = new PedidoVenta();
        $form = $this->createForm('AppBundle\Form\PedidoVentaType', $pedidoVenta,array('empresa'=>$empresa,'local'=>$local,'caja'=>$caja));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hora = date('H');
            $minuto = date('i');
            $segundo = date('s');

            $intervalo_adicional = 'PT'.$hora.'H'.$minuto.'M'.$segundo.'S';
            $pedidoVenta->getFecha()->add(new \DateInterval($intervalo_adicional));

            $cliente_id     = $request->request->get('cliente_select');
            $cliente        = $em->getRepository('AppBundle:Cliente')->find($cliente_id);

            $pedidoVenta->setCliente($cliente);            
            $pedidoVenta->setLocal($form->getData()->getCaja()->getLocal());
            $pedidoVenta->setDespachado(false);

            //Registramos el tipo de cambio si fue seleccionada como moneda el dolar
            $valor_tipo_cambio = null;
            if($pedidoVenta->getMoneda()->getId() == 2)
            {
                $fecha = $pedidoVenta->getFecha();

                $tipoCambioObj       = $em->getRepository('AppBundle:TipoCambio')->findOneBy(array('empresa'=>$empresa,'fecha'=>$fecha));
                $valor_tipo_cambio   = ($tipoCambioObj)? $tipoCambioObj->getVenta() : '';

                if($valor_tipo_cambio == '')
                {
                    $this->addFlash("danger", " No se pudo registrar el pedido . El tipo de cambio para la fecha seleccionada no existe. Fecha seleccionada: ".$fecha->format('d/m/Y'));
                    return $this->redirectToRoute('facturaventa_new');
                }
                
            }

            $pedidoVenta->setValorTipoCambio($valor_tipo_cambio);

            $em->persist($pedidoVenta);

            try {

                $em->flush();

                $factura = $this->generarFacturaVenta($request,$pedidoVenta);

                $this->addFlash("success", "La venta fue registrada exitosamente.");
                
            } catch (\Exception $e) {

                $this->addFlash("danger", $e." Ocurrió un error inesperado, el registro no se guardó.");
            }
            
            //return $this->redirectToRoute('facturaventa_edit', array('id' => $pedidoVenta->getId()));
            if(!$factura)
            {
                return $this->redirectToRoute('facturaventa_index');

            }

            return $this->redirectToRoute('facturaventa_generar_comprobante_pdf', array('id' => $factura->getId()));

        }

        return $this->render('facturaventa/new.html.twig', array(
            'pedidoVenta'       => $pedidoVenta,
            'form'              => $form->createView(),
            'titulo'            => 'Registrar venta',
            'categorias'        => $categorias,
            'formClientePv'     => $formClientePv->createView(),
            'productoXLocals'   => $productoXLocals,
            'tipoCambio'        => $tipoCambio,
            'tiposImpuesto'    => $tiposImpuesto
        ));
    }

    /**
     * Displays a form to edit an existing facturaVentum entity.
     *
     * @Route("/{id}/edit", name="facturaventa_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, PedidoVenta $pedidoVenta)
    {
        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $caja           = $session->get('caja');
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $empresaObj = $em->getRepository('AppBundle:Empresa')->find($empresa);
        $fecha = new \DateTime();


        $originalTags = new ArrayCollection();

        // Creamos un array collection original del detalle
        foreach ($pedidoVenta->getPedidoVentaDetalle() as $tag) {
            $originalTags->add($tag);
        }

        $categorias     = $this->container->get('AppBundle\Util\Util')->obtenerCategorias($local);
        $formClientePv  = $this->createForm('AppBundle\Form\ClientePvType',null);
        $productoXLocals = $this->container->get('AppBundle\Util\Util')->productosMasVendidosXLocal($local,null);
        $tipoCambio      = $em->getRepository('AppBundle:TipoCambio')->findOneBy(array('empresa'=>$empresa,'fecha'=>$fecha));
        $tiposImpuesto   = $em->getRepository('AppBundle:TipoImpuesto')->findAll();

        $editForm = $this->createForm('AppBundle\Form\PedidoVentaType', $pedidoVenta,array('empresa'=>$empresa,'local'=>$local,'caja'=>$caja));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $hora       = date('H');
            $minuto     = date('i');
            $segundo    = date('s');

            $intervalo_adicional = 'PT'.$hora.'H'.$minuto.'M'.$segundo.'S';
            $pedidoVenta->getFecha()->add(new \DateInterval($intervalo_adicional));
            $pedidoVenta->getFechaVencimiento()->add(new \DateInterval($intervalo_adicional));

            $cliente_id     = $request->request->get('cliente_select');
            $cliente        = $em->getRepository('AppBundle:Cliente')->find($cliente_id);

            $pedidoVenta->setCliente($cliente);

            //Registramos el tipo de cambio si fue seleccionada como moneda el dolar
            $valor_tipo_cambio = null;
            if($pedidoVenta->getMoneda()->getId() == 2)
            {
                $fecha = $pedidoVenta->getFecha();

                $tipoCambioObj       = $em->getRepository('AppBundle:TipoCambio')->findOneBy(array('empresa'=>$empresa,'fecha'=>$fecha));
                $valor_tipo_cambio   = ($tipoCambioObj)? $tipoCambioObj->getVenta() : '';

                if($valor_tipo_cambio == '')
                {
                    $this->addFlash("danger", " No se pudieron registrar los cambios . El tipo de cambio para la fecha seleccionada no existe. Fecha seleccionada: ".$fecha->format('d/m/Y'));
                    return $this->redirectToRoute('facturaventa_edit', array('id' => $pedidoVenta->getId()));
                }

            }

            $pedidoVenta->setValorTipoCambio($valor_tipo_cambio);


            // eliminamos los items del detalle q fueran necearios
            foreach ($originalTags as $tag) {
                if (false === $pedidoVenta->getPedidoVentaDetalle()->contains($tag)) {
                    $em->remove($tag);
                }
            }



            if($request->request->get('btn_opcion') == 'Vender')
            {
                //$pedidoVenta->setDespachado(true);
            }            

            $em->persist($pedidoVenta);


            $factura = $this->editarFacturaVenta($request,$pedidoVenta);

            //Guardamos en factura_venta en caso sea una venta.
            if($request->request->get('btn_opcion') == 'Vender')
            {
                $facturaVenta = $pedidoVenta->getFacturaVenta();
                $facturaVenta->setEmisionElectronica(true);

                //Guardamos el objeto Transferencia
                $transferencia  = new Transferencia();
                $transferencia->setLocalInicio($pedidoVenta->getLocal());
                $transferencia->setLocalFin(null);
                $transferencia->setFecha($fecha);
                
                $transferencia->setEmpresa($empresaObj);
                $usuario    = $em->getRepository('AppBundle:Usuario')->find($session->get('usuario'));
                $transferencia->setUsuario($usuario);

                $transferencia->setNumeroDocumento($facturaVenta->getTicket());
              
                $motivoTraslado  = $em->getRepository('AppBundle:MotivoTraslado')->findOneBy(array('codigo'=>'01'));
                $transferencia->setMotivoTraslado($motivoTraslado);
                $transferencia->setEstado(true);
                $transferencia->setFacturaVenta($facturaVenta);
                $em->persist($transferencia);


                // //Guardamos el detalle de la venta en la tabla detalle_venta
                $productos_array = array();
                $total = 0;
                $j=0;
                $total_gravada      = 0;
                $total_igv          = 0;
                $total              = 0;
                $total_gratuita     = 0;
                $total_descuento    = 0;
                $total_anticipo     = 0;
                $total_inafecta     = 0;
                $total_exonerada    = 0;            
                $total_otros_cargos = 0;

                foreach($pedidoVenta->getPedidoVentaDetalle() as $detalle)
                {
                    if($detalle->getProductoXLocal())
                    {

                        //Guardamos el detalle de la transferencia
                        $transferenciaXProducto = new TransferenciaXProducto();
                        $transferenciaXProducto->setProductoXLocal($detalle->getProductoXLocal());
                        $transferenciaXProducto->setCantidad($detalle->getCantidadPedida());
                        $transferenciaXProducto->setTransferencia($transferencia);
                        $transferenciaXProducto->setPrecio($detalle->getPrecio());
                        $em->persist($transferenciaXProducto);

                        //Armamos el array q se va enviar a nubefact
                        $tipo           = $detalle->getProductoXLocal()->getProducto()->getTipo();
                        $codigo         = $detalle->getProductoXLocal()->getProducto()->getCodigo();
                        $descripcion    = $detalle->getProductoXLocal()->getProducto()->getNombre();
                        $cantidad       = $detalle->getCantidadPedida();
                        $precio         = $detalle->getPrecio();
                        $subtotal       = $detalle->getSubtotal();
                        $tipoImpuesto   = $detalle->getTipoImpuesto()->getId();
                        $impuestoValor  = $detalle->getTipoImpuesto()->getValor();
                        $codigoProductoSunat  = $detalle->getProductoXLocal()->getProducto()->getCodigoSunat()->getCodigo();

                        $productos_array[$j]['id'] = $detalle->getProductoXLocal()->getId();
                        $productos_array[$j]['tipo'] = $tipo;
                        $productos_array[$j]['codigo'] = $codigo;
                        $productos_array[$j]['descripcion'] = $descripcion;
                        $productos_array[$j]['cantidad'] = $cantidad;
                        $productos_array[$j]['precio'] = $precio;
                        $productos_array[$j]['subtotal'] = $subtotal;
                        $productos_array[$j]['tipoImpuesto'] = $tipoImpuesto;
                        $productos_array[$j]['impuestoValor'] = $impuestoValor;
                        $productos_array[$j]['codigoProductoSunat'] = $codigoProductoSunat;

                        if($pedidoVenta->getSinIgv())
                        {
                            $valor_unitario     = ($cantidad > 0) ? $precio : 0;
                        }
                        else
                        {
                            $valor_unitario     = ($cantidad > 0) ? ($subtotal/$cantidad)/(1 + $impuestoValor) : 0;
                        }

                        $precio_unitario    = ($cantidad > 0) ? $valor_unitario * (1 + $impuestoValor) : 0;
                        $igv                = $cantidad * $valor_unitario * $impuestoValor;

                        switch ($tipoImpuesto) 
                        {
                            case 1:
                                $total_igv       = $total_igv + $igv;
                                $total_gravada   = $total_gravada + $valor_unitario * $cantidad;
                                $total           = ($pedidoVenta->getSinIgv()) ? $total + $subtotal * (1 + $impuestoValor) : $total + $subtotal;                          
                                break;
                            case 2:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                                break;
                            case 3:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                                break;
                            case 4:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                                break;
                            case 5:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                                break;
                            case 6:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                                break;
                            case 7:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                                break;
                            case 8:
                                $total_exonerada = $total_exonerada + $precio * $cantidad;
                                break;
                            case 9:
                                $total_inafecta  = $total_inafecta + $precio * $cantidad;
                                break;
                            case 10:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;
                                break;
                            case 11:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;
                                break;
                            case 12:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;
                                break;
                            case 13:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;
                                break;
                            case 14:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;
                                break;
                            case 15:
                                $total_gratuita  = $total_gratuita + $precio * $cantidad;
                                break;
                            case 16:
                                $total           = $total + $precio * $cantidad;
                                $total_inafecta  = $total_inafecta + $precio * $cantidad;
                                break;                                                                                                                                                                     
                            default:
                                $total_igv       = $total_igv + $igv;
                                $total_gravada   = $total_gravada + $valor_unitario * $cantidad;
                                $total           = $total + $subtotal;                               
                                break;
                        }


                        $j++;                        

                    }
                 
                }

                $total  = $total + $total_exonerada + $total_inafecta;

                if($facturaVenta->getDocumento() == 'boleta' || $facturaVenta->getDocumento() == 'factura' )
                {

                    //Enviamos la venta a nubefact
                    $respuesta = $this->container->get('AppBundle\Util\Util')->enviarVenta($facturaVenta,$pedidoVenta,$productos_array);
                    $respuesta = json_decode($respuesta, true);
                    

                    if(isset($respuesta['errors']))
                    {
                        $this->addFlash("danger"," Ocurrió un error, el registro no fue enviado.".$respuesta['errors']);
                        return $this->redirectToRoute('facturaventa_edit', array('id' => $pedidoVenta->getId()));
                    }
                    else
                    {
                        $facturaVenta->setEnlacepdf($respuesta['enlace_del_pdf']);
                        $facturaVenta->setEnlaceCdr($respuesta['enlace_del_cdr']);
                        $facturaVenta->setEnlaceXml($respuesta['enlace_del_xml']);
                        $facturaVenta->setEnviadoSunat(true);
                        $em->persist($facturaVenta);

                        $pedidoVenta->setDespachado(true);
                        $em->persist($pedidoVenta);
                    }     

                }


    

            }



            try {

                $em->flush();

                

                // Actualizamos el stock y registramos los movimientos en caja
                if($request->request->get('btn_opcion') == 'Vender')
                {

                    $forma_pago = $pedidoVenta->getFormaPago()->getCodigo();
                    $monto_a_cuenta = $pedidoVenta->getMontoACuenta();

                    switch ($forma_pago) 
                    {
                        case '1'://Contado
                            $monto_en_caja = $total;
                            break;
                        case '2'://Crédito
                            $monto_en_caja = 0;
                            break;                                                            
                        case '3'://Tarjeta de credito
                            $monto_en_caja = $total;
                            break;
                        case '4'://Nota de credito
                            $monto_en_caja = 0;
                            break;
                        case '5'://A cuenta
                            $monto_en_caja = $monto_a_cuenta;
                            break;
                        case '6'://Cheque
                            $monto_en_caja = $total;
                            break;                                                            
                        case '7'://Letra
                            $monto_en_caja = $total;
                            break;
                        case '8'://Depósito Transferencia
                            $monto_en_caja = $total;
                            break;                                                                         
                        default://por defecto
                            $monto_en_caja = $total;
                            break;
                    }    

                              
                    //Obtenermos el id de la caja apertura
                    $caja_apertura_id = $session->get('caja_apertura');

                    if(!$session->get('caja_apertura')){

                        $dql = "SELECT ca FROM AppBundle:CajaApertura ca ";
                        $dql .= " JOIN ca.caja c";
                        $dql .= " WHERE  c.id =:caja_id  AND ca.estado = 1 ";

                        $query = $em->createQuery($dql);

                        $query->setParameter('caja_id',$pedidoVenta->getCaja()->getId());
                 
                        $cajaApertura = $query->getSingleResult();

                        $caja_apertura_id = $cajaApertura->getId();

                        $session->set('caja_apertura',$caja_apertura_id);

                    }

                    //Registramos la entrada de dinero en caja
                    $this->container->get('AppBundle\Util\Util')->registrarCajaAperturaDetalle($caja_apertura_id,$total,'venta',$facturaVenta->getId());

                    if($facturaVenta->getLocal()->getCajaybanco()){
                        //Guardamos pago de transaccion
                        $this->container->get('AppBundle\Util\UtilCajaBanco')->guardarPagoTransaccion($facturaVenta,$monto_en_caja,'v');
                    }

                    //Reducimos el stock
                    foreach($productos_array as $j=>$producto)
                    {
                        $this->container->get('AppBundle\Util\Util')->disminuirAlmacen($producto['id'],$producto['cantidad']);
                    }
                    

                    $this->addFlash("success", "La venta fue realizada exitosamente. Ver documento en la lista de ventas. Factura Nro. : ".$facturaVenta->getTicket());

                    return $this->redirectToRoute('facturaventa_index');

                }

                if(!$factura)
                {
                    $this->addFlash("warning", "Hubo un error en el registro de la venta.");

                    return $this->redirectToRoute('facturaventa_index');

                }

                $this->addFlash("success", "La venta fue editada exitosamente.");

                return $this->redirectToRoute('facturaventa_generar_comprobante_pdf', array('id' => $factura->getId()));


                
                
            } catch (\Exception $e) {

                $this->addFlash("danger", $e." Ocurrió un error inesperado, el registro no se guardó.");
            }


            return $this->redirectToRoute('facturaventa_edit', array('id' => $pedidoVenta->getId()));
        }

        return $this->render('facturaventa/edit.html.twig', array(
            'pedidoVenta'       => $pedidoVenta,
            'edit_form'         => $editForm->createView(),
            'titulo'            => 'Editar venta',
            'categorias'        => $categorias,
            'formClientePv'     => $formClientePv->createView(),
            'productoXLocals'   => $productoXLocals,
            'tipoCambio'        => $tipoCambio,
            'tiposImpuesto'     => $tiposImpuesto,         
        ));
    }

    /**
     * Finds and displays a facturaVenta entity.
     *
     * @Route("/{id}", name="facturaventa_show")
     * @Method({"POST","GET"})
     */
    public function showAction(Request $request,FacturaVenta $facturaVenta)
    {
        $em = $this->getDoctrine()->getManager();
        
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $host           = $request->getHost();

        $localObj  = $em->getRepository('AppBundle:EmpresaLocal')->find($local);
        $empresaObj  = $em->getRepository('AppBundle:Empresa')->find($empresa);

        $formato_ferretero = $empresaObj->getFormatoFerretero();

        // if( ($facturaVenta->getDocumento() == 'boleta' || $facturaVenta->getDocumento() == 'factura') && $facturaVenta->getVenta()->getVentaFormaPago()[0]->getFormaPago()->getCodigo() != '5'){

        //     $leer_respuesta = array();
        //     if($localObj->getUrlFacturacion() != '' && $localObj->getTokenFacturacion() != '' && $localObj->getFacturacionElectronica() != false)
        //     {

        //         $data_json = $this->container->get('AppBundle\Util\Util')->generarArchivoJson($facturaVenta,$local);
        //         $respuesta = $this->container->get('AppBundle\Util\Util')->enviarArchivoJson($data_json,$localObj);
        //         $leer_respuesta = json_decode($respuesta, true);


        //     }


        //     if (isset($leer_respuesta['errors'])) {

        //         foreach($facturaVenta->getVenta()->getDetalleVenta() as $detalle)
        //         {
        //             if($detalle->getProductoXLocal()){

        //                 $productoXLocal = $detalle->getProductoXLocal();
        //                 $cantidad       = $detalle->getCantidad();
        //                 $this->container->get('AppBundle\Util\Util')->aumentarAlmacen($productoXLocal->getId(),$cantidad);

        //             }

        //         }

        //         $em->remove($facturaVenta);

        //         $nferror  = $em->getRepository('AppBundle:NubefactError')->findOneBy(array('codigo'=>$leer_respuesta['codigo']));
        //         $msj_error = ($nferror) ? $nferror->getDescripcion().' '.$leer_respuesta['errors'] : 'Error no identificado';

      
        //         try {

        //             $em->flush();

        //             $this->addFlash("danger", $msj_error);
                               
        //         } catch (Exception $e) {
        //             //Mostramos los errores si los hay
        //             $this->addFlash("danger", $e." La factura no pudo ser generada en SUNAT,volver a intentarlo. Si el problema persiste contactar con el administrador del sistema.");         
        //         }    

        //         return $this->redirectToRoute('detalleventa_puntoventa');


        //     }
        //     else
        //     {

        //         if($localObj->getUrlFacturacion() != '' && $localObj->getTokenFacturacion() != '' && $localObj->getFacturacionElectronica() != false)
        //         {

        //             if($leer_respuesta['aceptada_por_sunat'] != 'true'){

        //                 $enlace_de_pdf = $leer_respuesta['enlace'].'.pdf';

        //                 if($facturaVenta->getDocumento() == 'factura')
        //                 {
        //                     $facturaVenta->setEnviadoSunat(false);
        //                 }
        //                 else
        //                 {
        //                     $facturaVenta->setEnviadoSunat(true);
        //                 }

                        
        //                 $facturaVenta->setEnlacepdf($enlace_de_pdf);

        //             }
        //             else
        //             {
        //                 $enlace_de_pdf = $leer_respuesta['enlace_del_pdf'];

        //                 $facturaVenta->setEnviadoSunat(true);
        //                 $facturaVenta->setEnlacepdf($leer_respuesta['enlace_del_pdf']);

        //             }


                    
        //             $em->persist($facturaVenta);

        //             try {

        //                 $em->flush();

        //                 //Get the file
        //                 $content = file_get_contents($enlace_de_pdf);
        //                 file_put_contents("uploads/files/recibo.pdf",$content);

        //                 if($facturaVenta->getDocumento() == 'factura'  && $facturaVenta->getFacturaEnviada() == true)
        //                 {
        //                     if($facturaVenta->getCliente()->getEmail()){

        //                         $email = $facturaVenta->getCliente()->getEmail();

        //                         $this->enviarFactura($email,$leer_respuesta['enlace_del_pdf'],$empresaObj->getCorreoRemitente(),$facturaVenta);

        //                     }
                            

        //                 }
                                                
                        
        //             } catch (\Exception $e) {

        //                 return $e;
                        
        //             }

        //             return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/recibo.pdf')));


        //         }
        //         else
        //         {

        //             return $this->redirectToRoute('facturaventa_generapdf',array('id'=>$facturaVenta->getId()));

        //         }

        //     }

        // }
        // else
        // {
        //     return $this->redirectToRoute('facturaventa_generapdf',array('id'=>$facturaVenta->getId()));

        // }


        if( ($facturaVenta->getDocumento() == 'boleta' || $facturaVenta->getDocumento() == 'factura') && $facturaVenta->getVenta()->getVentaFormaPago()[0]->getFormaPago()->getCodigo() != '5'){

            $leer_respuesta = array();
            if($localObj->getUrlFacturacion() != '' && $localObj->getTokenFacturacion() != '' && $localObj->getFacturacionElectronica() != false)
            {

                $data_json = $this->container->get('AppBundle\Util\Util')->generarArchivoJson($facturaVenta,$local);
                $respuesta = $this->container->get('AppBundle\Util\Util')->enviarArchivoJson($data_json,$localObj);
                $leer_respuesta = json_decode($respuesta, true);
              

                if (isset($leer_respuesta['errors'])) {


                    if(!$formato_ferretero)
                    {

                        foreach($facturaVenta->getVenta()->getDetalleVenta() as $detalle)
                        {
                            if($detalle->getProductoXLocal()){

                                $productoXLocal = $detalle->getProductoXLocal();
                                $cantidad       = $detalle->getCantidad();
                                $this->container->get('AppBundle\Util\Util')->aumentarAlmacen($productoXLocal->getId(),$cantidad);

                            }

                        }

                        $transferencia = $em->getRepository('AppBundle:Transferencia')->findOneBy(array('facturaVenta'=>$facturaVenta));
                        $cajaAperturaDetalle = $em->getRepository('AppBundle:CajaAperturaDetalle')->findOneBy(array('identificador'=>$facturaVenta->getId()));

                        $em->remove($cajaAperturaDetalle);
                        $em->remove($transferencia);                
                        $em->remove($facturaVenta);

                        $nferror  = $em->getRepository('AppBundle:NubefactError')->findOneBy(array('codigo'=>$leer_respuesta['codigo']));
                        $msj_error = ($nferror) ? $nferror->getDescripcion().' '.$leer_respuesta['errors'] : 'Error no identificado';

              
                        try {

                            $em->flush();

                            $this->addFlash("danger", $msj_error);
                                       
                        } catch (Exception $e) {
                            //Mostramos los errores si los hay
                            $this->addFlash("danger", $e." La factura no pudo ser generada en SUNAT,volver a intentarlo. Si el problema persiste contactar con el administrador del sistema.");



                            $response = new Response($e);

                            return $response;

                        }    

                        return $this->redirectToRoute('detalleventa_puntoventa');

                    }
                    else
                    {

                        $nferror  = $em->getRepository('AppBundle:NubefactError')->findOneBy(array('codigo'=>$leer_respuesta['codigo']));
                        $msj_error = ($nferror) ? $nferror->getDescripcion() : '';  

                        $facturaVentaNubefactError = new FacturaVentaNubefactError();
                        $facturaVentaNubefactError->setFacturaVenta($facturaVenta);
                        $facturaVentaNubefactError->setError($leer_respuesta['errors']);
                        $facturaVentaNubefactError->setSunatResponsecode($leer_respuesta['codigo']);
                        $facturaVentaNubefactError->setSunatDescription($msj_error);

                        $em->persist($facturaVentaNubefactError);

                        try {

                            $em->flush();
                            
                        } catch (\Exception $e) {
                            
                            return $e;
                        }

                    }


                }
                else
                {
                    if(!$formato_ferretero)
                    {

                        if($localObj->getUrlFacturacion() != '' && $localObj->getTokenFacturacion() != '' && $localObj->getFacturacionElectronica() != false)
                        {

                            if($leer_respuesta['aceptada_por_sunat'] != 'true'){

                                $enlace_de_pdf = ($leer_respuesta['enlace']) ? $leer_respuesta['enlace'].'.pdf' : $leer_respuesta['enlace_del_pdf'];

                                if($facturaVenta->getDocumento() == 'factura')
                                {
                                    $facturaVenta->setEnviadoSunat(false);
                                }
                                else
                                {
                                    $facturaVenta->setEnviadoSunat(false);
                                }

                                $facturaVenta->setEnlacepdf($enlace_de_pdf);
                                
                            }
                            else
                            {
                                $enlace_de_pdf = $leer_respuesta['enlace_del_pdf'];

                                $facturaVenta->setEnviadoSunat(true);
                                $facturaVenta->setEnlacepdf($enlace_de_pdf);

                            }


                            //Si no se genera el PDF, volvemos a consultar el documento en nubefact
                            if($enlace_de_pdf == '.pdf' || is_null($enlace_de_pdf) || $enlace_de_pdf == '')
                            {
                                $url_facturacion_electronica   = $facturaVenta->getLocal()->getUrlFacturacion();
                                $token_facturacion_electronica = $facturaVenta->getLocal()->getTokenFacturacion();

                                $data_json_reenvio      = $this->container->get('AppBundle\Util\Util')->generarConsultaArchivoJson($facturaVenta);
                                $respuesta_reenvio      = $this->container->get('AppBundle\Util\Util')->enviarConsultaArchivoJson($data_json_reenvio,$url_facturacion_electronica,$token_facturacion_electronica);
                                $leer_respuesta_reenvio = json_decode($respuesta_reenvio, true);

                                if(!isset($leer_respuesta_reenvio['errors']))
                                {
                                    $enlace_de_pdf = ($leer_respuesta_reenvio['enlace_del_pdf']) ? $leer_respuesta_reenvio['enlace_del_pdf'] : $leer_respuesta_reenvio['enlace'].'.pdf';
                                    $facturaVenta->setEnlacepdf($enlace_de_pdf);
                                }

                            }

                            //Fin de consulta
                            $facturaVenta->setCodigoError($leer_respuesta['sunat_responsecode']);
                            $facturaVenta->setMensajeError($leer_respuesta['sunat_description']);
                            $facturaVenta->setEnlaceXml($leer_respuesta['enlace_del_xml']);
                            $facturaVenta->setEnlaceCdr($leer_respuesta['enlace_del_cdr']);
                                                
                            $em->persist($facturaVenta);

                            

                            try {

                                $em->flush();


                                //Get the file
                                $content = file_get_contents($enlace_de_pdf);
                                file_put_contents("uploads/files/".$empresa."/".$facturaVenta->getLocal()->getId()."/recibo.pdf",$content);

                                if($facturaVenta->getDocumento() == 'factura'  && $facturaVenta->getFacturaEnviada() == true)
                                {
                                    if($facturaVenta->getCliente()->getEmail()){

                                        $email = $facturaVenta->getCliente()->getEmail();

                                        $this->enviarFactura($email,$leer_respuesta['enlace_del_pdf'],$empresaObj->getCorreoRemitente(),$facturaVenta);

                                    }
                                    
                                }                            

                                                        
                                
                            } catch (\Exception $e) {

                                $this->addFlash("danger"," Ocurrió un error inesperado, Imprimir la factura desde la lista de ventas.");

                                $response = new Response($e);
                                return $response;
                                
                            }


                            return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$facturaVenta->getLocal()->getId().'/recibo.pdf')));



                        }
                        else
                        {

                            return $this->redirectToRoute('facturaventa_generapdf',array('id'=>$facturaVenta->getId()));

                        }

                    }
                    else
                    {
                        $enlace_de_pdf = '';
                        if($leer_respuesta['aceptada_por_sunat'] != 'true')
                        {

                            $enlace_de_pdf = ($leer_respuesta['enlace']) ? $leer_respuesta['enlace'].'.pdf' : $leer_respuesta['enlace_del_pdf'];

                            if($facturaVenta->getDocumento() == 'factura')
                            {
                                $facturaVenta->setEnviadoSunat(false);
                            }
                            else
                            {
                                $facturaVenta->setEnviadoSunat(true);
                            }
                           
                        }
                        else
                        {
                            $enlace_de_pdf = $leer_respuesta['enlace_del_pdf'];
                            $facturaVenta->setEnviadoSunat(true);
                            
                        }

                        //$facturaVenta->setEnlacepdf($enlace_de_pdf);

                        //Si no se genera el PDF, volvemos a consultar el documento en nubefact
                        // if($enlace_de_pdf == '.pdf' || is_null($enlace_de_pdf) || $enlace_de_pdf == '')
                        // {
                        //     $url_facturacion_electronica   = $facturaVenta->getLocal()->getUrlFacturacion();
                        //     $token_facturacion_electronica = $facturaVenta->getLocal()->getTokenFacturacion();

                        //     $data_json_reenvio      = $this->container->get('AppBundle\Util\Util')->generarConsultaArchivoJson($facturaVenta);
                        //     $respuesta_reenvio      = $this->container->get('AppBundle\Util\Util')->enviarConsultaArchivoJson($data_json_reenvio,$url_facturacion_electronica,$token_facturacion_electronica);
                        //     $leer_respuesta_reenvio = json_decode($respuesta_reenvio, true);


                        //     if(!isset($leer_respuesta_reenvio['errors']))
                        //     {
                        //         $enlace_de_pdf = ($leer_respuesta_reenvio['enlace_del_pdf']) ? $leer_respuesta_reenvio['enlace_del_pdf'] : $leer_respuesta_reenvio['enlace'].'.pdf';
                                
                        //     }

                        // }
                        //Fin de consulta

                        $facturaVenta->setEnlacepdf($enlace_de_pdf);
                        $em->persist($facturaVenta);


                        try {

                            $em->flush();

                            if($facturaVenta->getDocumento() == 'factura'  && $facturaVenta->getFacturaEnviada() == true)
                            {
                                if($facturaVenta->getCliente()->getEmail()){

                                    $email = $facturaVenta->getCliente()->getEmail();
                                    $enlace_documento = ($facturaVenta->getEnlacePdfFerretero()) ? $facturaVenta->getEnlacePdfFerretero() : $facturaVenta->getEnlacepdf();
                                    $this->enviarFactura($email,$enlace_documento,$empresaObj->getCorreoRemitente(),$facturaVenta);

                                }
                                

                            }                        
                            
                        } catch (\Exception $e) {

                            $this->addFlash("danger"," Ocurrió un error inesperado, Imprimir la factura desde la lista de ventas.");

                            $response = new Response($e);
                            return $response;
                            
                        }


                        return $this->redirectToRoute('facturaventa_generapdf_electronico',array('id'=>$facturaVenta->getId()));              

                    }

                }


                return $this->redirectToRoute('facturaventa_generapdf_electronico',array('id'=>$facturaVenta->getId()));

            }
            else
            {

                return $this->redirectToRoute('facturaventa_generapdf',array('id'=>$facturaVenta->getId()));

            }


        }
        else
        {
            return $this->redirectToRoute('facturaventa_generapdf',array('id'=>$facturaVenta->getId()));

        }


        return $this->redirectToRoute('detalleventa_puntoventa');

    }


    /**
     * Genera documento PDF de venta cuando es facturacion electronnica, factura,boleta.
     *
     * @Route("/{id}/generapdfelectronico", name="facturaventa_generapdf_electronico")
     * @Method({"GET", "POST"})
     */
    public function generaPDFElectronicoAction(Request $request, FacturaVenta $facturaVenta)
    {

        $em = $this->getDoctrine()->getManager();
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');

        
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');
        $host           = $request->getHost();

        $localObj  = $em->getRepository('AppBundle:EmpresaLocal')->find($local);


        if($facturaVenta->getDocumento() == 'boleta')
        {

            $f = $this->generateUniqueFileName().'.pdf';
            $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;

            switch ($localObj->getBoletaFormato()) {
                case 'A4':

                    $html = $this->render('facturaventa/boletaElectronicaA4.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 8,'margin-top' => 3,'margin-bottom' => 3));

                break;
                case 'TICKET':

                    $html = $this->render('facturaventa/boletaElectronicaTicket.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' =>  200,'page-width' => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0));

                break;                
            }


            $facturaVenta->setEnlacePdfFerretero($ruta_pdf);

            $em->persist($facturaVenta);

            try {

                $em->flush();
                
            } catch (\Exception $e) {

                return $e;
                
            }

            //return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f)));

            return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f,'facturaId'=>$facturaVenta->getId())));

        }elseif($facturaVenta->getDocumento() == 'factura'){


            $f = $this->generateUniqueFileName().'.pdf';
            $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;    

            switch ($localObj->getFacturaFormato()) {
                case 'A4':

                    $html = $this->render('facturaventa/facturaElectronicaA4.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 10,'margin-top' => 3,'margin-bottom' => 3));

                break;
                case 'TICKET':

                    $html = $this->render('facturaventa/facturaElectronicaTicket.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' =>  200,'page-width' => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                break;                
            }


            $facturaVenta->setEnlacePdfFerretero($ruta_pdf);

            $em->persist($facturaVenta);

            try {

                $em->flush();
                
            } catch (\Exception $e) {

                return $e;
                
            }

            //return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f)));

            return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f,'facturaId'=>$facturaVenta->getId())));


        }


        return new \Symfony\Component\HttpFoundation\Response(
                $pdf, 200, array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'inline; filename="'.$f.'"',
                     
        ));

    }



    public function showGuiaAction(Request $request,$facturaId=null)
    {
        $em = $this->getDoctrine()->getManager();

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $host           = $request->getHost();

        $facturaVenta = null;
        if($facturaId){
            $facturaVenta   = $em->getRepository('AppBundle:FacturaVenta')->find((int)$facturaId);
        }
        
        $localObj   = $em->getRepository('AppBundle:EmpresaLocal')->find($local);


        return $this->render('facturaventa/showGuia.html.twig', array(
            'facturaVenta' => $facturaVenta,
            //'localObj' => $localObj,
        ));

    }



    /**
     * Genera documento PDF de venta , factura,boleta,ticket o recibo.
     *
     * @Route("/{id}/generapdf", name="facturaventa_generapdf")
     * @Method({"GET", "POST"})
     */
    public function generaPDFAction(Request $request, FacturaVenta $facturaVenta)
    {

        $em = $this->getDoctrine()->getManager();
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');

        
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $host           = $request->getHost();

        $localObj  = $em->getRepository('AppBundle:EmpresaLocal')->find($local);


        if($facturaVenta->getVenta()->getVentaFormaPago()[0]->getFormaPago()->getCodigo() == '5'){


            $dql = "SELECT cxd FROM AppBundle:ComponenteXDocumento cxd ";
            $dql .= " JOIN cxd.documento d";
            $dql .= " JOIN d.local l";
            $dql .= " WHERE  l.id =:local  AND d.codigo = '07' AND cxd.estado = 1  ";

            $query = $em->createQuery($dql);

            $query->setParameter('local',$local);         
     
            $componentesXDocumento =  $query->getResult();

            $html = $this->render('facturaventa/showReciboingreso.html.twig', array(
                    'facturaVenta' => $facturaVenta,
                    'localObj'     => $localObj,
                    'componentesXDocumento' => $componentesXDocumento,
                    'host' => $host
                ))->getContent();

            // $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

            $f = 'reciboingreso_'.$facturaVenta->getTicket();

            $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f . '.pdf',array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

            //return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f . '.pdf')));

            return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f . '.pdf','facturaId'=>$facturaVenta->getId())));


        }else{

            if($facturaVenta->getDocumento() == 'boleta'){

                $dql = "SELECT cxd FROM AppBundle:ComponenteXDocumento cxd ";
                $dql .= " JOIN cxd.documento d";
                $dql .= " JOIN d.local l";
                $dql .= " WHERE  l.id =:local  AND d.codigo = '03' AND cxd.estado = 1  ";

                $query = $em->createQuery($dql);

                $query->setParameter('local',$local);         
         
                $componentesXDocumento =  $query->getResult();

                $html = $this->render('facturaventa/showBoleta.html.twig', array(
                        'facturaVenta' => $facturaVenta,
                        'localObj'     => $localObj,
                        'componentesXDocumento' => $componentesXDocumento,
                        'host' => $host
                    ))->getContent();

                $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                $f = 'boleta_'.$facturaVenta->getTicket();

                $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f . '.pdf',array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                //return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f . '.pdf')));

                return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f . '.pdf','facturaId'=>$facturaVenta->getId())));

            }elseif($facturaVenta->getDocumento() == 'factura'){

                $dql = "SELECT cxd FROM AppBundle:ComponenteXDocumento cxd ";
                $dql .= " JOIN cxd.documento d";
                $dql .= " JOIN d.local l";
                $dql .= " WHERE  l.id =:local  AND d.codigo = '01'  AND cxd.estado = 1 ";

                $query = $em->createQuery($dql);

                $query->setParameter('local',$local);         
         
                $componentesXDocumento =  $query->getResult();

                $cuentasBancarias = $em->getRepository('AppBundle:CuentaBanco')->findBy(array('estado'=>true,'empresa'=>$empresa),array('banco'=>'ASC'));

                $html = $this->render('facturaventa/showFactura.html.twig', array(
                        'facturaVenta' => $facturaVenta,
                        'localObj'     => $localObj,
                        'cuentasBancarias' => $cuentasBancarias,
                        'componentesXDocumento' => $componentesXDocumento,
                        'host' => $host
                    ))->getContent();

                // $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html, array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0)); 

                $f = 'factura_'.$facturaVenta->getTicket();               


                $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f . '.pdf',array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));


                //return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f . '.pdf')));

                return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f . '.pdf','facturaId'=>$facturaVenta->getId())));


            }else{

                switch ($localObj->getNotaventaFormato()) {
                    case 'A4':

                        $dql = "SELECT cxd FROM AppBundle:ComponenteXDocumento cxd ";
                        $dql .= " JOIN cxd.documento d";
                        $dql .= " JOIN d.local l";
                        $dql .= " WHERE  l.id =:local  AND d.codigo = '03' AND cxd.estado = 1  ";

                        $query = $em->createQuery($dql);

                        $query->setParameter('local',$local);         
                 
                        $componentesXDocumento =  $query->getResult();

                        $html = $this->render('facturaventa/showGuiaA4.html.twig', array(
                                'facturaVenta' => $facturaVenta,
                                'localObj'     => $localObj,
                                'componentesXDocumento' => $componentesXDocumento,
                                'host' => $host
                            ))->getContent();


                        $f = $this->generateUniqueFileName().'.pdf';//'notaventa_'.$facturaVenta->getTicket();

                        $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                        return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f,'facturaId'=>$facturaVenta->getId())));

                        break;
                    
                    default:

                        $html = $this->render('detalleventa/ticket.html.twig', array(
                                'facturaVenta' => $facturaVenta,
                                'localObj'     => $localObj,
                                'host' => $host
                            ))->getContent();

                        $f = $this->generateUniqueFileName().'.pdf';


                        $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' => 210,'page-width'  => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                        return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f,'facturaId'=>$facturaVenta->getId())));


                        //return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('guiaHtml'=>'si','facturaId'=>$facturaVenta->getId())));

                        break;

                }


                return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>'uploads/files/'.$empresa.'/'.$f . '.pdf','facturaId'=>$facturaVenta->getId())));


            }


        }

        return new \Symfony\Component\HttpFoundation\Response(
                $pdf, 200, array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'inline; filename="'.$f.'.pdf"',
                     
        ));


    }

    /**
     * Genera documento PDF de venta desde nubefact: factura,boleta,ticket,nota de credito.
     *
     * @Route("/{id}/{enlacepdf}/generapdfnubefact", name="facturaventa_generapdf_nubefact")
     * @Method({"GET", "POST"})
     */
    public function generaPDFNubefactAction(Request $request, FacturaVenta $facturaVenta,$enlacepdf)
    {

        $em = $this->getDoctrine()->getManager();
        
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        return $this->redirect($this->generateUrl('detalleventa_puntoventa',array('rutaPdf'=>$enlacepdf)));

        //return $this->redirectToRoute('facturaventa_edit', array('id' => $facturaVenta->getId()));
    }


    private function generarArchivoJson(FacturaVenta $facturaVenta,$local)
    {
        $em = $this->getDoctrine()->getManager();

        $detalleVenta = array();
        $i=0;
        $total_gravada = 0;
        $total_igv = 0;
        $total = 0;
        foreach($facturaVenta->getVenta()->getDetalleVenta() as $detalle){

            $cantidadLinea      = $detalle->getCantidad();
            $valor_unitario     = ($cantidadLinea > 0) ? ($detalle->getSubtotal()/$cantidadLinea)/1.18 : 0;//($detalle->getSubtotal()/$detalle->getCantidad())/1.18;
            $subt               = $valor_unitario*$cantidadLinea;
            
            $precio_unitario    = $valor_unitario + $valor_unitario*0.18;
            $totalLinea         = $cantidadLinea*$precio_unitario;//$detalle->getSubtotal();
            $igv                = $subt*0.18;


            $descripcion = ($detalle->getDescripcion())? $detalle->getProductoXLocal()->getProducto()->getNombre().' . '.$detalle->getDescripcion():$detalle->getProductoXLocal()->getProducto()->getNombre();
            
            $unidad_medida = "NIU";
            if($detalle->getProductoXLocal()->getProducto()->getTipo() == 'servicio')
            {
               $unidad_medida = "ZZ";     
            }
                    
            $detalleVenta[$i]['unidad_de_medida'] = "".$unidad_medida."";
            $detalleVenta[$i]['codigo'] = "".$detalle->getProductoXLocal()->getProducto()->getCodigo()."";
            $detalleVenta[$i]['descripcion'] = "".$descripcion."";
            $detalleVenta[$i]['cantidad'] = "".$detalle->getCantidad()."";
            $detalleVenta[$i]['valor_unitario'] = "".$valor_unitario."";
            $detalleVenta[$i]['precio_unitario'] = "".$precio_unitario."";
            $detalleVenta[$i]['descuento'] = "";
            $detalleVenta[$i]['subtotal'] = "".$subt."";
            $detalleVenta[$i]['tipo_de_igv'] = "1";
            $detalleVenta[$i]['igv'] = "".$igv."";
            $detalleVenta[$i]['total'] = "".$totalLinea."";
            $detalleVenta[$i]['anticipo_regularizacion'] = "false";
            $detalleVenta[$i]['anticipo_documento_serie'] = "";
            $detalleVenta[$i]['anticipo_documento_numero'] = "";

            $i++;
            $total_igv  = $total_igv + $igv;
            $total      = $total + $detalle->getSubtotal();
            $total_gravada = $total_gravada + $subt;
        }



        $partesticket = explode("-", $facturaVenta->getTicket());//$this->generarNumero($local);//
        $numero = $partesticket[1];

        $localObj = $em->getRepository('AppBundle:EmpresaLocal')->find($local);

        switch ($facturaVenta->getDocumento()) {
            case 'factura':
                $serie = $localObj->getSerieFactura();
                $tipo_de_comprobante = 1;
                $cliente_tipo_de_documento = 6;
                break;
            case 'boleta':
                $serie = $localObj->getSerieBoleta();
                $cliente_tipo_de_documento = 1;
                $tipo_de_comprobante = 2;
                break;            
            default:
                $serie = '';
                $tipo_de_comprobante = '';
                break;
        }

        $total_gravada = $total/1.18;
        $total_igv = $total - $total_gravada;

        if($facturaVenta->getCliente()->getRuc() == '---'){

            $cliente_tipo_de_documento = '-';

        }

        $detraccion = 'false';
        if($facturaVenta->getDetraccion()){

            $detraccion = 'true';

        }

        $condicion_de_pago = '';
        $ventaFormaPago = $facturaVenta->getVenta()->getVentaFormaPago()[0];

        if($ventaFormaPago){

            $condicion_de_pago = strtoupper($ventaFormaPago->getFormaPago()->getNombre());

            if($ventaFormaPago->getFormaPago()->getCodigo() == '2'){
                $condicion_de_pago .= ' A '.$ventaFormaPago->getNumeroDias().' DIAS';
            }
        }

        $orden_compra = ($facturaVenta->getOrdenCompra())? $facturaVenta->getOrdenCompra():'';



        $data = array(
            "operacion"                         => "generar_comprobante",
            "tipo_de_comprobante"               => "".$tipo_de_comprobante."",
            "serie"                             => "".$serie."",
            "numero"                            => $numero,
            "sunat_transaction"                 => "1",
            "cliente_tipo_de_documento"         => "".$cliente_tipo_de_documento."",
            "cliente_numero_de_documento"       => "".$facturaVenta->getCliente()->getRuc()."",
            "cliente_denominacion"              => "".$facturaVenta->getCliente()->getRazonSocial()."",
            "cliente_direccion"                 => "".$facturaVenta->getCliente()->getDireccion()."",
            "cliente_email"                     => "",
            "cliente_email_1"                   => "",
            "cliente_email_2"                   => "",
            "fecha_de_emision"                  => date('d-m-Y'),
            "fecha_de_vencimiento"              => "",
            "moneda"                            => "1",
            "tipo_de_cambio"                    => "",
            "porcentaje_de_igv"                 => "18.00",
            "descuento_global"                  => "",
            "descuento_global"                  => "",
            "total_descuento"                   => "",
            "total_anticipo"                    => "",
            "total_gravada"                     => "".round($total_gravada,2)."",
            "total_inafecta"                    => "",
            "total_exonerada"                   => "",
            "total_igv"                         => "".round($total_igv,2)."",
            "total_gratuita"                    => "",
            "total_otros_cargos"                => "",
            "total"                             => "".round($total,2)."",
            "percepcion_tipo"                   => "",
            "percepcion_base_imponible"         => "",
            "total_percepcion"                  => "",
            "total_incluido_percepcion"         => "",
            "detraccion"                        => "".$detraccion."",
            "observaciones"                     => "",
            "documento_que_se_modifica_tipo"    => "",
            "documento_que_se_modifica_serie"   => "",
            "documento_que_se_modifica_numero"  => "",
            "tipo_de_nota_de_credito"           => "",
            "tipo_de_nota_de_debito"            => "",
            "enviar_automaticamente_a_la_sunat" => "true",
            "enviar_automaticamente_al_cliente" => "false",
            "codigo_unico"                      => "",
            "condiciones_de_pago"               => "".$condicion_de_pago."",
            "medio_de_pago"                     => "",
            "placa_vehiculo"                    => "",
            "orden_compra_servicio"             => "".$orden_compra."",
            "tabla_personalizada_codigo"        => "",
            "formato_de_pdf"                    => "",
            "items" => $detalleVenta
        );
            
        // dump($data);
        // die();

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



    private function generarNumero($local)
    {
        $em = $this->getDoctrine()->getManager();

        $ultimo_id = $em->createQueryBuilder()
            ->select('MAX(fv.id)')
            ->from('AppBundle:FacturaVenta', 'fv')
            ->leftJoin('fv.venta', 'v')
            ->leftJoin('v.empleado', 'e')
            ->leftJoin('e.local', 'l')
            ->where('l.id=:local')
            ->setParameter('local', $local)
            ->getQuery()
            ->getSingleScalarResult();


        return $ultimo_id;

    }

    private function enviarFactura($email = '',$enlace = '',$correo_remitente = '',$facturaVenta = null)
    {
        $request        = $this->get('request_stack')->getCurrentRequest();
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $em = $this->getDoctrine()->getManager();

        $empresaObj = $em->getRepository('AppBundle:Empresa')->find($empresa);
        $nombreCorto = ($empresaObj->getNombreCorto()) ? $empresaObj->getNombreCorto() : 'FERRETERO';

        $mensaje = 'En el siguiente enlace puede descargar la FACTURA/BOLETA emitida el : '.$facturaVenta->getFecha()->format('d/m/Y').'.';

        $cliente = ($facturaVenta->getCliente()) ? $facturaVenta->getCliente()->getRazonSocial() : 'CLIENTE';

        if($correo_remitente == ''){
            $correo_remitente = 'soporte@intimedia.net';
        }

        if($email != ''){

            $message = \Swift_Message::newInstance()
                    ->setSubject($nombreCorto.' - Factura/Boleta')
                    ->setFrom($correo_remitente)
                    ->setTo($email)
                    ->setContentType('text/html')
                    ->setBody(
                    $this->renderView('detalleventa/enviarFactura.html.twig', array(
                        'enlace'     => $enlace,
                        'mensaje'    => $mensaje,
                        'empresa'    => $nombreCorto,
                        'cliente'    => $cliente
                    ))
                    )
            ;
            

            if($this->get('mailer')->send($message)){

                return true;

            }else{

                return false;

            }

            

        }

        return false;

    }

    /**
     * Factura a imprimir
     *
     * @Route("/{id}/mostrar/comprobante", name="facturaventa_mostrar_comprobante")
     * @Method({"GET", "POST"})
     */
    public function mostrarComprobanteAction(Request $request,FacturaVenta $facturaVenta)
    {
        $em = $this->getDoctrine()->getManager();

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');
        $host           = $request->getHost();

        return $this->render('facturaventa/comprobante.html.twig', array(
            'facturaVenta' => $facturaVenta,
        ));

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

    private function generarFacturaVenta($request,$pedidoVenta)
    {

        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $caja           = $session->get('caja');
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');


        $fecha = $pedidoVenta->getFecha();
        $fecha_vencimiento = $pedidoVenta->getFechaVencimiento();
        $empleado = $em->getRepository('AppBundle:Empleado')->findOneBy(array('usuario'=>$session->get('usuario')));

        //Guardamos en la tabla venta
        $venta = new Venta();
        $venta->setEmpleado($empleado);
        $venta->setFecha($fecha);
        $venta->setEstado(true);
        $venta->setCondicion(false);
        $em->persist($venta);


        //Guardamos en la tabla factura_venta
        $facturaVenta = new FacturaVenta();

        $tipoVenta = $em->getRepository('AppBundle:FacturaVentaTipo')->find(2);
        $facturaVenta->setTipo($tipoVenta);

        $empresa_mismo_prefijo_multilocal = ($pedidoVenta->getLocal()->getEmpresa()->getPermitirMismoPrefijoMultilocal()) ? $empresa : '';

        $ultimoIdFacturaVenta = $this->container->get('AppBundle\Util\Util')->generarNumeroDocumento('factura_venta',$pedidoVenta->getCaja()->getLocal()->getId(),$pedidoVenta->getDocumento(),$empresa_mismo_prefijo_multilocal);

        $ultimoIdFacturaVenta++;

        switch ($pedidoVenta->getDocumento()) {
            case 'factura':
                $voucher = ($pedidoVenta->getCaja()->getLocal()->getSerieFactura()) ? $pedidoVenta->getCaja()->getLocal()->getSerieFactura().'-'.$ultimoIdFacturaVenta : $ultimoIdFacturaVenta;
                break;
            case 'boleta':
                $voucher = ($pedidoVenta->getCaja()->getLocal()->getSerieBoleta()) ? $pedidoVenta->getCaja()->getLocal()->getSerieBoleta().'-'.$ultimoIdFacturaVenta : $ultimoIdFacturaVenta;
                break;
            case 'guia':
                $voucher = ($pedidoVenta->getCaja()->getLocal()->getPrefijoVoucher()) ? $pedidoVenta->getCaja()->getLocal()->getPrefijoVoucher().'-'.$ultimoIdFacturaVenta : $ultimoIdFacturaVenta;
                break;                            
            default:
                $voucher = ($pedidoVenta->getCaja()->getLocal()->getPrefijoVoucher()) ? $pedidoVenta->getCaja()->getLocal()->getPrefijoVoucher().'-'.$ultimoIdFacturaVenta : $ultimoIdFacturaVenta;
                break;
        }

        $facturaVenta->setCliente($pedidoVenta->getCliente());
        $facturaVenta->setVenta($venta);
        $facturaVenta->setFecha($fecha);
        $facturaVenta->setDocumento($pedidoVenta->getDocumento());
        $facturaVenta->setObservacion($pedidoVenta->getObservacion());

        if($pedidoVenta->getSinIgv())
        {
            $facturaVenta->setIncluirIgv(false);
        }
        else
        {
            $facturaVenta->setIncluirIgv(true);
        }
        
        $facturaVenta->setTicket($voucher);              


        $facturaVenta->setNumeroGuiaremision($pedidoVenta->getNumeroGuiaRemision());
        $facturaVenta->setLocal($pedidoVenta->getCaja()->getLocal());
        $facturaVenta->setCaja($pedidoVenta->getCaja());
        //$facturaVenta->setFacturaEnviada($enviarFactura);
        $facturaVenta->setDetraccion(false);
        $facturaVenta->setEnviadoSunat(false);
        $facturaVenta->setEmisionElectronica(true);
        $facturaVenta->setTipoVenta($pedidoVenta->getTipoVenta());
        $facturaVenta->setOrdenCompra($pedidoVenta->getOrdenCompra());
        $facturaVenta->setFechaVencimiento($fecha_vencimiento);        

        $em->persist($facturaVenta);



        //Guardamos el detalle de la venta en la tabla detalle_venta
        $productos_array = array();
        $total = 0;
        $j=0;
        $total_gravada      = 0;
        $total_igv          = 0;
        $total              = 0;
        $total_gratuita     = 0;
        $total_descuento    = 0;
        $total_anticipo     = 0;
        $total_inafecta     = 0;
        $total_exonerada    = 0;            
        $total_otros_cargos = 0;

        foreach($pedidoVenta->getPedidoVentaDetalle() as $detalle)
        {
            if($detalle->getProductoXLocal())
            {
                //Guardamos el detalle de Venta
                $detalleVenta = new DetalleVenta();
                $detalleVenta->setVenta($venta);
                $detalleVenta->setProductoXLocal($detalle->getProductoXLocal());
                $detalleVenta->setCantidad($detalle->getCantidadPedida());
                $detalleVenta->setPrecio($detalle->getPrecio());
                $detalleVenta->setSubtotal($detalle->getSubtotal());
                //$detalleVenta->setDescripcion($descripcion);
                $detalleVenta->setTipoImpuesto($detalle->getTipoImpuesto());
                $em->persist($detalleVenta);

                //Armamos el array q se va enviar a nubefact
                $tipo           = $detalle->getProductoXLocal()->getProducto()->getTipo();
                $codigo         = $detalle->getProductoXLocal()->getProducto()->getCodigo();
                $descripcion    = $detalle->getProductoXLocal()->getProducto()->getNombre();
                $cantidad       = $detalle->getCantidadPedida();
                $precio         = $detalle->getPrecio();
                $subtotal       = $detalle->getSubtotal();
                $tipoImpuesto   = $detalle->getTipoImpuesto()->getId();
                $impuestoValor  = $detalle->getTipoImpuesto()->getValor();

                $productos_array[$j]['id'] = $detalle->getProductoXLocal()->getId();
                $productos_array[$j]['tipo'] = $tipo;
                $productos_array[$j]['codigo'] = $codigo;
                $productos_array[$j]['descripcion'] = $descripcion;
                $productos_array[$j]['cantidad'] = $cantidad;
                $productos_array[$j]['precio'] = $precio;
                $productos_array[$j]['subtotal'] = $subtotal;
                $productos_array[$j]['tipoImpuesto'] = $tipoImpuesto;
                $productos_array[$j]['impuestoValor'] = $impuestoValor;

                if($pedidoVenta->getSinIgv())
                {
                    $valor_unitario     = ($cantidad > 0) ? $precio : 0;
                }
                else
                {
                    $valor_unitario     = ($cantidad > 0) ? ($subtotal/$cantidad)/(1 + $impuestoValor) : 0;
                }

                $precio_unitario    = ($cantidad > 0) ? $valor_unitario * (1 + $impuestoValor) : 0;
                $igv                = $cantidad * $valor_unitario * $impuestoValor;

                switch ($tipoImpuesto) 
                {
                    case 1:
                        $total_igv       = $total_igv + $igv;
                        $total_gravada   = $total_gravada + $valor_unitario * $cantidad;
                        $total           = ($pedidoVenta->getSinIgv()) ? $total + $subtotal * (1 + $impuestoValor) : $total + $subtotal;                          
                        break;
                    case 2:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 3:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 4:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 5:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 6:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 7:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 8:
                        $total_exonerada = $total_exonerada + $precio * $cantidad;
                        break;
                    case 9:
                        $total_inafecta  = $total_inafecta + $precio * $cantidad;
                        break;
                    case 10:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 11:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 12:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 13:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 14:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 15:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 16:
                        //$total           = $total + $precio * $cantidad;
                        $total_inafecta  = $total_inafecta + $precio * $cantidad;
                        break;                                                                                                                                                                     
                    default:
                        $total_igv       = $total_igv + $igv;
                        $total_gravada   = $total_gravada + $valor_unitario * $cantidad;
                        $total           = $total + $subtotal;                               
                        break;
                }




                $j++;                        

            }
         
        }

        $total_onerosa  = $total + $total_exonerada + $total_inafecta;

        //Guardamos en la tabla venta_forma_pago
        $ventaformaPago = new VentaFormaPago();        
        $ventaformaPago->setFormaPago($pedidoVenta->getFormaPago());
        $ventaformaPago->setVenta($venta);
        $ventaformaPago->setCantidad($total_onerosa);
        $ventaformaPago->setMoneda($pedidoVenta->getMoneda());
        $ventaformaPago->setMontoACuenta($pedidoVenta->getMontoACuenta());
        $ventaformaPago->setNumeroDias($pedidoVenta->getDiasCredito());

        $valor_tipo_cambio = ($pedidoVenta->getValorTipoCambio()) ? $pedidoVenta->getValorTipoCambio() : null;

        $ventaformaPago->setValorTipoCambio($valor_tipo_cambio);
        $ventaformaPago->setTotalOnerosa($total_onerosa);
        $ventaformaPago->setTotalGratuita($total_gratuita);

        $em->persist($ventaformaPago);

        $pedidoVenta->setFacturaVenta($facturaVenta);
        $pedidoVenta->setTotalOnerosa($total_onerosa);
        $pedidoVenta->setTotalGratuita($total_gratuita);
        $em->persist($pedidoVenta);



        try {

            $em->flush();

            return $facturaVenta;   

        } catch (\Exception $e) {

            return false;
                     
        }         


    }

    private function editarFacturaVenta($request,$pedidoVenta)
    {

        $em = $this->getDoctrine()->getManager();
        $session        = $request->getSession();
        $caja           = $session->get('caja');
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');


        $fecha = $pedidoVenta->getFecha();
        $fecha_vencimiento = $pedidoVenta->getFechaVencimiento();
        $empleado = $em->getRepository('AppBundle:Empleado')->findOneBy(array('usuario'=>$session->get('usuario')));

        $facturaVenta = $pedidoVenta->getFacturaVenta();

        $voucher = $facturaVenta->getTicket();

        if($facturaVenta->getDocumento() != $pedidoVenta->getDocumento())
        {

            $empresa_mismo_prefijo_multilocal = ($pedidoVenta->getLocal()->getEmpresa()->getPermitirMismoPrefijoMultilocal()) ? $empresa : '';

            $ultimoIdFacturaVenta = $this->container->get('AppBundle\Util\Util')->generarNumeroDocumento('factura_venta',$pedidoVenta->getCaja()->getLocal()->getId(),$pedidoVenta->getDocumento(),$empresa_mismo_prefijo_multilocal);

            $ultimoIdFacturaVenta++;

            switch ($pedidoVenta->getDocumento()) {
                case 'factura':
                    $voucher = ($pedidoVenta->getCaja()->getLocal()->getSerieFactura()) ? $pedidoVenta->getCaja()->getLocal()->getSerieFactura().'-'.$ultimoIdFacturaVenta : $ultimoIdFacturaVenta;
                    break;
                case 'boleta':
                    $voucher = ($pedidoVenta->getCaja()->getLocal()->getSerieBoleta()) ? $pedidoVenta->getCaja()->getLocal()->getSerieBoleta().'-'.$ultimoIdFacturaVenta : $ultimoIdFacturaVenta;
                    break;
                case 'guia':
                    $voucher = ($pedidoVenta->getCaja()->getLocal()->getPrefijoVoucher()) ? $pedidoVenta->getCaja()->getLocal()->getPrefijoVoucher().'-'.$ultimoIdFacturaVenta : $ultimoIdFacturaVenta;
                    break;                            
                default:
                    $voucher = ($pedidoVenta->getCaja()->getLocal()->getPrefijoVoucher()) ? $pedidoVenta->getCaja()->getLocal()->getPrefijoVoucher().'-'.$ultimoIdFacturaVenta : $ultimoIdFacturaVenta;
                    break;
            }


        }

        $facturaVenta->setCliente($pedidoVenta->getCliente());
        $facturaVenta->setFecha($fecha);
        $facturaVenta->setDocumento($pedidoVenta->getDocumento());
        $facturaVenta->setTicket($voucher);

        $facturaVenta->setNumeroGuiaremision($pedidoVenta->getNumeroGuiaRemision());
        $facturaVenta->setLocal($pedidoVenta->getCaja()->getLocal());
        $facturaVenta->setCaja($pedidoVenta->getCaja());
        $facturaVenta->setDetraccion(false);
        $facturaVenta->setEnviadoSunat(false);
        $facturaVenta->setEmisionElectronica(true);
        $facturaVenta->setTipoVenta($pedidoVenta->getTipoVenta());
        $facturaVenta->setOrdenCompra($pedidoVenta->getOrdenCompra());
        $facturaVenta->setFechaVencimiento($fecha_vencimiento);
        $facturaVenta->setObservacion($pedidoVenta->getObservacion());
        
        foreach($facturaVenta->getVenta()->getDetalleVenta() as $detalleVenta)
        {
            $em->remove($detalleVenta);
            $em->flush();           
        }


        //Guardamos el detalle de la venta en la tabla detalle_venta
        $productos_array = array();
        $total = 0;
        $j=0;
        $total_gravada      = 0;
        $total_igv          = 0;
        $total              = 0;
        $total_gratuita     = 0;
        $total_descuento    = 0;
        $total_anticipo     = 0;
        $total_inafecta     = 0;
        $total_exonerada    = 0;            
        $total_otros_cargos = 0;

        foreach($pedidoVenta->getPedidoVentaDetalle() as $detalle)
        {
            if($detalle->getProductoXLocal())
            {
                //Guardamos el detalle de Venta
                $detalleVenta = new DetalleVenta();
                $detalleVenta->setVenta($facturaVenta->getVenta());
                $detalleVenta->setProductoXLocal($detalle->getProductoXLocal());
                $detalleVenta->setCantidad($detalle->getCantidadPedida());
                $detalleVenta->setPrecio($detalle->getPrecio());
                $detalleVenta->setSubtotal($detalle->getSubtotal());
                //$detalleVenta->setDescripcion($descripcion);
                $detalleVenta->setTipoImpuesto($detalle->getTipoImpuesto());
                $em->persist($detalleVenta);

                //Armamos el array q se va enviar a nubefact
                $tipo           = $detalle->getProductoXLocal()->getProducto()->getTipo();
                $codigo         = $detalle->getProductoXLocal()->getProducto()->getCodigo();
                $descripcion    = $detalle->getProductoXLocal()->getProducto()->getNombre();
                $cantidad       = $detalle->getCantidadPedida();
                $precio         = $detalle->getPrecio();
                $subtotal       = $detalle->getSubtotal();
                $tipoImpuesto   = $detalle->getTipoImpuesto()->getId();
                $impuestoValor  = $detalle->getTipoImpuesto()->getValor();

                $productos_array[$j]['id'] = $detalle->getProductoXLocal()->getId();
                $productos_array[$j]['tipo'] = $tipo;
                $productos_array[$j]['codigo'] = $codigo;
                $productos_array[$j]['descripcion'] = $descripcion;
                $productos_array[$j]['cantidad'] = $cantidad;
                $productos_array[$j]['precio'] = $precio;
                $productos_array[$j]['subtotal'] = $subtotal;
                $productos_array[$j]['tipoImpuesto'] = $tipoImpuesto;
                $productos_array[$j]['impuestoValor'] = $impuestoValor;


                if($pedidoVenta->getSinIgv())
                {
                    $valor_unitario     = ($cantidad > 0) ? $precio : 0;
                }
                else
                {
                    $valor_unitario     = ($cantidad > 0) ? ($subtotal/$cantidad)/(1 + $impuestoValor) : 0;
                }

                $precio_unitario    = ($cantidad > 0) ? $valor_unitario * (1 + $impuestoValor) : 0;
                $igv                = $cantidad * $valor_unitario * $impuestoValor;

                switch ($tipoImpuesto) 
                {
                    case 1:
                        $total_igv       = $total_igv + $igv;
                        $total_gravada   = $total_gravada + $valor_unitario * $cantidad;
                        $total           = ($pedidoVenta->getSinIgv()) ? $total + $subtotal * (1 + $impuestoValor) : $total + $subtotal;                          
                        break;
                    case 2:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 3:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 4:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 5:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 6:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 7:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;    
                        break;
                    case 8:
                        $total_exonerada = $total_exonerada + $precio * $cantidad;
                        break;
                    case 9:
                        $total_inafecta  = $total_inafecta + $precio * $cantidad;
                        break;
                    case 10:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 11:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 12:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 13:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 14:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 15:
                        $total_gratuita  = $total_gratuita + $precio * $cantidad;
                        break;
                    case 16:
                        $total           = $total + $precio * $cantidad;
                        $total_inafecta  = $total_inafecta + $precio * $cantidad;
                        break;                                                                                                                                                                     
                    default:
                        $total_igv       = $total_igv + $igv;
                        $total_gravada   = $total_gravada + $valor_unitario * $cantidad;
                        $total           = $total + $subtotal;                               
                        break;
                }

                $j++;                        

            }
         
        }

        $total_onerosa  = $total + $total_exonerada + $total_inafecta;

        //Guardamos en la tabla venta_forma_pago
        $facturaVenta->getVenta()->getVentaFormaPago()[0]->setFormaPago($pedidoVenta->getFormaPago());
        $facturaVenta->getVenta()->getVentaFormaPago()[0]->setCantidad($total_onerosa);
        $facturaVenta->getVenta()->getVentaFormaPago()[0]->setMoneda($pedidoVenta->getMoneda());
        $facturaVenta->getVenta()->getVentaFormaPago()[0]->setMontoACuenta($pedidoVenta->getMontoACuenta());
        $facturaVenta->getVenta()->getVentaFormaPago()[0]->setNumeroDias($pedidoVenta->getDiasCredito());

        $valor_tipo_cambio = ($pedidoVenta->getValorTipoCambio()) ? $pedidoVenta->getValorTipoCambio() : null;
        $facturaVenta->getVenta()->getVentaFormaPago()[0]->setValorTipoCambio($valor_tipo_cambio);

        $facturaVenta->getVenta()->getVentaFormaPago()[0]->setTotalOnerosa($total_onerosa);
        $facturaVenta->getVenta()->getVentaFormaPago()[0]->setTotalGratuita($total_gratuita);

        $em->persist($facturaVenta);

        $pedidoVenta->setTotalOnerosa($total_onerosa);
        $pedidoVenta->setTotalGratuita($total_gratuita);

        $em->persist($pedidoVenta);


        try {

            $em->flush();

            return $facturaVenta;   

        } catch (\Exception $e) {

            return false;
                     
        }         


    }

    /**
     * Genera documento PDF de venta cuando es facturacion electronnica, factura,boleta.
     *
     * @Route("/{id}/generar/comprobante/pdf", name="facturaventa_generar_comprobante_pdf")
     * @Method({"GET", "POST"})
     */
    public function generarComprobantePdfAction(Request $request, FacturaVenta $facturaVenta)
    {

        $em = $this->getDoctrine()->getManager();
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');

        
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');
        $host           = $request->getHost();

        $localObj  = $em->getRepository('AppBundle:EmpresaLocal')->find($local);
        


        if($facturaVenta->getDocumento() == 'boleta')
        {

            $f = $this->generateUniqueFileName().'.pdf';
            $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;

            switch ($localObj->getBoletaFormato()) {
                case 'A4':

                    $html = $this->render('facturaventa/boletaElectronicaA4.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 10,'margin-top' => 5,'margin-bottom' => 3));

                break;
                case 'TICKET':

                    $html = $this->render('facturaventa/boletaElectronicaTicket.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' =>  200,'page-width' => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0));

                break;                
            }


            $facturaVenta->setEnlacePdfFerretero($ruta_pdf);

            $em->persist($facturaVenta);

            try {

                $em->flush();
                
            } catch (\Exception $e) {

                return $e;
                
            }

            return $this->redirectToRoute('facturaventa_index');

        }
        elseif($facturaVenta->getDocumento() == 'factura')
        {


            $f = $this->generateUniqueFileName().'.pdf';
            $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;    

            switch ($localObj->getFacturaFormato()) {
                case 'A4':

                    $html = $this->render('facturaventa/facturaElectronicaA4.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 10,'margin-top' => 5,'margin-bottom' => 3));

                break;
                case 'TICKET':

                    $html = $this->render('facturaventa/facturaElectronicaTicket.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $localObj,
                            'host' => $host
                        ))->getContent();


                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' =>  200,'page-width' => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                break;                
            }


            $facturaVenta->setEnlacePdfFerretero($ruta_pdf);

            $em->persist($facturaVenta);

            try {

                $em->flush();
                
            } catch (\Exception $e) {

                return $e;
                
            }

            return $this->redirectToRoute('facturaventa_index');


        }
        else
        {
            $f = $this->generateUniqueFileName().'.pdf';
            $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;

            switch ($facturaVenta->getLocal()->getNotaventaFormato()) {
                case 'A4':


                    $html = $this->render('facturaventa/showGuiaA4.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $facturaVenta->getLocal(),
                            'host' => $host
                        ))->getContent();
                    

                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 10,'margin-top' => 5,'margin-bottom' => 0));


                    break;
                
                default:

                    $html = $this->render('detalleventa/ticket.html.twig', array(
                            'facturaVenta' => $facturaVenta,
                            'localObj'     => $facturaVenta->getLocal(),
                            'host' => $host
                        ))->getContent();



                    $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' => 210,'page-width'  => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                    break;

            }


            $facturaVenta->setEnlacePdfFerretero($ruta_pdf);

            $em->persist($facturaVenta);

            try {

                $em->flush();
                
            } catch (\Exception $e) {

                return $e;
                
            }

            return $this->redirectToRoute('facturaventa_index');



        }


        return new \Symfony\Component\HttpFoundation\Response(
                $pdf, 200, array(
                    'Content-Type'          => 'application/pdf',
                    'Content-Disposition'   => 'inline; filename="'.$f.'"',
                     
        ));

    }


    /**
     * Actualizamos PDF de los comprobantes
     *
     * @Route("/actualizar/comprobante/pdf", name="facturaventa_actualizar_comprobante_pdf")
     * @Method({"GET", "POST"})
     */
    public function actualizarComprobantePdfAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        setlocale(LC_ALL, "es_ES", 'Spanish_Spain', 'Spanish');

        
        $session        = $request->getSession();
        //$local          = $session->get('local');
        //$empresa        = $session->get('empresa');
        $host           = $request->getHost();

        $dql = "SELECT fv FROM AppBundle:FacturaVenta fv ";
        $dql .= " JOIN fv.local l";
        $dql .= " JOIN l.empresa e";
        $dql .= " WHERE e.id = 7  AND fv.tipo = 2 AND (fv.documento = 'factura' OR fv.documento = 'boleta' ) ";

        $query = $em->createQuery($dql);

        $facturasVenta =  $query->getResult();

        foreach($facturasVenta as $facturaVenta)
        {
            $localObj = $facturaVenta->getLocal();
            $empresa  = $facturaVenta->getLocal()->getEmpresa()->getId();
            $local    = $facturaVenta->getLocal()->getId();

            if($facturaVenta->getDocumento() == 'boleta')
            {

                $f = $this->generateUniqueFileName().'.pdf';
                $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;

                switch ($localObj->getBoletaFormato()) {
                    case 'A4':

                        $html = $this->render('facturaventa/boletaElectronicaA4.html.twig', array(
                                'facturaVenta' => $facturaVenta,
                                'localObj'     => $localObj,
                                'host' => $host
                            ))->getContent();


                        $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 10,'margin-top' => 5,'margin-bottom' => 3));

                    break;
                    case 'TICKET':

                        $html = $this->render('facturaventa/boletaElectronicaTicket.html.twig', array(
                                'facturaVenta' => $facturaVenta,
                                'localObj'     => $localObj,
                                'host' => $host
                            ))->getContent();


                        $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' =>  200,'page-width' => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0));

                    break;                
                }


                $facturaVenta->setEnlacePdfFerretero($ruta_pdf);

                $em->persist($facturaVenta);

                try {

                    $em->flush();
                    
                } catch (\Exception $e) {

                    return $e;
                    
                }

                //return $this->redirectToRoute('facturaventa_index');

            }
            elseif($facturaVenta->getDocumento() == 'factura')
            {


                $f = $this->generateUniqueFileName().'.pdf';
                $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;    

                switch ($localObj->getFacturaFormato()) {
                    case 'A4':

                        $html = $this->render('facturaventa/facturaElectronicaA4.html.twig', array(
                                'facturaVenta' => $facturaVenta,
                                'localObj'     => $localObj,
                                'host' => $host
                            ))->getContent();


                        $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 10,'margin-top' => 5,'margin-bottom' => 3));

                    break;
                    case 'TICKET':

                        $html = $this->render('facturaventa/facturaElectronicaTicket.html.twig', array(
                                'facturaVenta' => $facturaVenta,
                                'localObj'     => $localObj,
                                'host' => $host
                            ))->getContent();


                        $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' =>  200,'page-width' => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                    break;                
                }


                $facturaVenta->setEnlacePdfFerretero($ruta_pdf);

                $em->persist($facturaVenta);

                try {

                    $em->flush();
                    
                } catch (\Exception $e) {

                    return $e;
                    
                }

                //return $this->redirectToRoute('facturaventa_index');


            }
            else
            {
                $f = $this->generateUniqueFileName().'.pdf';
                $ruta_pdf = $request->getSchemeAndHttpHost().'/uploads/files/'.$empresa.'/'.$f;

                switch ($facturaVenta->getLocal()->getNotaventaFormato()) {
                    case 'A4':


                        $html = $this->render('facturaventa/showGuiaA4.html.twig', array(
                                'facturaVenta' => $facturaVenta,
                                'localObj'     => $facturaVenta->getLocal(),
                                'host' => $host
                            ))->getContent();
                        

                        $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-size'=> "A4",'margin-right' => 0,'margin-left' => 10,'margin-top' => 5,'margin-bottom' => 0));


                        break;
                    
                    default:

                        $html = $this->render('detalleventa/ticket.html.twig', array(
                                'facturaVenta' => $facturaVenta,
                                'localObj'     => $facturaVenta->getLocal(),
                                'host' => $host
                            ))->getContent();



                        $this->get('knp_snappy.pdf')->generateFromHtml($html, 'uploads/files/'.$empresa.'/'.$f,array('header-html'=> null,'footer-html'=> null,'page-height' => 210,'page-width'  => 80,'margin-right' => 0,'margin-left' => 0,'margin-top' => 0,'margin-bottom' => 0));

                        break;

                }


                $facturaVenta->setEnlacePdfFerretero($ruta_pdf);

                $em->persist($facturaVenta);

                try {

                    $em->flush();
                    
                } catch (\Exception $e) {

                    return $e;
                    
                }

                //return $this->redirectToRoute('facturaventa_index');


            }

        }


        //$localObj  = $em->getRepository('AppBundle:EmpresaLocal')->find($local);

        return new Response('Se actualizaron los PDF');

        



        // return new \Symfony\Component\HttpFoundation\Response(
        //         $pdf, 200, array(
        //             'Content-Type'          => 'application/pdf',
        //             'Content-Disposition'   => 'inline; filename="'.$f.'"',
                     
        // ));

    }



}
