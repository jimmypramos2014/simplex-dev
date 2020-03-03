<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaDebito
 *
 * @ORM\Table(name="nota_debito")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotaDebitoRepository")
 */
class NotaDebito
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
     * @ORM\Column(name="numero", type="string", length=32, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="serie", type="string", length=4, nullable=true)
     */
    private $serie;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimiento", type="datetime", nullable=true)
     */
    private $fechaVencimiento;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_venta", type="string", length=32, nullable=true)
     */
    private $tipoVenta;

    /**
     * @var string
     *
     * @ORM\Column(name="documento_modifica", type="string", length=32,nullable=true)
     * 
     */
    private $documentoModifica;    

    /**
     * @ORM\ManyToOne(targetEntity="EmpresaLocal", inversedBy="notaDebito", cascade={"persist"})
     * @ORM\JoinColumn(name="empresa_local_id", referencedColumnName="id")
     * 
     */
    protected $local;

    /**
     * @ORM\ManyToOne(targetEntity="Caja", inversedBy="notaDebito", cascade={"persist"})
     * @ORM\JoinColumn(name="caja_id", referencedColumnName="id")
     * 
     */
    protected $caja;

    /**
     * @ORM\OneToMany(targetEntity="NotaDebitoDetalle", mappedBy="notaDebito" , cascade={"remove","persist"})
     */
    protected $notaDebitoDetalle;

    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="notaDebito", cascade={"persist"})
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * 
     */
    protected $cliente;

    /**
     * @ORM\ManyToOne(targetEntity="Moneda", inversedBy="notaDebito", cascade={"persist"})
     * @ORM\JoinColumn(name="moneda_id", referencedColumnName="id")
     * 
     */
    protected $moneda;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_tipo_cambio", type="float", nullable=true)
     */
    private $valorTipoCambio;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numero_guia_remision", type="string", length=100, nullable=true)
     */
    private $numeroGuiaRemision;

    /**
     * @var string
     *
     * @ORM\Column(name="orden_compra", type="string", length=100,nullable=true)
     * 
     */
    private $ordenCompra;
  
    /**
     * @var string
     *
     * @ORM\Column(name="placa_vehiculo", type="string", length=32,nullable=true)
     * 
     */
    private $placaVehiculo;

    /**
     * @var string
     *
     * @ORM\Column(name="condicion_pago", type="string", length=100,nullable=true)
     * 
     */
    private $condicionPago;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255,nullable=true)
     * 
     */
    private $observacion;

     
    /**
     * @ORM\ManyToOne(targetEntity="FacturaVenta", inversedBy="notaDebito", cascade={"persist"})
     * @ORM\JoinColumn(name="factura_venta_id", referencedColumnName="id")
     * 
     */
    protected $facturaVenta;

    /**
     * @ORM\ManyToOne(targetEntity="NotaDebitoTipo", inversedBy="notaDebito", cascade={"persist"})
     * @ORM\JoinColumn(name="nota_debito_tipo_id", referencedColumnName="id")
     * 
     */
    protected $notaDebitoTipo;


    /**
     * @var float
     *
     * @ORM\Column(name="total", type="float", nullable=true)
     */
    private $total;

    /**
     * @var float
     *
     * @ORM\Column(name="total_gravada", type="float", nullable=true)
     */
    private $totalGravada;

    /**
     * @var float
     *
     * @ORM\Column(name="total_exonerada", type="float", nullable=true)
     */
    private $totalExonerada;

    /**
     * @var float
     *
     * @ORM\Column(name="total_gratuita", type="float", nullable=true)
     */
    private $totalGratuita;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento_global", type="float", nullable=true)
     */
    private $descuentoGlobal;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento_x_item", type="float", nullable=true)
     */
    private $descuentoXItem;

    /**
     * @var string
     *
     * @ORM\Column(name="enlace_xml", type="string", length=255,nullable=true)
     * 
     */
    private $enlaceXml;

    /**
     * @var string
     *
     * @ORM\Column(name="enlace_cdr", type="string", length=255,nullable=true)
     * 
     */
    private $enlaceCdr;

     /**
     * @var string
     *
     * @ORM\Column(name="enlace_pdf", type="string", length=255,nullable=true)
     * 
     */
    private $enlacePdf;

     /**
     * @var string
     *
     * @ORM\Column(name="enlace_pdf_ferretero", type="string", length=255,nullable=true)
     * 
     */
    private $enlacePdfFerretero;

     /**
     * @var string
     *
     * @ORM\Column(name="motivo_anulacion", type="string", length=255,nullable=true)
     * 
     */
    private $motivoAnulacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="notaDebito", cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_creacion", referencedColumnName="id")
     */
    private $usuarioCreacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="notaDebito", cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_modificacion", referencedColumnName="id")
     */
    private $usuarioModificacion;

    /** 
     * created Time/Date 
     * 
     * @var \DateTime 
     * 
     * @ORM\Column(name="fecha_creacion", type="datetime", nullable=true) 
     */  
    protected $fechaCreacion;  
  
    /** 
     * updated Time/Date 
     * 
     * @var \DateTime 
     * 
     * @ORM\Column(name="fecha_modificacion", type="datetime", nullable=true) 
     */  
    protected $fechaModificacion;  

    /**
     * @ORM\PrePersist
     */
    public function setUsuarioCreacion()
    {
        $usuario = $GLOBALS['kernel']->getContainer()->get('security.token_storage')->getToken()->getUser();
        $this->usuarioCreacion = $usuario;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUsuarioModificacion()
    {
        $usuario = $GLOBALS['kernel']->getContainer()->get('security.token_storage')->getToken()->getUser();
        $this->usuarioModificacion = $usuario;

    }

    /** 
     * Set FechaCreacion 
     * 
     * @ORM\PrePersist 
     */  
    public function setFechaCreacion()  
    {  
        $this->fechaCreacion = new \DateTime();  
        $this->fechaModificacion = new \DateTime();  
    }  
  
    /** 
     * Set FechaModificacion 
     * 
     * @ORM\PreUpdate 
     */  
    public function setFechaModificacion()  
    {  
        $this->fechaModificacion = new \DateTime();  
    }

    public function __toString()
    {
        return '';
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notaDebitoDetalle = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set numero
     *
     * @param string $numero
     *
     * @return NotaDebito
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set serie
     *
     * @param string $serie
     *
     * @return NotaDebito
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return string
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return NotaDebito
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     *
     * @return NotaDebito
     */
    public function setFechaVencimiento($fechaVencimiento)
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * Get fechaVencimiento
     *
     * @return \DateTime
     */
    public function getFechaVencimiento()
    {
        return $this->fechaVencimiento;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     *
     * @return NotaDebito
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
     * Set tipoVenta
     *
     * @param string $tipoVenta
     *
     * @return NotaDebito
     */
    public function setTipoVenta($tipoVenta)
    {
        $this->tipoVenta = $tipoVenta;

        return $this;
    }

    /**
     * Get tipoVenta
     *
     * @return string
     */
    public function getTipoVenta()
    {
        return $this->tipoVenta;
    }

    /**
     * Set documentoModifica
     *
     * @param string $documentoModifica
     *
     * @return NotaDebito
     */
    public function setDocumentoModifica($documentoModifica)
    {
        $this->documentoModifica = $documentoModifica;

        return $this;
    }

    /**
     * Get documentoModifica
     *
     * @return string
     */
    public function getDocumentoModifica()
    {
        return $this->documentoModifica;
    }

    /**
     * Set valorTipoCambio
     *
     * @param float $valorTipoCambio
     *
     * @return NotaDebito
     */
    public function setValorTipoCambio($valorTipoCambio)
    {
        $this->valorTipoCambio = $valorTipoCambio;

        return $this;
    }

    /**
     * Get valorTipoCambio
     *
     * @return float
     */
    public function getValorTipoCambio()
    {
        return $this->valorTipoCambio;
    }

    /**
     * Set numeroGuiaRemision
     *
     * @param string $numeroGuiaRemision
     *
     * @return NotaDebito
     */
    public function setNumeroGuiaRemision($numeroGuiaRemision)
    {
        $this->numeroGuiaRemision = $numeroGuiaRemision;

        return $this;
    }

    /**
     * Get numeroGuiaRemision
     *
     * @return string
     */
    public function getNumeroGuiaRemision()
    {
        return $this->numeroGuiaRemision;
    }

    /**
     * Set ordenCompra
     *
     * @param string $ordenCompra
     *
     * @return NotaDebito
     */
    public function setOrdenCompra($ordenCompra)
    {
        $this->ordenCompra = $ordenCompra;

        return $this;
    }

    /**
     * Get ordenCompra
     *
     * @return string
     */
    public function getOrdenCompra()
    {
        return $this->ordenCompra;
    }

    /**
     * Set placaVehiculo
     *
     * @param string $placaVehiculo
     *
     * @return NotaDebito
     */
    public function setPlacaVehiculo($placaVehiculo)
    {
        $this->placaVehiculo = $placaVehiculo;

        return $this;
    }

    /**
     * Get placaVehiculo
     *
     * @return string
     */
    public function getPlacaVehiculo()
    {
        return $this->placaVehiculo;
    }

    /**
     * Set condicionPago
     *
     * @param string $condicionPago
     *
     * @return NotaDebito
     */
    public function setCondicionPago($condicionPago)
    {
        $this->condicionPago = $condicionPago;

        return $this;
    }

    /**
     * Get condicionPago
     *
     * @return string
     */
    public function getCondicionPago()
    {
        return $this->condicionPago;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     *
     * @return NotaDebito
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string
     */
    public function getObservacion()
    {
        return $this->observacion;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return NotaDebito
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set totalGravada
     *
     * @param float $totalGravada
     *
     * @return NotaDebito
     */
    public function setTotalGravada($totalGravada)
    {
        $this->totalGravada = $totalGravada;

        return $this;
    }

    /**
     * Get totalGravada
     *
     * @return float
     */
    public function getTotalGravada()
    {
        return $this->totalGravada;
    }

    /**
     * Set totalExonerada
     *
     * @param float $totalExonerada
     *
     * @return NotaDebito
     */
    public function setTotalExonerada($totalExonerada)
    {
        $this->totalExonerada = $totalExonerada;

        return $this;
    }

    /**
     * Get totalExonerada
     *
     * @return float
     */
    public function getTotalExonerada()
    {
        return $this->totalExonerada;
    }

    /**
     * Set totalGratuita
     *
     * @param float $totalGratuita
     *
     * @return NotaDebito
     */
    public function setTotalGratuita($totalGratuita)
    {
        $this->totalGratuita = $totalGratuita;

        return $this;
    }

    /**
     * Get totalGratuita
     *
     * @return float
     */
    public function getTotalGratuita()
    {
        return $this->totalGratuita;
    }

    /**
     * Set enlaceXml
     *
     * @param string $enlaceXml
     *
     * @return NotaDebito
     */
    public function setEnlaceXml($enlaceXml)
    {
        $this->enlaceXml = $enlaceXml;

        return $this;
    }

    /**
     * Get enlaceXml
     *
     * @return string
     */
    public function getEnlaceXml()
    {
        return $this->enlaceXml;
    }

    /**
     * Set enlaceCdr
     *
     * @param string $enlaceCdr
     *
     * @return NotaDebito
     */
    public function setEnlaceCdr($enlaceCdr)
    {
        $this->enlaceCdr = $enlaceCdr;

        return $this;
    }

    /**
     * Get enlaceCdr
     *
     * @return string
     */
    public function getEnlaceCdr()
    {
        return $this->enlaceCdr;
    }

    /**
     * Set enlacePdf
     *
     * @param string $enlacePdf
     *
     * @return NotaDebito
     */
    public function setEnlacePdf($enlacePdf)
    {
        $this->enlacePdf = $enlacePdf;

        return $this;
    }

    /**
     * Get enlacePdf
     *
     * @return string
     */
    public function getEnlacePdf()
    {
        return $this->enlacePdf;
    }

    /**
     * Set enlacePdfFerretero
     *
     * @param string $enlacePdfFerretero
     *
     * @return NotaDebito
     */
    public function setEnlacePdfFerretero($enlacePdfFerretero)
    {
        $this->enlacePdfFerretero = $enlacePdfFerretero;

        return $this;
    }

    /**
     * Get enlacePdfFerretero
     *
     * @return string
     */
    public function getEnlacePdfFerretero()
    {
        return $this->enlacePdfFerretero;
    }

    /**
     * Set motivoAnulacion
     *
     * @param string $motivoAnulacion
     *
     * @return NotaDebito
     */
    public function setMotivoAnulacion($motivoAnulacion)
    {
        $this->motivoAnulacion = $motivoAnulacion;

        return $this;
    }

    /**
     * Get motivoAnulacion
     *
     * @return string
     */
    public function getMotivoAnulacion()
    {
        return $this->motivoAnulacion;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Get fechaModificacion
     *
     * @return \DateTime
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * Set local
     *
     * @param \AppBundle\Entity\EmpresaLocal $local
     *
     * @return NotaDebito
     */
    public function setLocal(\AppBundle\Entity\EmpresaLocal $local = null)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return \AppBundle\Entity\EmpresaLocal
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set caja
     *
     * @param \AppBundle\Entity\Caja $caja
     *
     * @return NotaDebito
     */
    public function setCaja(\AppBundle\Entity\Caja $caja = null)
    {
        $this->caja = $caja;

        return $this;
    }

    /**
     * Get caja
     *
     * @return \AppBundle\Entity\Caja
     */
    public function getCaja()
    {
        return $this->caja;
    }

    /**
     * Add notaDebitoDetalle
     *
     * @param \AppBundle\Entity\NotaDebitoDetalle $notaDebitoDetalle
     *
     * @return NotaDebito
     */
    public function addNotaDebitoDetalle(\AppBundle\Entity\NotaDebitoDetalle $notaDebitoDetalle)
    {
        //$this->notaDebitoDetalle[] = $notaDebitoDetalle;
        //return $this;

        $notaDebitoDetalle->setNotaDebito($this);
        $this->notaDebitoDetalle->add($notaDebitoDetalle);             
    }

    /**
     * Remove notaDebitoDetalle
     *
     * @param \AppBundle\Entity\NotaDebitoDetalle $notaDebitoDetalle
     */
    public function removeNotaDebitoDetalle(\AppBundle\Entity\NotaDebitoDetalle $notaDebitoDetalle)
    {
        $this->notaDebitoDetalle->removeElement($notaDebitoDetalle);
    }

    /**
     * Get notaDebitoDetalle
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotaDebitoDetalle()
    {
        return $this->notaDebitoDetalle;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return NotaDebito
     */
    public function setCliente(\AppBundle\Entity\Cliente $cliente = null)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \AppBundle\Entity\Cliente
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set moneda
     *
     * @param \AppBundle\Entity\Moneda $moneda
     *
     * @return NotaDebito
     */
    public function setMoneda(\AppBundle\Entity\Moneda $moneda = null)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda
     *
     * @return \AppBundle\Entity\Moneda
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set facturaVenta
     *
     * @param \AppBundle\Entity\FacturaVenta $facturaVenta
     *
     * @return NotaDebito
     */
    public function setFacturaVenta(\AppBundle\Entity\FacturaVenta $facturaVenta = null)
    {
        $this->facturaVenta = $facturaVenta;

        return $this;
    }

    /**
     * Get facturaVenta
     *
     * @return \AppBundle\Entity\FacturaVenta
     */
    public function getFacturaVenta()
    {
        return $this->facturaVenta;
    }

    /**
     * Set notaDebitoTipo
     *
     * @param \AppBundle\Entity\NotaDebitoTipo $notaDebitoTipo
     *
     * @return NotaDebito
     */
    public function setNotaDebitoTipo(\AppBundle\Entity\NotaDebitoTipo $notaDebitoTipo = null)
    {
        $this->notaDebitoTipo = $notaDebitoTipo;

        return $this;
    }

    /**
     * Get notaDebitoTipo
     *
     * @return \AppBundle\Entity\NotaDebitoTipo
     */
    public function getNotaDebitoTipo()
    {
        return $this->notaDebitoTipo;
    }

    /**
     * Get usuarioCreacion
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuarioCreacion()
    {
        return $this->usuarioCreacion;
    }

    /**
     * Get usuarioModificacion
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuarioModificacion()
    {
        return $this->usuarioModificacion;
    }

    /**
     * Set descuentoGlobal
     *
     * @param float $descuentoGlobal
     *
     * @return NotaDebito
     */
    public function setDescuentoGlobal($descuentoGlobal)
    {
        $this->descuentoGlobal = $descuentoGlobal;

        return $this;
    }

    /**
     * Get descuentoGlobal
     *
     * @return float
     */
    public function getDescuentoGlobal()
    {
        return $this->descuentoGlobal;
    }

    /**
     * Set descuentoXItem
     *
     * @param float $descuentoXItem
     *
     * @return NotaDebito
     */
    public function setDescuentoXItem($descuentoXItem)
    {
        $this->descuentoXItem = $descuentoXItem;

        return $this;
    }

    /**
     * Get descuentoXItem
     *
     * @return float
     */
    public function getDescuentoXItem()
    {
        return $this->descuentoXItem;
    }
}
