<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EmpresaLocal
 *
 * @ORM\Table(name="empresa_local")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmpresaLocalRepository")
 *
 */
class EmpresaLocal
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=100)
     * @Assert\NotBlank(message="Este valor es requerido")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=100, nullable=true)
     * 
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=32, nullable=true)
     * @Assert\NotBlank(message="Este valor es requerido")
     * 
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=100, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="limite_venta", type="string", length=32, nullable=true)
     */
    private $limite_venta;


    /**
     * @ORM\ManyToOne(targetEntity="Empresa", inversedBy="empresaLocal", cascade={"persist"})
     * @ORM\JoinColumn(name="empresa_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    protected $empresa;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;
    
    /**
     * @ORM\ManyToOne(targetEntity="Distrito", inversedBy="empresaLocal", cascade={"persist"})
     * @ORM\JoinColumn(name="distrito_id", referencedColumnName="id")
     * 
     */
    protected $distrito;

    /**
     * @var string
     *
     * @ORM\Column(name="serie_guiaremision", type="string", length=32)
     * @Assert\NotBlank(message="Este valor es requerido")
     * @Assert\Length(
     *      max = 4,
     *      maxMessage = "La serie no debe exceder de  {{ limit }} caracteres"
     * )        
     */
    private $serieGuiaremision;

    /**
     * @var string
     *
     * @ORM\Column(name="serie_boleta", type="string", length=32)
     * @Assert\NotBlank(message="Este valor es requerido")
     * @Assert\Length(
     *      max = 4,
     *      maxMessage = "La serie no debe exceder de  {{ limit }} caracteres"
     * )     
     */
    private $serieBoleta;

    /**
     * @var string
     *
     * @ORM\Column(name="serie_factura", type="string", length=32)
     * @Assert\NotBlank(message="Este valor es requerido")
     * @Assert\Length(
     *      max = 4,
     *      maxMessage = "La serie no debe exceder de  {{ limit }} caracteres"
     * )        
     */
    private $serieFactura;

    /**
     * @var string
     *
     * @ORM\Column(name="serie_notacredito_factura", type="string", length=4, nullable=true)
     *      
     */
    private $serieNotacreditoFactura;

    /**
     * @var string
     *
     * @ORM\Column(name="serie_notacredito_boleta", type="string", length=4, nullable=true)
     *      
     */
    private $serieNotacreditoBoleta;

    /**
     * @var string
     *
     * @ORM\Column(name="serie_notadebito_factura", type="string", length=4, nullable=true)
     *      
     */
    private $serieNotadebitoFactura;

    /**
     * @var string
     *
     * @ORM\Column(name="serie_notadebito_boleta", type="string", length=4, nullable=true)
     *      
     */
    private $serieNotadebitoBoleta;


    /**
     * @var string
     *
     * @ORM\Column(name="prefijo_voucher", type="string", length=3)
     * @Assert\NotBlank(message="Este valor es requerido")
     * @Assert\Length(
     *      max = 3,
     *      maxMessage = "El prefijo no debe exceder de  {{ limit }} caracteres"
     * )    
     */
    private $prefijoVoucher;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     * 
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="url_facturacion", type="string", length=255,nullable=true)
     */
    private $urlFacturacion;

    /**
     * @var string
     *
     * @ORM\Column(name="token_facturacion", type="string", length=255,nullable=true)
     */
    private $tokenFacturacion;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="facturacion_electronica", type="boolean", nullable=true)
     */
    private $facturacionElectronica;

    
    /**
     * @var string
     *
     *     
     * @ORM\Column(name="imagen_producto_default", type="string", length=100, nullable=true)
     */
    private $imagenProductoDefault;

    /**
     * @var string
     *
     *     
     * @ORM\Column(name="imagen_categoria_default", type="string", length=100, nullable=true)
     */
    private $imagenCategoriaDefault;
    

    /**
     * @var bool
     *
     * @ORM\Column(name="venta_negativo", type="boolean", nullable=true)
     */
    private $ventaNegativo;


    /**
     * @var string
     *
     * @ORM\Column(name="notaventa_ancho", type="string", length=32,nullable=true)
     */
    private $notaventaAncho;


    /**
     * @var string
     *
     * @ORM\Column(name="notaventa_largo", type="string", length=32,nullable=true)
     */
    private $notaventaLargo;


    /**
     * @var string
     *
     * @ORM\Column(name="notaventa_formato", type="string", length=32,nullable=true)
     */
    private $notaventaFormato;

    /**
     * @var bool
     *
     * @ORM\Column(name="cajaybanco", type="boolean", nullable=true)
     */
    private $cajaybanco;
    
    /**
     * @var string
     *
     * @ORM\Column(name="razon_comercial", type="string", length=100, nullable=true)
     */
    private $razonComercial;

    /**
     * @var string
     *
     * @ORM\Column(name="ruc_comercial", type="string", length=100, nullable=true)
     */
    private $rucComercial;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="ventana_vuelto", type="boolean", nullable=true)
     */
    private $ventanaVuelto;

    /**
     * @var bool
     *
     * @ORM\Column(name="mostrar_guia_remision", type="boolean", nullable=true)
     */
    private $mostrarGuiaRemision;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=100,nullable=true)
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="factura_formato", type="string", length=32,nullable=true)
     */
    private $facturaFormato;

    /**
     * @var string
     *
     * @ORM\Column(name="boleta_formato", type="string", length=32,nullable=true)
     */
    private $boletaFormato;

    /**
     * @var string
     *
     * @ORM\Column(name="boleta_color", type="string", length=8,nullable=true)
     */
    private $boletaColor;

    /**
     * @var string
     *
     * @ORM\Column(name="factura_color", type="string", length=8,nullable=true)
     */
    private $facturaColor;
    
    
    public function __toString()
    {
        return $this->getNombre();
    }
    

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return EmpresaLocal
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return EmpresaLocal
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return EmpresaLocal
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return EmpresaLocal
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set empresa
     *
     * @param \AppBundle\Entity\Empresa $empresa
     *
     * @return EmpresaLocal
     */
    public function setEmpresa(\AppBundle\Entity\Empresa $empresa = null)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return \AppBundle\Entity\Empresa
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set limiteVenta
     *
     * @param string $limiteVenta
     *
     * @return EmpresaLocal
     */
    public function setLimiteVenta($limiteVenta)
    {
        $this->limite_venta = $limiteVenta;

        return $this;
    }

    /**
     * Get limiteVenta
     *
     * @return string
     */
    public function getLimiteVenta()
    {
        return $this->limite_venta;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     *
     * @return EmpresaLocal
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return boolean
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set distrito
     *
     * @param \AppBundle\Entity\Distrito $distrito
     *
     * @return EmpresaLocal
     */
    public function setDistrito(\AppBundle\Entity\Distrito $distrito = null)
    {
        $this->distrito = $distrito;

        return $this;
    }

    /**
     * Get distrito
     *
     * @return \AppBundle\Entity\Distrito
     */
    public function getDistrito()
    {
        return $this->distrito;
    }

    /**
     * Set serieBoleta
     *
     * @param string $serieBoleta
     *
     * @return EmpresaLocal
     */
    public function setSerieBoleta($serieBoleta)
    {
        $this->serieBoleta = $serieBoleta;

        return $this;
    }

    /**
     * Get serieBoleta
     *
     * @return string
     */
    public function getSerieBoleta()
    {
        return $this->serieBoleta;
    }

    /**
     * Set serieFactura
     *
     * @param string $serieFactura
     *
     * @return EmpresaLocal
     */
    public function setSerieFactura($serieFactura)
    {
        $this->serieFactura = $serieFactura;

        return $this;
    }

    /**
     * Get serieFactura
     *
     * @return string
     */
    public function getSerieFactura()
    {
        return $this->serieFactura;
    }

    /**
     * Set prefijoVoucher
     *
     * @param string $prefijoVoucher
     *
     * @return EmpresaLocal
     */
    public function setPrefijoVoucher($prefijoVoucher)
    {
        $this->prefijoVoucher = $prefijoVoucher;

        return $this;
    }

    /**
     * Get prefijoVoucher
     *
     * @return string
     */
    public function getPrefijoVoucher()
    {
        return $this->prefijoVoucher;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return EmpresaLocal
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set urlFacturacion
     *
     * @param string $urlFacturacion
     *
     * @return EmpresaLocal
     */
    public function setUrlFacturacion($urlFacturacion)
    {
        $this->urlFacturacion = $urlFacturacion;

        return $this;
    }

    /**
     * Get urlFacturacion
     *
     * @return string
     */
    public function getUrlFacturacion()
    {
        return $this->urlFacturacion;
    }

    /**
     * Set tokenFacturacion
     *
     * @param string $tokenFacturacion
     *
     * @return EmpresaLocal
     */
    public function setTokenFacturacion($tokenFacturacion)
    {
        $this->tokenFacturacion = $tokenFacturacion;

        return $this;
    }

    /**
     * Get tokenFacturacion
     *
     * @return string
     */
    public function getTokenFacturacion()
    {
        return $this->tokenFacturacion;
    }

    /**
     * Set facturacionElectronica
     *
     * @param boolean $facturacionElectronica
     *
     * @return EmpresaLocal
     */
    public function setFacturacionElectronica($facturacionElectronica)
    {
        $this->facturacionElectronica = $facturacionElectronica;

        return $this;
    }

    /**
     * Get facturacionElectronica
     *
     * @return boolean
     */
    public function getFacturacionElectronica()
    {
        return $this->facturacionElectronica;
    }

    /**
     * Set imagenProductoDefault
     *
     * @param string $imagenProductoDefault
     *
     * @return EmpresaLocal
     */
    public function setImagenProductoDefault($imagenProductoDefault)
    {
        $this->imagenProductoDefault = $imagenProductoDefault;

        return $this;
    }

    /**
     * Get imagenProductoDefault
     *
     * @return string
     */
    public function getImagenProductoDefault()
    {
        return $this->imagenProductoDefault;
    }

    /**
     * Set imagenCategoriaDefault
     *
     * @param string $imagenCategoriaDefault
     *
     * @return EmpresaLocal
     */
    public function setImagenCategoriaDefault($imagenCategoriaDefault)
    {
        $this->imagenCategoriaDefault = $imagenCategoriaDefault;

        return $this;
    }

    /**
     * Get imagenCategoriaDefault
     *
     * @return string
     */
    public function getImagenCategoriaDefault()
    {
        return $this->imagenCategoriaDefault;
    }

    /**
     * Set ventaNegativo
     *
     * @param boolean $ventaNegativo
     *
     * @return EmpresaLocal
     */
    public function setVentaNegativo($ventaNegativo)
    {
        $this->ventaNegativo = $ventaNegativo;

        return $this;
    }

    /**
     * Get ventaNegativo
     *
     * @return boolean
     */
    public function getVentaNegativo()
    {
        return $this->ventaNegativo;
    }

    /**
     * Set notaventaAncho
     *
     * @param string $notaventaAncho
     *
     * @return EmpresaLocal
     */
    public function setNotaventaAncho($notaventaAncho)
    {
        $this->notaventaAncho = $notaventaAncho;

        return $this;
    }

    /**
     * Get notaventaAncho
     *
     * @return string
     */
    public function getNotaventaAncho()
    {
        return $this->notaventaAncho;
    }

    /**
     * Set notaventaLargo
     *
     * @param string $notaventaLargo
     *
     * @return EmpresaLocal
     */
    public function setNotaventaLargo($notaventaLargo)
    {
        $this->notaventaLargo = $notaventaLargo;

        return $this;
    }

    /**
     * Get notaventaLargo
     *
     * @return string
     */
    public function getNotaventaLargo()
    {
        return $this->notaventaLargo;
    }

    /**
     * Set notaventaFormato
     *
     * @param string $notaventaFormato
     *
     * @return EmpresaLocal
     */
    public function setNotaventaFormato($notaventaFormato)
    {
        $this->notaventaFormato = $notaventaFormato;

        return $this;
    }

    /**
     * Get notaventaFormato
     *
     * @return string
     */
    public function getNotaventaFormato()
    {
        return $this->notaventaFormato;
    }

    /**
     * Set cajaybanco
     *
     * @param boolean $cajaybanco
     *
     * @return EmpresaLocal
     */
    public function setCajaybanco($cajaybanco)
    {
        $this->cajaybanco = $cajaybanco;

        return $this;
    }

    /**
     * Get cajaybanco
     *
     * @return boolean
     */
    public function getCajaybanco()
    {
        return $this->cajaybanco;
    }

    /**
     * Set razonComercial
     *
     * @param string $razonComercial
     *
     * @return EmpresaLocal
     */
    public function setRazonComercial($razonComercial)
    {
        $this->razonComercial = $razonComercial;

        return $this;
    }

    /**
     * Get razonComercial
     *
     * @return string
     */
    public function getRazonComercial()
    {
        return $this->razonComercial;
    }

    /**
     * Set rucComercial
     *
     * @param string $rucComercial
     *
     * @return EmpresaLocal
     */
    public function setRucComercial($rucComercial)
    {
        $this->rucComercial = $rucComercial;

        return $this;
    }

    /**
     * Get rucComercial
     *
     * @return string
     */
    public function getRucComercial()
    {
        return $this->rucComercial;
    }

    /**
     * Set ventanaVuelto
     *
     * @param boolean $ventanaVuelto
     *
     * @return EmpresaLocal
     */
    public function setVentanaVuelto($ventanaVuelto)
    {
        $this->ventanaVuelto = $ventanaVuelto;

        return $this;
    }

    /**
     * Get ventanaVuelto
     *
     * @return boolean
     */
    public function getVentanaVuelto()
    {
        return $this->ventanaVuelto;
    }

    /**
     * Set serieGuiaremision
     *
     * @param string $serieGuiaremision
     *
     * @return EmpresaLocal
     */
    public function setSerieGuiaremision($serieGuiaremision)
    {
        $this->serieGuiaremision = $serieGuiaremision;

        return $this;
    }

    /**
     * Get serieGuiaremision
     *
     * @return string
     */
    public function getSerieGuiaremision()
    {
        return $this->serieGuiaremision;
    }

    /**
     * Set mostrarGuiaRemision
     *
     * @param boolean $mostrarGuiaRemision
     *
     * @return EmpresaLocal
     */
    public function setMostrarGuiaRemision($mostrarGuiaRemision)
    {
        $this->mostrarGuiaRemision = $mostrarGuiaRemision;

        return $this;
    }

    /**
     * Get mostrarGuiaRemision
     *
     * @return boolean
     */
    public function getMostrarGuiaRemision()
    {
        return $this->mostrarGuiaRemision;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return EmpresaLocal
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set facturaFormato
     *
     * @param string $facturaFormato
     *
     * @return EmpresaLocal
     */
    public function setFacturaFormato($facturaFormato)
    {
        $this->facturaFormato = $facturaFormato;

        return $this;
    }

    /**
     * Get facturaFormato
     *
     * @return string
     */
    public function getFacturaFormato()
    {
        return $this->facturaFormato;
    }

    /**
     * Set boletaFormato
     *
     * @param string $boletaFormato
     *
     * @return EmpresaLocal
     */
    public function setBoletaFormato($boletaFormato)
    {
        $this->boletaFormato = $boletaFormato;

        return $this;
    }

    /**
     * Get boletaFormato
     *
     * @return string
     */
    public function getBoletaFormato()
    {
        return $this->boletaFormato;
    }

    /**
     * Set boletaColor
     *
     * @param string $boletaColor
     *
     * @return EmpresaLocal
     */
    public function setBoletaColor($boletaColor)
    {
        $this->boletaColor = $boletaColor;

        return $this;
    }

    /**
     * Get boletaColor
     *
     * @return string
     */
    public function getBoletaColor()
    {
        return $this->boletaColor;
    }

    /**
     * Set facturaColor
     *
     * @param string $facturaColor
     *
     * @return EmpresaLocal
     */
    public function setFacturaColor($facturaColor)
    {
        $this->facturaColor = $facturaColor;

        return $this;
    }

    /**
     * Get facturaColor
     *
     * @return string
     */
    public function getFacturaColor()
    {
        return $this->facturaColor;
    }


    /**
     * Set serieNotacreditoFactura
     *
     * @param string $serieNotacreditoFactura
     *
     * @return EmpresaLocal
     */
    public function setSerieNotacreditoFactura($serieNotacreditoFactura)
    {
        $this->serieNotacreditoFactura = $serieNotacreditoFactura;

        return $this;
    }

    /**
     * Get serieNotacreditoFactura
     *
     * @return string
     */
    public function getSerieNotacreditoFactura()
    {
        return $this->serieNotacreditoFactura;
    }

    /**
     * Set serieNotacreditoBoleta
     *
     * @param string $serieNotacreditoBoleta
     *
     * @return EmpresaLocal
     */
    public function setSerieNotacreditoBoleta($serieNotacreditoBoleta)
    {
        $this->serieNotacreditoBoleta = $serieNotacreditoBoleta;

        return $this;
    }

    /**
     * Get serieNotacreditoBoleta
     *
     * @return string
     */
    public function getSerieNotacreditoBoleta()
    {
        return $this->serieNotacreditoBoleta;
    }

    /**
     * Set serieNotadebitoFactura
     *
     * @param string $serieNotadebitoFactura
     *
     * @return EmpresaLocal
     */
    public function setSerieNotadebitoFactura($serieNotadebitoFactura)
    {
        $this->serieNotadebitoFactura = $serieNotadebitoFactura;

        return $this;
    }

    /**
     * Get serieNotadebitoFactura
     *
     * @return string
     */
    public function getSerieNotadebitoFactura()
    {
        return $this->serieNotadebitoFactura;
    }

    /**
     * Set serieNotadebitoBoleta
     *
     * @param string $serieNotadebitoBoleta
     *
     * @return EmpresaLocal
     */
    public function setSerieNotadebitoBoleta($serieNotadebitoBoleta)
    {
        $this->serieNotadebitoBoleta = $serieNotadebitoBoleta;

        return $this;
    }

    /**
     * Get serieNotadebitoBoleta
     *
     * @return string
     */
    public function getSerieNotadebitoBoleta()
    {
        return $this->serieNotadebitoBoleta;
    }
}
