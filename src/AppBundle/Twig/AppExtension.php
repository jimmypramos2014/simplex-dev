<?php

namespace AppBundle\Twig;
use Doctrine\DBAL\Driver\Connection;
use AppBundle\Barcode\Barcode;

class AppExtension extends \Twig_Extension
{
    private $conn;
    private $em;
    private $barcode;

    public function __construct(\Doctrine\ORM\EntityManagerInterface  $em,Connection $connection,Barcode $barcode)
    {
        $this->em   = $em;
        $this->conn = $connection;
        $this->barcode = $barcode;
    }

    public function getFilters()
    {
        return array(
            'formatNumTelefono' => new \Twig_Filter_Method($this, 'formatNumTelefono'),
            'jsonDecode'        => new \Twig_Filter_Method($this, 'jsonDecode'),
            'obtenerSvg'        => new \Twig_Filter_Method($this, 'obtenerSvg'),
            'nombreMes' => new \Twig_Filter_Method($this, 'nombreMes'),
            'parteEntera' => new \Twig_Filter_Method($this, 'parteEntera'),
            'parteDecimal' => new \Twig_Filter_Method($this, 'parteDecimal'),
            'obtenerHash'  => new \Twig_Filter_Method($this, 'obtenerHash'),

        );
    }

    public function getFunctions()
    {
        return array(
            'tieneSubmenu'          => new \Twig_Function_Method($this, 'tieneSubmenu'),
            'obtenerCodigoProducto' => new \Twig_Function_Method($this, 'obtenerCodigoProducto'),
            'obtenerStock'          => new \Twig_Function_Method($this, 'obtenerStock'),
            'obtenerVentaTotalMes'  => new \Twig_Function_Method($this, 'obtenerVentaTotalMes'),
            'generarCodigoBarras'   => new \Twig_Function_Method($this, 'generarCodigoBarras'),
            'obtenerVentaTotalMesXEmpresa' => new \Twig_Function_Method($this, 'obtenerVentaTotalMesXEmpresa'),
            'obtenerCompraTotalMesXEmpresa' => new \Twig_Function_Method($this, 'obtenerCompraTotalMesXEmpresa'),
            'obtenerGastoTotalMesXEmpresa' => new \Twig_Function_Method($this, 'obtenerGastoTotalMesXEmpresa'),
            'diferencia_dias' => new \Twig_Function_Method($this, 'diferencia_dias'),
            'obtenerDatosEntrega' => new \Twig_Function_Method($this, 'obtenerDatosEntrega'),
            'obtenerDatosTransporte' => new \Twig_Function_Method($this, 'obtenerDatosTransporte'),
            'obtenerDatosEntregaParcialTransporte' => new \Twig_Function_Method($this, 'obtenerDatosEntregaParcialTransporte'),
            'verificaEstadoCaja' => new \Twig_Function_Method($this, 'verificaEstadoCaja'),
            'obtenerCumpleanerosHoy' => new \Twig_Function_Method($this, 'obtenerCumpleanerosHoy'),
            'obtenerNombreEmpresa' => new \Twig_Function_Method($this, 'obtenerNombreEmpresa'),
            'numeroALetras' => new \Twig_Function_Method($this, 'numeroALetras'),
            'obtenerImagenCategoriaDefault' => new \Twig_Function_Method($this, 'obtenerImagenCategoriaDefault'),
            'obtenerImagenProductoDefault'  => new \Twig_Function_Method($this, 'obtenerImagenProductoDefault'),
            'ObtenerCajaSeleccionada'  => new \Twig_Function_Method($this, 'ObtenerCajaSeleccionada'),
            'permiteVentaConStockNegativo'  => new \Twig_Function_Method($this, 'permiteVentaConStockNegativo'),
            'mostrarServicios' => new \Twig_Function_Method($this, 'mostrarServicios'),
            'ObtenerLocalSeleccionado' => new \Twig_Function_Method($this, 'ObtenerLocalSeleccionado'),
            'funcionCajaYBanco' => new \Twig_Function_Method($this, 'funcionCajaYBanco'),
            'tieneAnulacion' => new \Twig_Function_Method($this, 'tieneAnulacion'),
            'obtenerMontoDisponible' => new \Twig_Function_Method($this, 'obtenerMontoDisponible'),
            'obtenerMontoPagado' => new \Twig_Function_Method($this, 'obtenerMontoPagado'),
            'obtenerCambiosStockManual' => new \Twig_Function_Method($this, 'obtenerCambiosStockManual'),
            'obtenerStockInicial' =>  new \Twig_Function_Method($this, 'obtenerStockInicial'),
            'mostrarGuiaRemision' =>  new \Twig_Function_Method($this, 'mostrarGuiaRemision'),
            'generarCodigoQR' => new \Twig_Function_Method($this, 'generarCodigoQR'),
            'obtenerDetalleVenta' => new \Twig_Function_Method($this, 'obtenerDetalleVenta'),
            'obtenerCuentasBancarias' => new \Twig_Function_Method($this, 'obtenerCuentasBancarias'),

        );
    }

