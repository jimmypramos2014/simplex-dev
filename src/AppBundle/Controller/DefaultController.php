<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\TipoCambio;
use AppBundle\Entity\CajaApertura;
use AppBundle\NumeroALetras\NumeroALetras;

class DefaultController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @Security("has_role('ROLE_ADMIN')")
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');
        $local_param    = '';

        // $numeroALetras = new NumeroALetras();

        // dump($numeroALetras->convertir(234));
        // die();
        // if(!$session->get('caja')){
        //     return $this->redirectToRoute('seleccionar_local');
        // }
        
        //Filtro local
        $options = array('empresa'=>$empresa);
        $form = $this->createFormBuilder($options)
            ->add('local', EntityType::class,array(
                'class'     => 'AppBundle:EmpresaLocal',
                'attr'      => array('class' => 'form-control mb-2 mr-sm-2'),
                'label'     => 'Local',
                'label_attr'=> array('class'=>'sr-only'),
                'required'  => false,
                'placeholder'  => 'Todos los locales',
                'query_builder' => function(EntityRepository $er) use ($options)
                {
                    $qb = $er->createQueryBuilder('el');
                    $qb->leftJoin('el.empresa','e');
                    $qb->where('el.estado = 1');

                    if($options['empresa'] != ''){
                        $qb->andWhere('e.id ='.$options['empresa']);
                    }
                    
                    return $qb;
                }                   
                ))
            ->getForm();

        //var_dump($request->request->get('form')['local']);
        //die();

        if(null !== $request->request->get('form')['local'] &&  $request->request->get('form')['local'] != ''){
            $local_param    = $request->request->get('form')['local'];
        }

        $ventas_x_dia       = array();
        $compras_x_dia      = array();
        $gastos_x_dia       = array();
        $ganancias_x_dia    = array();
        $dias               = array();

        for($i = 30;$i>=0;$i--){
            $ventas_x_dia[]  = ($this->container->get('AppBundle\Util\Util')->obtenerVentaXDia($empresa,$i,$local_param))?(float)$this->container->get('AppBundle\Util\Util')->obtenerVentaXDia($empresa,$i,$local_param):0;
            $compras_x_dia[] = ($this->container->get('AppBundle\Util\Util')->obtenerCompraXDia($empresa,$i,$local_param))?(float)$this->container->get('AppBundle\Util\Util')->obtenerCompraXDia($empresa,$i,$local_param):0;
            $gastos_x_dia[]  = ($this->container->get('AppBundle\Util\Util')->obtenerGastoXDia($empresa,$i,$local_param))?(float)$this->container->get('AppBundle\Util\Util')->obtenerGastoXDia($empresa,$i,$local_param):0;

            $ganancias_x_dia[] = end($ventas_x_dia) - (end($compras_x_dia) + end($gastos_x_dia));

            $dias[] = date('d/m/Y', strtotime('today - '.$i.' days'));
        }

        //dump($ventas_x_dia);
        //die();

        $ventas_x_mes       = array();
        $compras_x_mes      = array();
        $gastos_x_mes       = array();
        $ganancias_x_mes    = array();
        $meses              = array();

        for($j = 12;$j>=0;$j--){
            $ventas_x_mes[]  = ($this->container->get('AppBundle\Util\Util')->obtenerVentaXMes($empresa,$j,$local_param))?(float)$this->container->get('AppBundle\Util\Util')->obtenerVentaXMes($empresa,$j,$local_param):0;
            $compras_x_mes[] = ($this->container->get('AppBundle\Util\Util')->obtenerCompraXMes($empresa,$j,$local_param))?(float)$this->container->get('AppBundle\Util\Util')->obtenerCompraXMes($empresa,$j,$local_param):0;
            $gastos_x_mes[]  = ($this->container->get('AppBundle\Util\Util')->obtenerGastoXMes($empresa,$j,$local_param))?(float)$this->container->get('AppBundle\Util\Util')->obtenerGastoXMes($empresa,$j,$local_param):0;

            $ganancias_x_mes[] = end($ventas_x_mes) - (end($compras_x_mes) + end($gastos_x_mes));

            //$meses[] = date('m/Y', strtotime('today - '.$j.' month'));
            $meses[] = date("m/Y", strtotime( date( 'Y-m-01' )." -$j months"));
        }



        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'titulo' => 'Panel',
            'ventas_x_dia' => implode(",", $ventas_x_dia),
            'compras_x_dia' => implode(",", $compras_x_dia),
            'gastos_x_dia' => implode(",", $gastos_x_dia),
            'ganancias_x_dia' => implode(",", $ganancias_x_dia),
            'dias' => $dias,
            'ventas_x_mes' => implode(",", $ventas_x_mes),
            'compras_x_mes' => implode(",", $compras_x_mes),
            'gastos_x_mes' => implode(",", $gastos_x_mes),
            'ganancias_x_mes' => implode(",", $ganancias_x_mes),
            'meses' => $meses,            
            'empresa' => $empresa,
            'form'   => $form->createView(),
            'local_param' => $local_param

        ]);


    }

    /**
     * @Route("/seleccionar/local", name="seleccionar_local")
     * @Security("has_role('ROLE_VENDEDOR') or has_role('ROLE_ALMACENERO') or has_role('ROLE_ADMIN') ")
     */
    public function seleccionarLocalAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');
        $caja_apertura  = $session->get('caja_apertura');

        $localObj       = $em->getRepository('AppBundle:EmpresaLocal')->find($local);

        $form = $this->createForm('AppBundle\Form\SeleccionarLocalType', null,array('empresa'=>$empresa,'local'=>$local));

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $formLocal = $form->getData()['local'];
            $formCaja  = $form->getData()['caja'];

            
            $session->set('local',$formLocal->getId());
            $session->set('caja',$formCaja->getId());


            $this->addFlash("success", "El local y caja fue seleccionado exitosamente.");


            switch ($session->get('rol')) {
                case 'Administrador':

                    $estado_caja = $this->container->get('AppBundle\Util\Util')->verificaEstadoCaja($session->get('caja'));
                    if(!$estado_caja){
                        return $this->redirectToRoute('aperturar_caja');
                    }
                    return $this->redirectToRoute('detalleventa_puntoventa');
                    break;
                case 'Almacenero':
                    // $estado_caja = $this->container->get('AppBundle\Util\Util')->verificaEstadoCaja($session->get('caja'));
                    // if(!$estado_caja){
                    //     return $this->redirectToRoute('aperturar_caja');
                    // }                
                    return $this->redirectToRoute('productoxlocal_index');
                    break;
                case 'Vendedor':
                    // $estado_caja = $this->container->get('AppBundle\Util\Util')->verificaEstadoCaja($session->get('caja'));
                    // if(!$estado_caja){
                    //     return $this->redirectToRoute('aperturar_caja');
                    // }                
                    return $this->redirectToRoute('almacen_productosxlocal');
                    break;                                            
                default:
                    // $estado_caja = $this->container->get('AppBundle\Util\Util')->verificaEstadoCaja($session->get('caja'));
                    // if(!$estado_caja){
                    //     return $this->redirectToRoute('aperturar_caja');
                    // }                
                    return $this->redirectToRoute('almacen_productosxlocal');
                    break;
            }            

        }

        return $this->render('default/seleccionarlocal.html.twig', [
            'titulo' => '',
            'form'   => $form->createView(),
        ]);


    }

    /**
     * @Route("/aperturar/caja", name="aperturar_caja")
     * @Security("has_role('ROLE_VENDEDOR') or has_role('ROLE_ALMACENERO') ")
     */
    public function aperturarCajaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');
        $caja           = $session->get('caja');

        $localObj       = $em->getRepository('AppBundle:EmpresaLocal')->find($local);
        $cajaObj        = $em->getRepository('AppBundle:Caja')->find($caja);

        $fecha = new \DateTime();

        $montoAnterior = ($cajaObj->getMontoAnterior()) ? $cajaObj->getMontoAnterior() : 0;

        $cajaApertura = new CajaApertura();
        $form = $this->createForm('AppBundle\Form\CajaAperturaType',$cajaApertura,array('caja'=>$caja));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cajaApertura->setFecha($fecha);
            $cajaApertura->getCaja()->setCondicion('abierto');
            $em->persist($cajaApertura);

            try {

                $em->flush();

                $session->set('caja_apertura',$cajaApertura->getId());

                $this->addFlash("success", "La caja fue aperturada exitosamente.");
                
            } catch (\Exception $e) {

                $this->addFlash("danger", $e." Ocurrió un error inesperado, la apertura no se realizó.");  
                
            }


            switch ($session->get('rol')) {
                case 'Administrador':
                    return $this->redirectToRoute('detalleventa_puntoventa');
                    break;
                case 'Almacenero':
                    return $this->redirectToRoute('productoxlocal_index');
                    break;
                case 'Vendedor':
                    return $this->redirectToRoute('almacen_productosxlocal');
                    break;                                            
                default:
                    return $this->redirectToRoute('almacen_productosxlocal');
                    break;
            } 

                      

        }

        return $this->render('default/aperturarcaja.html.twig', [
            'caja' => $cajaObj,
            'form' => $form->createView(),
            'montoAnterior' => $montoAnterior,
            'titulo'    => ''
        ]);


    }


    public function imprimirTicketAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $factura_id = $request->query->get('factura_id');

        $facturaVenta  = $em->getRepository('AppBundle:FacturaVenta')->find($factura_id);

        $i = 0;
        foreach($facturaVenta->getVenta()->getDetalleVenta() as $detalleVenta){

            if($i == 0){
                $localObj = $detalleVenta->getProductoXLocal()->getLocal();
                break;
            }
            

            $i++;
        }

        $ticket_html = '';

        $ticket_html .= '<style type="text/css">';
     
        $ticket_html .= '
            body, td, th {
                font-size: 12px;
                font-family: Calibri;
            }  
            table {
                display: table;
                border-collapse: separate;
                border-spacing: 1px;
                border-color: grey;                
            }

            .col-4 {
                -webkit-box-flex: 0;
                -ms-flex: 0 0 15%;
                flex: 0 0 15%;
                max-width: 15%;
            }

            .table {
                width: 100%;
                max-width: 100%;
                margin-bottom: 1rem;
                background-color: transparent;
            }

            .h3, h3 {
                font-size: 1.75rem;
                font-family: inherit;
                font-weight: 500;
                line-height: 1.2;
                color: inherit;                
            }

            .h6, h6 {
                font-size: 1rem;
                font-family: inherit;
                font-weight: 500;
                line-height: 1.2;
                color: inherit;                    
            }            

            .text-center {
                text-align: center !important;
            }';            
        $ticket_html .= '</style>';

        $ticket_html .= '<div class="col-4 ">';
        $ticket_html .= '    <table class="table" border="0" width="40">';
        $ticket_html .= '        <tr class="h3 text-center" align="center"><td colspan="4" ><h3>'.$localObj->getNombre().'</h3></td></tr>';
        $ticket_html .= '        <tr height="10" class="text-center" align="center"><td colspan="4">'.$localObj->getDireccion().'</td></tr>';
        $ticket_html .= '        <tr height="10" class="text-center"><td colspan="4"><center>Tel: '.$localObj->getTelefono().'</center></td></tr>';
        $ticket_html .= '        <tr><td colspan="4"><center>RUC : '.$localObj->getEmpresa()->getRuc().'</center></td></tr>';

        $ticket_html .= '        <tr height="25"><th>TICKET:</th><td>'.$facturaVenta->getTicket().'</td><th>  FECHA:</th><td>'.$facturaVenta->getFecha()->format('d/m/Y H:i:s').'</td></tr>';
        $ticket_html .= '        <tr height="25"><td>CLIENTE:</td><td colspan="3">'.$facturaVenta->getClienteNombre().'</td></tr>';
        $ticket_html .= '        <tr height="25"><td>CAJERO:</td><td colspan="3">'.$facturaVenta->getVenta()->getEmpleado().'</td></tr>';
        $ticket_html .= '        <tr>';
        $ticket_html .= '            <td colspan="4" >';
        $ticket_html .= '                =================================================';
        $ticket_html .= '                <table class="table table-striped">';
        $ticket_html .= '                    <thead>';
        $ticket_html .= '                        <tr height="25" class="text-center">';
        $ticket_html .= '                            <th>Producto</th>';
        $ticket_html .= '                            <th>Descripción</th>';
        $ticket_html .= '                            <th>Cantidad</th>';
        $ticket_html .= '                            <th>Importe</th>';
        $ticket_html .= '                        </tr>';
        $ticket_html .= '                    </thead>';
        $ticket_html .= '                    <tbody>';
                                $total = 0;
                                $impuesto = 0;
                                foreach($facturaVenta->getVenta()->getDetalleVenta() as $detalle){

        $ticket_html .= '                            <tr class="text-center">';
        $ticket_html .= '                                <td>'.$detalle->getProductoXLocal()->getProducto()->getNombre().'</td>';
                                                         $descripcion = '';
                                                         if ($detalle->getDescripcion()){
                                                            $descripcion = $detalle->getDescripcion();
                                                         }
                                                        
        $ticket_html .= '                                <td>'.$descripcion.'</td>';
        $ticket_html .= '                                <td>'.$detalle->getCantidad().'</td>';
        $ticket_html .= '                                <td>'.$detalle->getSubtotal().'</td>';
        $ticket_html .= '                            </tr>';   
                                    $total = $total + $detalle->getSubtotal();
                                    $impuesto = $impuesto +  0.18*$detalle->getSubtotal()/1.18;
                                }
        $ticket_html .= '                    </tbody>';
        $ticket_html .= '                    <tfoot>';

        // $ticket_html .= '                        <tr height="25">';
        // $ticket_html .= '                            <th></th>';
        // $ticket_html .= '                            <th></th>';
        // $ticket_html .= '                            <th>Sub Total</th>';
        // $ticket_html .= '                            <th>'.number_format($total).'</th>' ;                          
        // $ticket_html .= '                        </tr>';
        $ticket_html .= '                        <tr height="25">';
        $ticket_html .= '                            <th></th>';
        $ticket_html .= '                            <th></th>';
        $ticket_html .= '                            <th>IGV</th>';
        $ticket_html .= '                            <th>'.number_format($impuesto).'</th>' ;                           
        $ticket_html .= '                        </tr>' ;
        $ticket_html .= '                        <tr height="25">';
        $ticket_html .= '                            <th></th>';
        $ticket_html .= '                            <th ></th>';
        $ticket_html .= '                            <th>TOTAL</th>';
        $ticket_html .= '                            <th>'.number_format($total).'</th>';                           
        $ticket_html .= '                        </tr>';
        $ticket_html .= '                    </tfoot>';
                         
        $ticket_html .= '                </table>';
        $ticket_html .= '                =================================================';
        $ticket_html .= '            </td>';
        $ticket_html .= '        </tr>';

        $ticket_html .= '    </table>';
        // $ticket_html .= '</div>';

        foreach($facturaVenta->getVenta()->getVentaFormaPago() as $ventaFormaPago){

            if($ventaFormaPago->getFormaPago()->getId() == 5){

                $montoACuenta   = ($ventaFormaPago->getMontoACuenta())?$ventaFormaPago->getMontoACuenta():0;
                $pagoTotal      = ($ventaFormaPago->getCantidad())?$ventaFormaPago->getCantidad():0;;
                $saldo          = $pagoTotal - $montoACuenta;

                $ticket_html .= '    <p class="text-center ">';
                $ticket_html .= '        A CUENTA: '.$montoACuenta.' ---  SALDO: '.$saldo;
                $ticket_html .= '    </p>';

            }


        }



        // $ticket_html .= '<div class="col-4 ">';
            
        $ticket_html .= '    <p class="text-center ">';
        $ticket_html .= '        **GRACIAS POR SU COMPRA**';
        $ticket_html .= '    </p>';
        $ticket_html .= '    <p class="text-center ">';
        $ticket_html .= '        Este es un comprobante interno sin valor legal,por favor canjearlo por una boleta o factura';
        $ticket_html .= '    </p>';

        $ticket_html .= '</div>';

        $response = new Response(
            $ticket_html,
            Response::HTTP_OK,
            array('content-type' => 'text/html')
        );

        return $response;


    }


    public function establecerTipoDeCambioAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $empresas     = $em->getRepository('AppBundle:Empresa')->findBy(array('estado'=>true));

        $fecha = new \Datetime();

        $sUrl = "http://www.sunat.gob.pe/cl-at-ittipcam/tcS01Alias";
        $sContent = file_get_contents($sUrl);
        $doc = new \DOMDocument();

        // set error level
        $internalErrors = libxml_use_internal_errors(true);

        $doc->loadHTML($sContent);

        // Restore error level
        libxml_use_internal_errors($internalErrors);        


        $xpath = new \DOMXPath($doc);
        $tablaTC = $xpath->query("//table[@class='class=\"form-table\"']"); //obtenemos la tabla TC
        $filas = [];

        foreach($tablaTC as $fila){
            $filas = $fila->getElementsByTagName("tr"); //obtiene todas las tr de la tabla de TC
        }

        $tcs = array(); //array de tcs, por dia como clave

        foreach($filas as $fila){//recorremos cada tr
            $tds = [];
            $tds = $fila->getElementsByTagName("td");
            $i = 0;
            $j = 0;
            $arr = [];
            $dia = "";
            foreach($tds as $td){//recorremos cada td
                if($j == 3){
                    $j = 0;
                    $arr = [];
                }
                if($j == 0){
                    $dia = trim(preg_replace("/[\r\n]+/", " ", $td->nodeValue));
                    $tcs[$dia] = [];
                }
                if($j > 0 && $j < 3){
                    $tcs[$dia][] = trim(preg_replace("/[\r\n]+/", " ", $td->nodeValue));
                }
                $j++;
            }
        }

        foreach($empresas as $empresa){

            $tipoCambio = $em->getRepository('AppBundle:TipoCambio')->findOneBy(array('fecha'=>$fecha,'empresa'=>$empresa));

            if(!$tipoCambio){
                $tipoCambio = new TipoCambio();
            }


            $j = 1;
            foreach($tcs as $i=>$tc){

                if($j == count($tcs)){                

                    $tipoCambio->setEmpresa($empresa);
                    $tipoCambio->setFecha($fecha);
                    $tipoCambio->setCompra($tc[0]);
                    $tipoCambio->setVenta($tc[1]);
                    $em->persist($tipoCambio);

                    try {

                        $em->flush();

                        

                    } catch (\Exception $e) {

                        return $e;
                    }
                    

                }

                $j++;

            }

        }


        return new Response('Se actualizó el tipo de cambio exitosamente.');        


    }


}
