<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PedidoVenta
 *
 * @ORM\Table(name="pedido_venta")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PedidoVentaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PedidoVenta
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
     * @ORM\Column(name="numero_pedido", type="string", length=32, nullable=true)
     */
    private $numeroPedido;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var bool
     *
     * @ORM\Column(name="despachado", type="boolean", nullable=true)
     */
    private $despachado;

    /**
     * @var string
     *
     * @ORM\Column(name="terminos_pago", type="string", length=100, nullable=true)
     */
    private $terminosPago;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="float", nullable=true)
     */
    private $descuento;

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float", nullable=true)
     */
    private $monto;

    /**
     * @var float
     *
     * @ORM\Column(name="monto_a_cuenta", type="float", nullable=true)
     */
    private $montoACuenta;

    /**
     * @var float
     *
     * @ORM\Column(name="impuesto", type="float", nullable=true)
     */
    private $impuesto;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=32,nullable=true)
     * 
     */
    private $documento;    

    /**
     * @ORM\ManyToOne(targetEntity="EmpresaLocal", inversedBy="pedidoVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="empresa_local_id", referencedColumnName="id")
     * 
     */
    protected $local;

    /**
     * @ORM\ManyToOne(targetEntity="Caja", inversedBy="pedidoVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="caja_id", referencedColumnName="id")
     * 
     */
    protected $caja;

    /**
     * @ORM\OneToMany(targetEntity="PedidoVentaDetalle", mappedBy="pedidoVenta" , cascade={"remove","persist"})
     */
    protected $pedidoVentaDetalle;

    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="pedidoVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * 
     */
    protected $cliente;

    /**
     * @ORM\ManyToOne(targetEntity="Moneda", inversedBy="pedidoVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="moneda_id", referencedColumnName="id")
     * 
     */
    protected $moneda;

    /**
     * @ORM\ManyToOne(targetEntity="FormaPago", inversedBy="pedidoVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="forma_pago_id", referencedColumnName="id")
     * 
     */
    protected $formaPago;
    
    /**
     * @ORM\OneToMany(targetEntity="Transferencia", mappedBy="pedidoVenta" , cascade={"remove","persist"})
     */
    protected $transferencia;

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
     * @ORM\Column(name="tipo_venta", type="string", length=32,nullable=true)
     * 
     */
    private $tipoVenta;

    /**
     * @var int
     *
     * @ORM\Column(name="dias_credito", type="integer", nullable=true)
     */
    private $diasCredito;

    /**
     * @var string
     *
     * @ORM\Column(name="orden_compra", type="string", length=100,nullable=true)
     * 
     */
    private $ordenCompra;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimiento", type="datetime", nullable=true)
     */
    private $fechaVencimiento;
     
    /**
     * @var bool
     *
     * @ORM\Column(name="sin_igv", type="boolean", nullable=true)
     */
    private $sinIgv;

    /**
     * @ORM\ManyToOne(targetEntity="FacturaVenta", inversedBy="pedidoVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="factura_venta_id", referencedColumnName="id")
     * 
     */
    protected $facturaVenta;

    /**
     * @var float
     *
     * @ORM\Column(name="total_onerosa", type="float", nullable=true)
     */
    private $totalOnerosa;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255,nullable=true)
     * 
     */
    private $observacion;
    
    /**
     * @var float
     *
     * @ORM\Column(name="total_gratuita", type="float", nullable=true)
     */
    private $totalGratuita;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="pedidoVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_creacion", referencedColumnName="id")
     */
    private $usuarioCreacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="pedidoVenta", cascade={"persist"})
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
        $this->pedidoVentaDetalle = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set numeroPedido
     *
     * @param string $numeroPedido
     *
     * @return PedidoVenta
     */
    public function setNumeroPedido($numeroPedido)
    {
        $this->numeroPedido = $numeroPedido;

        return $this;
    }

    /**
     * Get numeroPedido
     *
     * @return string
     */
    public function getNumeroPedido()
    {
        return $this->numeroPedido;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return PedidoVenta
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
     * Set estado
     *
     * @param boolean $estado
     *
     * @return PedidoVenta
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
     * Set terminosPago
     *
     * @param string $terminosPago
     *
     * @return PedidoVenta
     */
    public function setTerminosPago($terminosPago)
    {
        $this->terminosPago = $terminosPago;

        return $this;
    }

    /**
     * Get terminosPago
     *
     * @return string
     */
    public function getTerminosPago()
    {
        return $this->terminosPago;
    }

    /**
     * Set descuento
     *
     * @param float $descuento
     *
     * @return PedidoVenta
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return float
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set monto
     *
     * @param float $monto
     *
     * @return PedidoVenta
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return float
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set impuesto
     *
     * @param float $impuesto
     *
     * @return PedidoVenta
     */
    public function setImpuesto($impuesto)
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    /**
     * Get impuesto
     *
     * @return float
     */
    public function getImpuesto()
    {
        return $this->impuesto;
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
     * @return PedidoVenta
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
     * @return PedidoVenta
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
     * Add pedidoVentaDetalle
     *
     * @param \AppBundle\Entity\PedidoVentaDetalle $pedidoVentaDetalle
     *
     * @return PedidoVenta
     */
    public function addPedidoVentaDetalle(\AppBundle\Entity\PedidoVentaDetalle $pedidoVentaDetalle)
    {
        //$this->pedidoVentaDetalle[] = $pedidoVentaDetalle;
        //return $this;

        // for a many-to-one association:
        $pedidoVentaDetalle->setPedidoVenta($this);
        $this->pedidoVentaDetalle->add($pedidoVentaDetalle);            
    }

    /**
     * Remove pedidoVentaDetalle
     *
     * @param \AppBundle\Entity\PedidoVentaDetalle $pedidoVentaDetalle
     */
    public function removePedidoVentaDetalle(\AppBundle\Entity\PedidoVentaDetalle $pedidoVentaDetalle)
    {
        $this->pedidoVentaDetalle->removeElement($pedidoVentaDetalle);
    }

    /**
     * Get pedidoVentaDetalle
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPedidoVentaDetalle()
    {
        return $this->pedidoVentaDetalle;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return PedidoVenta
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
     * Set moneda
     *
     * @param \AppBundle\Entity\Moneda $moneda
     *
     * @return PedidoVenta
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
     * Set formaPago
     *
     * @param \AppBundle\Entity\FormaPago $formaPago
     *
     * @return PedidoVenta
     */
    public function setFormaPago(\AppBundle\Entity\FormaPago $formaPago = null)
    {
        $this->formaPago = $formaPago;

        return $this;
    }

    /**
     * Get formaPago
     *
     * @return \AppBundle\Entity\FormaPago
     */
    public function getFormaPago()
    {
        return $this->formaPago;
    }

    /**
     * Set documento
     *
     * @param string $documento
     *
     * @return PedidoVenta
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;

        return $this;
    }

    /**
     * Get documento
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Set despachado
     *
     * @param boolean $despachado
     *
     * @return PedidoVenta
     */
    public function setDespachado($despachado)
    {
        $this->despachado = $despachado;

        return $this;
    }

    /**
     * Get despachado
     *
     * @return boolean
     */
    public function getDespachado()
    {
        return $this->despachado;
    }

    /**
     * Add transferencia
     *
     * @param \AppBundle\Entity\Transferencia $transferencia
     *
     * @return PedidoVenta
     */
    public function addTransferencia(\AppBundle\Entity\Transferencia $transferencia)
    {
        $this->transferencia[] = $transferencia;

        return $this;
    }

    /**
     * Remove transferencia
     *
     * @param \AppBundle\Entity\Transferencia $transferencia
     */
    public function removeTransferencia(\AppBundle\Entity\Transferencia $transferencia)
    {
        $this->transferencia->removeElement($transferencia);
    }

    /**
     * Get transferencia
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransferencia()
    {
        return $this->transferencia;
    }

    /**
     * Set montoACuenta
     *
     * @param float $montoACuenta
     *
     * @return PedidoVenta
     */
    public function setMontoACuenta($montoACuenta)
    {
        $this->montoACuenta = $montoACuenta;

        return $this;
    }

    /**
     * Get montoACuenta
     *
     * @return float
     */
    public function getMontoACuenta()
    {
        return $this->montoACuenta;
    }


    /**
     * Set valorTipoCambio
     *
     * @param float $valorTipoCambio
     *
     * @return PedidoVenta
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
     * Add transferencium
     *
     * @param \AppBundle\Entity\Transferencia $transferencium
     *
     * @return PedidoVenta
     */
    public function addTransferencium(\AppBundle\Entity\Transferencia $transferencium)
    {
        $this->transferencia[] = $transferencium;

        return $this;
    }

    /**
     * Remove transferencium
     *
     * @param \AppBundle\Entity\Transferencia $transferencium
     */
    public function removeTransferencium(\AppBundle\Entity\Transferencia $transferencium)
    {
        $this->transferencia->removeElement($transferencium);
    }

    /**
     * Set numeroGuiaRemision
     *
     * @param string $numeroGuiaRemision
     *
     * @return PedidoVenta
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
     * Set tipoVenta
     *
     * @param string $tipoVenta
     *
     * @return PedidoVenta
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
     * Set diasCredito
     *
     * @param integer $diasCredito
     *
     * @return PedidoVenta
     */
    public function setDiasCredito($diasCredito)
    {
        $this->diasCredito = $diasCredito;

        return $this;
    }

    /**
     * Get diasCredito
     *
     * @return integer
     */
    public function getDiasCredito()
    {
        return $this->diasCredito;
    }

    /**
     * Set ordenCompra
     *
     * @param string $ordenCompra
     *
     * @return PedidoVenta
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
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     *
     * @return PedidoVenta
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
     * Set facturaVenta
     *
     * @param \AppBundle\Entity\FacturaVenta $facturaVenta
     *
     * @return PedidoVenta
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
     * Set sinIgv
     *
     * @param boolean $sinIgv
     *
     * @return PedidoVenta
     */
    public function setSinIgv($sinIgv)
    {
        $this->sinIgv = $sinIgv;

        return $this;
    }

    /**
     * Get sinIgv
     *
     * @return boolean
     */
    public function getSinIgv()
    {
        return $this->sinIgv;
    }

    /**
     * Set totalOnerosa
     *
     * @param float $totalOnerosa
     *
     * @return PedidoVenta
     */
    public function setTotalOnerosa($totalOnerosa)
    {
        $this->totalOnerosa = $totalOnerosa;

        return $this;
    }

    /**
     * Get totalOnerosa
     *
     * @return float
     */
    public function getTotalOnerosa()
    {
        return $this->totalOnerosa;
    }

    /**
     * Set totalGratuita
     *
     * @param float $totalGratuita
     *
     * @return PedidoVenta
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
     * Set observacion
     *
     * @param string $observacion
     *
     * @return PedidoVenta
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
}