    public function getName()
    {
        return 'App extension';
    }

    public function jsonDecode($str) {
        return json_decode($str);
    }
    
    public function parteEntera($number) {

        return intval($number); 
    }

    public function parteDecimal($number) {

        $decimal = round(($number - intval($number))*100,0);

        if($decimal == 0){ $decimal = '00';};

        return $decimal;
    }

    public function obtenerHash($str) {

        return sha1($str); 
    }

    public function formatNumTelefono($num_telefono_temp)
    {
        $num_telefono = trim($num_telefono_temp);

        if(strlen($num_telefono)> 3)
        {
            $num_telefono_s = str_replace('-', '', $num_telefono);

            $block1 = substr($num_telefono_s, 0, 3);
            $block2 = substr($num_telefono_s, 3);

            return $block1.'-'.$block2;
        }
        else
        {
            return $num_telefono;
        }

    }

    public function tieneSubmenu($padre,$rol_id)
    {

        $sql = "SELECT * FROM menu_x_rol mxr INNER JOIN menu m
                ON (mxr.menu_id = m.id) WHERE
                mxr.estado = 1 AND mxr.rol_id = ? AND m.padre = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $rol_id);
        $stmt->bindValue(2, $padre);
        $stmt->execute();
        $menus = $stmt->fetchAll();

        $num_items = count($menus);

        return $num_items;
    }

    public function obtenerCodigoProducto($producto_id)
    {
        $sql = "SELECT p.codigo FROM producto p  WHERE p.id = ? ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $producto_id);
        $stmt->execute();
        $codigo = $stmt->fetchColumn();

        return $codigo;
    }

    public function obtenerStock($producto_id)
    {
        $sql = "SELECT SUM(pxl.stock) as stock FROM producto_x_local pxl WHERE pxl.producto_id = ? ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $producto_id);
        $stmt->execute();
        $stock = $stmt->fetchColumn();

        return $stock;
    }

    public function obtenerVentaTotalMes($local)
    {
        $sql = "SELECT SUM(vfp.cantidad + IFNULL(vfp.igv,0)) AS total FROM factura_venta fv
                    INNER JOIN venta v ON fv.venta_id = v.id
                    INNER JOIN venta_forma_pago vfp ON vfp.venta_id = v.id
                    INNER JOIN forma_pago fp ON vfp.forma_pago_id = fp.id
                    INNER JOIN empresa_local el ON fv.empresa_local_id = el.id
                    INNER JOIN empresa e ON el.empresa_id = e.id
                    WHERE v.estado = 1 AND fv.tipo_id = 2 AND el.id = ? AND MONTH(fv.fecha) = MONTH(CURRENT_DATE()) AND YEAR(fv.fecha) = YEAR(CURRENT_DATE()) ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $local);
        $stmt->execute();
        $venta_total_mes = $stmt->fetchColumn();

        // $sql = "SELECT SUM(vfp.igv) as total FROM factura_venta fv
        //             INNER JOIN venta v ON fv.venta_id = v.id
        //             INNER JOIN venta_forma_pago vfp ON vfp.venta_id = v.id
        //             INNER JOIN empresa_local el ON fv.empresa_local_id = el.id
        //             WHERE v.estado = 1 AND fv.tipo_id = 2 AND el.id = ? AND MONTH(v.fecha) = MONTH(CURRENT_DATE()) AND YEAR(v.fecha) = YEAR(CURRENT_DATE()) ";
        // $stmt = $this->conn->prepare($sql);
        // $stmt->bindValue(1, $local);
        // $stmt->execute();
        // $venta_igv_mes = $stmt->fetchColumn();

        // $venta_total_mes = $venta_total_mes + $venta_igv_mes;

        return $venta_total_mes;
    }

    public function obtenerVentaTotalMesXEmpresa($empresa,$local='',$mes_ant='')
    {
        $sql = "SELECT SUM(vfp.cantidad * (CASE WHEN vfp.moneda_id = 2 THEN vfp.valor_tipo_cambio ELSE 1 END) + IFNULL(vfp.igv,0)) AS total FROM factura_venta fv
                    INNER JOIN venta v ON fv.venta_id = v.id
                    INNER JOIN venta_forma_pago vfp ON vfp.venta_id = v.id
                    INNER JOIN forma_pago fp ON vfp.forma_pago_id = fp.id
                    INNER JOIN empresa_local el ON fv.empresa_local_id = el.id
                    INNER JOIN empresa e ON el.empresa_id = e.id
                    WHERE v.estado = 1 AND fv.tipo_id = 2 AND e.id = ? ";
        if($mes_ant == ''){
            $sql .= " AND MONTH(fv.fecha) = MONTH(CURRENT_DATE()) AND YEAR(fv.fecha) = YEAR(CURRENT_DATE()) ";
        }else{
            $sql .= " AND MONTH(fv.fecha) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH )) AND YEAR(fv.fecha) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH )) ";
        }
        
        if($local != ''){
            $sql .= " AND el.id = ? ";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $empresa);
        if($local != ''){
            $stmt->bindValue(2, $local);
        }        
        $stmt->execute();
        $venta_total_mes = $stmt->fetchColumn();

        // $sql = "SELECT SUM(vfp.igv) as total FROM factura_venta fv
        //             INNER JOIN venta v ON fv.venta_id = v.id
        //             INNER JOIN venta_forma_pago vfp ON vfp.venta_id = v.id
        //             INNER JOIN empresa_local el ON fv.empresa_local_id = el.id
        //             INNER JOIN empresa e ON el.empresa_id = e.id
        //             WHERE v.estado = 1 AND fv.tipo_id = 2 AND fv.incluir_igv = 0 AND e.id = ? ";
        // if($mes_ant == ''){
        //     $sql .= " AND MONTH(fv.fecha) = MONTH(CURRENT_DATE()) AND YEAR(fv.fecha) = YEAR(CURRENT_DATE()) ";
        // }else{
        //     $sql .= " AND MONTH(fv.fecha) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH )) AND YEAR(fv.fecha) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH )) ";
        // }
        
        // if($local != ''){
        //     $sql .= " AND el.id = ? ";
        // }

        // $stmt = $this->conn->prepare($sql);
        // $stmt->bindValue(1, $empresa);
        // if($local != ''){
        //     $stmt->bindValue(2, $local);
        // }        
        // $stmt->execute();
        // $venta_igv_mes = $stmt->fetchColumn();

        // $venta_total_mes = $venta_total_mes + $venta_igv_mes;


        return $venta_total_mes;
    }

    public function obtenerCompraTotalMesXEmpresa($empresa,$local='',$mes_ant='')
    {
        $sql = "SELECT SUM(dc.subtotal) as total FROM detalle_compra dc
                    INNER JOIN compra c  ON dc.compra_id = c.id
                    INNER JOIN factura_compra fc ON fc.compra_id = c.id
                    INNER JOIN compra_forma_pago cfp  ON cfp.compra_id = c.id
                    INNER JOIN producto_x_local pxl ON dc.producto_x_local_id = pxl.id
                    INNER JOIN empresa_local el ON pxl.empresa_local_id = el.id
                    INNER JOIN empresa e ON el.empresa_id = e.id
                    WHERE fc.estado = 1 AND cfp.forma_pago_id <> 4 AND e.id = ? ";
        if($mes_ant == ''){
            $sql .= " AND MONTH(fc.fecha) = MONTH(CURRENT_DATE()) AND YEAR(fc.fecha) = YEAR(CURRENT_DATE()) ";
        }else{
            $sql .= " AND MONTH(fc.fecha) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH )) AND YEAR(fc.fecha) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH )) ";
        }
        
        if($local != ''){
            $sql .= " AND el.id = ? ";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $empresa);
        if($local != ''){
            $stmt->bindValue(2, $local);
        }          
        $stmt->execute();
        $compra_total_mes = $stmt->fetchColumn();

        return $compra_total_mes;
    }

    public function obtenerGastoTotalMesXEmpresa($empresa,$local='',$mes_ant='')
    {
        $sql = "SELECT SUM(g.monto) as total FROM gasto g
                    INNER JOIN empresa_local el ON g.empresa_local_id = el.id
                    INNER JOIN empresa e ON el.empresa_id = e.id
                    WHERE g.estado = 1 AND e.id = ? ";

        if($mes_ant == ''){
            $sql .= " AND MONTH(g.fecha) = MONTH(CURRENT_DATE()) AND YEAR(g.fecha) = YEAR(CURRENT_DATE()) ";
        }else{
            $sql .= " AND MONTH(g.fecha) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH )) AND YEAR(g.fecha) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH )) "; 
        }            
        
        if($local != ''){
            $sql .= " AND el.id = ? ";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $empresa);
        if($local != ''){
            $stmt->bindValue(2, $local);
        }            
        $stmt->execute();
        $gasto_total_mes = $stmt->fetchColumn();

        return $gasto_total_mes;
    }

    public function generarCodigoBarras($codigobarra='')
    {

        $format = 'png';
        $symbology = 'ean-128';
        $data = $codigobarra;
        $options = array('ms'=>'s','md'=>0.8,'h'=>100,'w'=>300);//'sf'=>2,

        $generator = $this->barcode;//new Barcode();

        /* Output directly to standard output. */
        //$generator->output_image($format, $symbology, $data, $options);
        /* Create bitmap image. */
        //$image = $generator->render_image($symbology, $data, $options);
        //imagepng($image);
        //imagedestroy($image);

        /* Generate SVG markup. */
        $svg = $generator->render_svg($symbology, $data, $options);


        return $svg;

    }

    public function obtenerSvg($svg){

        $findme   = '<svg';
        $pos = strpos($svg, $findme);

        if ($pos !== false) {

            $svg = mb_substr($svg,$pos);
            return $svg;

        } else {
            return 'no encontrado';
        }


    }

    public function nombreMes($mes){

        switch ($mes) {
            case '1':
                $nombre_mes = 'Enero';
                break;
            case '2':
                $nombre_mes = 'Febrero';
                break;
            
            case '3':
                $nombre_mes = 'Marzo';
                break;
            
            case '4':
                $nombre_mes = 'Abril';
                break;
            
            case '5':
                $nombre_mes = 'Mayo';
                break;
            
            case '6':
                $nombre_mes = 'Junio';
                break;
            
            case '7':
                $nombre_mes = 'Julio';
                break;
            
            case '8':
                $nombre_mes = 'Agosto';
                break;
            
            case '9':
                $nombre_mes = 'Setiembre';
                break;
            
            case '10':
                $nombre_mes = 'Octubre';
                break;
            
            case '11':
                $nombre_mes = 'Noviembre';
                break;
            
            case '12':
                $nombre_mes = 'Diciembre';
                break;
            
        }

        return $nombre_mes;

    }

    public function diferencia_dias($date , $differenceFormat = '%a' )
    {
        $datetime1 = new \DateTime();
        $datetime2 = date_create($date);
        
        $interval = date_diff($datetime1, $datetime2);
        
        return $interval->format($differenceFormat);
        
    }

    public function obtenerDatosEntrega($identificador,$tabla){

        $sql = "SELECT d.cliente as cliente,d.direccion as direccion FROM ";
        $sql .= $tabla." d ";
        $sql .= "  WHERE d.identificador = ?  LIMIT 1 ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $identificador);
        $stmt->execute();
        $entrega = $stmt->fetch();

        return $entrega;

    }

    public function obtenerDatosTransporte($id){

        $sql  = "SELECT * FROM transferencia_x_transporte ";
        $sql .= " WHERE transferencia_id = ?  ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $data = $stmt->fetchAll();

        return $data;

    }

    public function obtenerDatosEntregaParcialTransporte($id){

        $sql  = "SELECT punto_partida FROM detalle_venta_entrega_datos_envio ";
        $sql .= " WHERE identificador = ?  LIMIT 1 ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $data = $stmt->fetchColumn();

        return $data;

    }

    public function verificaEstadoCaja($caja=null)
    {

        if(!$caja){
            return false;
        }

        $dql = "SELECT ca FROM AppBundle:CajaApertura ca ";
        $dql .= " JOIN ca.caja c";
        $dql .= " WHERE  c.id =:caja_id  AND ca.estado = 1 ";

        $query = $this->em->createQuery($dql);

        $query->setParameter('caja_id',$caja);
 
        $cajaApertura = $query->getOneOrNullResult();

        if(!$cajaApertura){
            return false;
        }

        return true;
    }

    public function obtenerCumpleanerosHoy($empresa)
    {
        $fecha = new \DateTime();

        $mes = $fecha->format('m');
        $dia = $fecha->format('d');


        $sql  = "SELECT * FROM servicio s INNER JOIN empresa_local el ON s.empresa_local_id = el.id INNER JOIN empresa e ON el.empresa_id = e.id ";
        $sql .= " WHERE s.estado = 1 AND e.id = ? AND MONTH(s.fecha_nacimiento) = ? AND DAY(s.fecha_nacimiento) = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $empresa);
        $stmt->bindValue(2, $mes);
        $stmt->bindValue(3, $dia);
        $stmt->execute();
        $servicios = $stmt->fetchAll();


        return $servicios;
    }

    public function obtenerNombreEmpresa($empresa)
    {

        $sql  = "SELECT nombre_corto FROM empresa  ";
        $sql .= " WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $empresa);
        $stmt->execute();
        $nombreempresa = $stmt->fetchColumn();


        return $nombreempresa;
    }

    public function mostrarServicios($empresa)
    {

        $sql  = "SELECT mostrar_servicios FROM empresa  ";
        $sql .= " WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $empresa);
        $stmt->execute();
        $mostrar_servicios = $stmt->fetchColumn();


        return $mostrar_servicios;
    }

    public function obtenerImagenCategoriaDefault($local)
    {

        $sql  = "SELECT imagen_categoria_default FROM empresa_local  ";
        $sql .= " WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $local);
        $stmt->execute();
        $imagen_categoria_default = $stmt->fetchColumn();


        return $imagen_categoria_default;
    }

    public function obtenerImagenProductoDefault($local)
    {

        $sql  = "SELECT imagen_producto_default FROM empresa_local  ";
        $sql .= " WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $local);
        $stmt->execute();
        $imagen_producto_default = $stmt->fetchColumn();


        return $imagen_producto_default;
    }

    /*
    *Numero a letras
    */
    private static $UNIDADES = [
        '',
        'UN ',
        'DOS ',
        'TRES ',
        'CUATRO ',
        'CINCO ',
        'SEIS ',
        'SIETE ',
        'OCHO ',
        'NUEVE ',
        'DIEZ ',
        'ONCE ',
        'DOCE ',
        'TRECE ',
        'CATORCE ',
        'QUINCE ',
        'DIECISEIS ',
        'DIECISIETE ',
        'DIECIOCHO ',
        'DIECINUEVE ',
        'VEINTE '
    ];
    private static $DECENAS = [
        'VENTI',
        'TREINTA ',
        'CUARENTA ',
        'CINCUENTA ',
        'SESENTA ',
        'SETENTA ',
        'OCHENTA ',
        'NOVENTA ',
        'CIEN '
    ];
    private static $CENTENAS = [
        'CIENTO ',
        'DOSCIENTOS ',
        'TRESCIENTOS ',
        'CUATROCIENTOS ',
        'QUINIENTOS ',
        'SEISCIENTOS ',
        'SETECIENTOS ',
        'OCHOCIENTOS ',
        'NOVECIENTOS '
    ];
    public function numeroALetras($number, $moneda = '', $centimos = '', $forzarCentimos = false)
    {
        $converted = '';
        $decimales = '';
        if (($number < 0) || ($number > 999999999)) {
            return 'No es posible convertir el numero a letras';
        }
        $div_decimales = explode('.',$number);
        if(count($div_decimales) > 1){
            $number = $div_decimales[0];
            $decNumberStr = (string) $div_decimales[1];
            if(strlen($decNumberStr) == 2){
                $decNumberStrFill = str_pad($decNumberStr, 9, '0', STR_PAD_LEFT);
                $decCientos = substr($decNumberStrFill, 6);
                $decimales = self::convertGroup($decCientos);
            }
        }
        else if (count($div_decimales) == 1 && $forzarCentimos){
            $decimales = 'CERO ';
        }
        $numberStr = (string) $number;
        $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
        $millones = substr($numberStrFill, 0, 3);
        $miles = substr($numberStrFill, 3, 3);
        $cientos = substr($numberStrFill, 6);
        if (intval($millones) > 0) {
            if ($millones == '001') {
                $converted .= 'UN MILLON ';
            } else if (intval($millones) > 0) {
                $converted .= sprintf('%sMILLONES ', self::convertGroup($millones));
            }
        }
        if (intval($miles) > 0) {
            if ($miles == '001') {
                $converted .= 'MIL ';
            } else if (intval($miles) > 0) {
                $converted .= sprintf('%sMIL ', self::convertGroup($miles));
            }
        }
        if (intval($cientos) > 0) {
            if ($cientos == '001') {
                $converted .= 'UN ';
            } else if (intval($cientos) > 0) {
                $converted .= sprintf('%s ', self::convertGroup($cientos));
            }
        }
        if(empty($decimales)){
            $valor_convertido = $converted . strtoupper($moneda);
        } else {
            $valor_convertido = $converted . strtoupper($moneda) . ' CON ' . $decimales . ' ' . strtoupper($centimos);
        }

        return $valor_convertido;
    }
    
    private static function convertGroup($n)
    {
        $output = '';
        if ($n == '100') {
            $output = "CIEN ";
        } else if ($n[0] !== '0') {
            $output = self::$CENTENAS[$n[0] - 1];
        }
        $k = intval(substr($n,1));
        if ($k <= 20) {
            $output .= self::$UNIDADES[$k];
        } else {
            if(($k > 30) && ($n[2] !== '0')) {
                $output .= sprintf('%sY %s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            } else {
                $output .= sprintf('%s%s', self::$DECENAS[intval($n[1]) - 2], self::$UNIDADES[intval($n[2])]);
            }
        }
        return $output;
    }

    public function ObtenerCajaSeleccionada($caja=null)
    {
        if(!$caja){
            return 'No hay caja seleccionada';
        }

        $sql  = "SELECT nombre FROM caja  ";
        $sql .= " WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $caja);
        $stmt->execute();
        $nombrecaja = $stmt->fetchColumn();


        return $nombrecaja;
    }

    public function ObtenerLocalSeleccionado($local=null)
    {
        if(!$local){
            return 'No hay local seleccionado';
        }

        $sql  = "SELECT nombre FROM empresa_local  ";
        $sql .= " WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $local);
        $stmt->execute();
        $nombrelocal = $stmt->fetchColumn();


        return $nombrelocal;
    }

    public function permiteVentaConStockNegativo($local){

        $local = $this->em->getRepository('AppBundle:EmpresaLocal')->find($local);

        return $local->getVentaNegativo();



    }

    public function funcionCajaYBanco($local){

        $sql  = "SELECT cajaybanco FROM empresa_local WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $local);
        $stmt->execute();
        $cajaybanco = $stmt->fetchColumn();

        return $cajaybanco;

    }

    public function tieneAnulacion($id){

        $dql = "SELECT ca FROM AppBundle:DetalleCompraAnulada ca ";
        $dql .= " JOIN ca.detalleCompra d";
        $dql .= " WHERE  d.id =:id  ";

        $query = $this->em->createQuery($dql);

        $query->setParameter('id',$id);
 
        $compraAnulada = $query->getOneOrNullResult();

        if(!$compraAnulada){
            return false;
        }

        return $compraAnulada;
    }    

    public function obtenerMontoDisponible($id){

        $sql  = "SELECT td.transaccion_id,td.caja_cuentabanco_id,td.tipo_documento AS tipo_documento,td.numero_documento AS numero_documento,td.monto AS monto,
                    (SELECT tipo FROM transaccion WHERE id = td.transaccion_id)  AS tipo,
                    (SELECT identificador FROM transaccion WHERE id = td.transaccion_id)  AS identificador 
                    FROM transaccion_detalle td WHERE td.caja_cuentabanco_id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $transaccionDetalle = $stmt->fetchAll();

        $monto_disponible = 0;
        foreach($transaccionDetalle as $i=>$detalle){

            $monto = $detalle['monto'];
            $transaccion_id = $detalle['transaccion_id'];
            $identificador = $detalle['identificador'];
            $tipo_documento = $detalle['tipo_documento'];
            $tipo = $detalle['tipo'];

            switch ($tipo) {
                case 'c':

                    $sql  = "SELECT COUNT(*) AS anulado FROM factura_compra fc 
                                WHERE fc.estado = 0 AND fc.id = ? ";

                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindValue(1, $identificador);
                    $stmt->execute();
                    $anulado = $stmt->fetchColumn();

                    if($anulado != 0){
                        break;
                    }

                    $monto_disponible = $monto_disponible - $monto;
                    break;
                case 'v':

                    $sql  = "SELECT COUNT(*) AS anulado FROM factura_venta fv INNER JOIN venta v ON fv.venta_id = v.id
                                WHERE v.estado = 0 AND fv.id = ? ";

                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindValue(1, $identificador);
                    $stmt->execute();
                    $anulado = $stmt->fetchColumn();

                    if($anulado != 0){
                        break;
                    }

                    $monto_disponible = $monto_disponible + $monto;

                    break;
                case 'c_a':

                    if($tipo_documento != 'nota_credito'){
                        $monto_disponible = $monto_disponible + $monto;
                    }
                    
                    break;
                case 'v_a':

                    if($tipo_documento != 'nota_credito'){
                        $monto_disponible = $monto_disponible - $monto;
                    }

                    break;                                                        
                default:
                    # code...
                    break;
            }


        }

        $sql  = "SELECT SUM(monto) FROM transferencia_dinero WHERE salida =  ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $montoSalidaXTransferencia = $stmt->fetchColumn();


        $sql  = "SELECT SUM(monto) FROM transferencia_dinero WHERE entrada =  ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $montoEntradaXTransferencia = $stmt->fetchColumn();

        if($montoSalidaXTransferencia){
            $monto_disponible = $monto_disponible  -  $montoSalidaXTransferencia;
        }
        if($montoEntradaXTransferencia){
            $monto_disponible = $monto_disponible  +  $montoEntradaXTransferencia;
        }

        return $monto_disponible;

    }   

    public function obtenerMontoPagado($id,$tipo){

        $sql  = "SELECT SUM(td.monto) AS monto FROM transaccion_detalle td 
                    INNER JOIN transaccion t ON td.transaccion_id = t.id
                    WHERE t.identificador = ? AND t.tipo = ?
                    GROUP BY t.identificador ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->bindValue(2, $tipo);
        $stmt->execute();
        $monto = $stmt->fetchColumn();

        return $monto;

    }

    public function obtenerCambiosStockManual($producto){

        $sql  = "SELECT p.nombre AS producto,lm.valor,lm.valor_nuevo,IFNULL(lm.fecha_creacion,lm.fecha) AS fecha,mt.nombre AS modificacion FROM log_modificacion lm 
                    INNER JOIN modificacion_tipo mt ON lm.modificacion_tipo_id = mt.id
                    INNER JOIN producto_x_local pxl ON lm.producto_x_local_id = pxl.id
                    INNER JOIN producto p ON pxl.producto_id = p.id
                    WHERE mt.codigo = 4 AND lm.producto_x_local_id = ?
                    ORDER BY lm.fecha DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $producto);
        $stmt->execute();
        $modificacion_stock_manual = $stmt->fetchAll();

        return $modificacion_stock_manual;

    }

    public function obtenerStockInicial($producto){

        $sql  = "SELECT IFNULL(stock_inicial,0) AS stock_inicial,IFNULL(stock,0) AS stock FROM producto_x_local WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $producto);
        $stmt->execute();
        $stock_inicial = $stmt->fetch();

        return $stock_inicial;

    }

    public function mostrarGuiaRemision($local){

        $sql  = "SELECT mostrar_guia_remision FROM empresa_local WHERE id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $local);
        $stmt->execute();
        $mostrarguia = $stmt->fetchColumn();

        return $mostrarguia;

    }

    public function generarCodigoQR($codigobarra='')
    {

        $format = 'png';
        $symbology = 'qr-q';
        $data = $codigobarra;
        $options = array('ms'=>'s','md'=>1,'h'=>180,'w'=>180,'p'=>0,'pv'=>0,'ph'=>0);//'sf'=>2,

        $generator = $this->barcode;//new Barcode();

        /* Output directly to standard output. */
        //$generator->output_image($format, $symbology, $data, $options);
        /* Create bitmap image. */
        //$image = $generator->render_image($symbology, $data, $options);
        //imagepng($image);
        //imagedestroy($image);

        /* Generate SVG markup. */
        $svg = $generator->render_svg($symbology, $data, $options);


        return $svg;

    }
    
    public function obtenerDetalleVenta($venta_id)
    {
        $sql = "SELECT 'NIU' AS 'unidad',p.codigo,p.nombre,dv.cantidad,dv.precio,dv.subtotal
                FROM detalle_venta dv 
                INNER JOIN producto_x_local pxl ON pxl.id = dv.producto_x_local_id
                INNER JOIN producto p ON pxl.producto_id = p.id
                WHERE dv.venta_id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $venta_id);
    
        $stmt->execute();
        $detalles = $stmt->fetchAll();

        $result = '';

        foreach($detalles as $detalle)
        {
            $result .= '[UM = '.$detalle['unidad'].' | COD = '.$detalle['codigo'].' | DETALLE = '.$detalle['nombre'].' | CANT = '.$detalle['cantidad'].' | VU = '.$detalle['precio'].' | IGV-TIPO = 1 | IGV = '.number_format(($detalle['subtotal']/1.18) * 0.18,2).' | TOTAL = '.number_format($detalle['subtotal'],2).' ] ';

        }

        return $result;
    }

    public function obtenerCuentasBancarias($empresa)
    {

        $sql = "SELECT m.nombre AS 'moneda',cb.banco,cb.numero,cb.numero_interbancario,m.simbolo
                FROM cuenta_banco cb 
                INNER JOIN moneda m ON cb.moneda_id = m.id
                INNER JOIN empresa e ON cb.empresa_id = e.id
                WHERE cb.estado = 1 AND  cb.mostrar_en_comprobante = 1 AND e.id = ? ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $empresa);
    
        $stmt->execute();
        $cuentas = $stmt->fetchAll();


        return $cuentas;

    }
        
}