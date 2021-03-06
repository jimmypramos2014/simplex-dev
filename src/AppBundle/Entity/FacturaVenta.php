<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * FacturaVenta
 *
 * @ORM\Table(name="factura_venta")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FacturaVentaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class FacturaVenta
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity="Venta", inversedBy="facturaVenta", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="venta_id", referencedColumnName="id")
     * 
     */
    protected $venta;

    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="facturaVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id")
     * 
     */
    protected $cliente;

    /**
     * @var string
     *
     * @ORM\Column(name="ticket", type="string", length=32,nullable=true)
     * 
     */
    private $ticket;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=32,nullable=true)
     * 
     */
    private $documento;

    /**
     * @var string
     *
     * @ORM\Column(name="cliente_nombre", type="string", length=100,nullable=true)
     * 
     */
    private $clienteNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_guiaremision", type="string", length=32,nullable=true)
     * 
     */
    private $numeroGuiaremision;

    /**
     * @ORM\ManyToOne(targetEntity="EmpresaLocal", inversedBy="facturaVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="empresa_local_id", referencedColumnName="id")
     * 
     */
    protected $local;

    /**
     * @ORM\ManyToOne(targetEntity="Caja", inversedBy="facturaVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="caja_id", referencedColumnName="id")
     * 
     */
    protected $caja;

    /**
     * @ORM\ManyToOne(targetEntity="FacturaVentaTipo", inversedBy="facturaVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="tipo_id", referencedColumnName="id")
     * 
     */
    protected $tipo;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_proforma", type="string", length=32,nullable=true)
     * 
     */
    private $numeroProforma;

    /**
     * @var string
     *
     * @ORM\Column(name="enlacepdf", type="string", length=255,nullable=true)
     * 
     */
    private $enlacepdf;

    /**
     * @var boolean
     *
     * @ORM\Column(name="factura_enviada", type="boolean",nullable=true,options={"default":0, "comment":"Estado del registro"})
     */
    private $facturaEnviada;

    /**
     * @var boolean
     *
     * @ORM\Column(name="detraccion", type="boolean",nullable=true,options={"default":0, "comment":"Indica si la Factura viene con detraccion"})
     */
    private $detraccion;

    /**
     * @var string
     *
     * @ORM\Column(name="validez_oferta", type="string", length=32,nullable=true)
     * 
     */
    private $validezOferta;

    /**
     * @var string
     *
     * @ORM\Column(name="plazo_entrega", type="string", length=32,nullable=true)
     * 
     */
    private $plazoEntrega;

    /**
     * @var string
     *
     * @ORM\Column(name="empleado_cotiza", type="string", length=100,nullable=true)
     * 
     */
    private $empleadoCotiza;

    /**
     * @var string
     *
     * @ORM\Column(name="correo_empleado_cotiza", type="string", length=100,nullable=true)
     * 
     */
    private $correoEmpleadoCotiza;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono_cliente", type="string", length=100,nullable=true)
     * 
     */
    private $telefonoCliente;

    /**
     * @var boolean
     *
     * @ORM\Column(name="incluir_igv", type="boolean", options={"default":1, "comment":"Incluye IGV en la proforma"})
     */
    private $incluirIgv;


    /**
     * @var boolean
     *
     * @ORM\Column(name="enviado_sunat", type="boolean", options={"default":1, "comment":"Fue enviado a la SUNAT"})
     */
    private $enviadoSunat;

     /**
     * @ORM\OneToMany(targetEntity="LogModificacion", mappedBy="facturaVenta" , cascade={"remove","persist"})
     */
    protected $logModificacion;

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
     * @ORM\Column(name="codigo_error", type="string", length=32,nullable=true)
     * 
     */
    private $codigoError;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje_error", type="string", length=1000,nullable=true)
     * 
     */
    private $mensajeError;
        
     /**
     * @ORM\OneToMany(targetEntity="Transferencia", mappedBy="facturaVenta" , cascade={"remove"})
     */
    protected $transferencia;

    /**
     * @var string
     *
     * @ORM\Column(name="enlace_pdf_ferretero", type="string", length=255,nullable=true)
     * 
     */
    private $enlacePdfFerretero;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="emision_electronica", type="boolean", nullable=true)
     */
    private $emisionElectronica;
       
    /**
     * @var boolean
     *
     * @ORM\Column(name="es_gratuita", type="boolean", nullable=true)
     */
    private $esGratuita;
  
    /**
     * @var string
     *
     * @ORM\Column(name="tipo_venta", type="string", length=32,nullable=true)
     * 
     */
    private $tipoVenta;

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
     * @ORM\Column(name="observacion", type="string", length=255,nullable=true)
     * 
     */
    private $observacion;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimiento", type="datetime", nullable=true)
     */
    private $fechaVencimiento;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="facturaVenta", cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_creacion", referencedColumnName="id")
     */
    private $usuarioCreacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="facturaVenta", cascade={"persist"})
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


    public function __toString()
    {
        return $this->getTicket();
    }

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

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return FacturaVenta
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
     * Set venta
     *
     * @param \AppBundle\Entity\Venta $venta
     *
     * @return FacturaVenta
     */
    public function setVenta(\AppBundle\Entity\Venta $venta = null)
    {
        $this->venta = $venta;

        return $this;
    }

    /**
     * Get venta
     *
     * @return \AppBundle\Entity\Venta
     */
    public function getVenta()
    {
        return $this->venta;
    }

    /**
     * Set cliente
     *
     * @param \AppBundle\Entity\Cliente $cliente
     *
     * @return FacturaVenta
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
     * Set ticket
     *
     * @param string $ticket
     *
     * @return FacturaVenta
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * Get ticket
     *
     * @return string
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * Set documento
     *
     * @param string $documento
     *
     * @return FacturaVenta
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
     * Set clienteNombre
     *
     * @param string $clienteNombre
     *
     * @return FacturaVenta
     */
    public function setClienteNombre($clienteNombre)
    {
        $this->clienteNombre = $clienteNombre;

        return $this;
    }

    /**
     * Get clienteNombre
     *
     * @return string
     */
    public function getClienteNombre()
    {
        return $this->clienteNombre;
    }

    /**
     * Set numeroGuiaremision
     *
     * @param string $numeroGuiaremision
     *
     * @return FacturaVenta
     */
    public function setNumeroGuiaremision($numeroGuiaremision)
    {
        $this->numeroGuiaremision = $numeroGuiaremision;

        return $this;
    }

    /**
     * Get numeroGuiaremision
     *
     * @return string
     */
    public function getNumeroGuiaremision()
    {
        return $this->numeroGuiaremision;
    }

    /**
     * Set local
     *
     * @param \AppBundle\Entity\EmpresaLocal $local
     *
     * @return FacturaVenta
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
     * @return FacturaVenta
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
     * Set tipo
     *
     * @param \AppBundle\Entity\FacturaVentaTipo $tipo
     *
     * @return FacturaVenta
     */
    public function setTipo(\AppBundle\Entity\FacturaVentaTipo $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \AppBundle\Entity\FacturaVentaTipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set numeroProforma
     *
     * @param string $numeroProforma
     *
     * @return FacturaVenta
     */
    public function setNumeroProforma($numeroProforma)
    {
        $this->numeroProforma = $numeroProforma;

        return $this;
    }

    /**
     * Get numeroProforma
     *
     * @return string
     */
    public function getNumeroProforma()
    {
        return $this->numeroProforma;
    }

    /**
     * Set enlacepdf
     *
     * @param string $enlacepdf
     *
     * @return FacturaVenta
     */
    public function setEnlacepdf($enlacepdf)
    {
        $this->enlacepdf = $enlacepdf;

        return $this;
    }

    /**
     * Get enlacepdf
     *
     * @return string
     */
    public function getEnlacepdf()
    {
        return $this->enlacepdf;
    }

    /**
     * Set facturaEnviada
     *
     * @param boolean $facturaEnviada
     *
     * @return FacturaVenta
     */
    public function setFacturaEnviada($facturaEnviada)
    {
        $this->facturaEnviada = $facturaEnviada;

        return $this;
    }

    /**
     * Get facturaEnviada
     *
     * @return boolean
     */
    public function getFacturaEnviada()
    {
        return $this->facturaEnviada;
    }

    /**
     * Set detraccion
     *
     * @param boolean $detraccion
     *
     * @return FacturaVenta
     */
    public function setDetraccion($detraccion)
    {
        $this->detraccion = $detraccion;

        return $this;
    }

    /**
     * Get detraccion
     *
     * @return boolean
     */
    public function getDetraccion()
    {
        return $this->detraccion;
    }

    /**
     * Set validezOferta
     *
     * @param string $validezOferta
     *
     * @return FacturaVenta
     */
    public function setValidezOferta($validezOferta)
    {
        $this->validezOferta = $validezOferta;

        return $this;
    }

    /**
     * Get validezOferta
     *
     * @return string
     */
    public function getValidezOferta()
    {
        return $this->validezOferta;
    }

    /**
     * Set plazoEntrega
     *
     * @param string $plazoEntrega
     *
     * @return FacturaVenta
     */
    public function setPlazoEntrega($plazoEntrega)
    {
        $this->plazoEntrega = $plazoEntrega;

        return $this;
    }

    /**
     * Get plazoEntrega
     *
     * @return string
     */
    public function getPlazoEntrega()
    {
        return $this->plazoEntrega;
    }

    /**
     * Set empleadoCotiza
     *
     * @param string $empleadoCotiza
     *
     * @return FacturaVenta
     */
    public function setEmpleadoCotiza($empleadoCotiza)
    {
        $this->empleadoCotiza = $empleadoCotiza;

        return $this;
    }

    /**
     * Get empleadoCotiza
     *
     * @return string
     */
    public function getEmpleadoCotiza()
    {
        return $this->empleadoCotiza;
    }

    /**
     * Set correoEmpleadoCotiza
     *
     * @param string $correoEmpleadoCotiza
     *
     * @return FacturaVenta
     */
    public function setCorreoEmpleadoCotiza($correoEmpleadoCotiza)
    {
        $this->correoEmpleadoCotiza = $correoEmpleadoCotiza;

        return $this;
    }

    /**
     * Get correoEmpleadoCotiza
     *
     * @return string
     */
    public function getCorreoEmpleadoCotiza()
    {
        return $this->correoEmpleadoCotiza;
    }

    /**
     * Set telefonoCliente
     *
     * @param string $telefonoCliente
     *
     * @return FacturaVenta
     */
    public function setTelefonoCliente($telefonoCliente)
    {
        $this->telefonoCliente = $telefonoCliente;

        return $this;
    }

    /**
     * Get telefonoCliente
     *
     * @return string
     */
    public function getTelefonoCliente()
    {
        return $this->telefonoCliente;
    }

    /**
     * Set incluirIgv
     *
     * @param boolean $incluirIgv
     *
     * @return FacturaVenta
     */
    public function setIncluirIgv($incluirIgv)
    {
        $this->incluirIgv = $incluirIgv;

        return $this;
    }

    /**
     * Get incluirIgv
     *
     * @return boolean
     */
    public function getIncluirIgv()
    {
        return $this->incluirIgv;
    }

    /**
     * Set enviadoSunat
     *
     * @param boolean $enviadoSunat
     *
     * @return FacturaVenta
     */
    public function setEnviadoSunat($enviadoSunat)
    {
        $this->enviadoSunat = $enviadoSunat;

        return $this;
    }

    /**
     * Get enviadoSunat
     *
     * @return boolean
     */
    public function getEnviadoSunat()
    {
        return $this->enviadoSunat;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logModificacion = new \Doctrine\Common\Collections\ArrayCollection();
        $this->transferencia = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add logModificacion
     *
     * @param \AppBundle\Entity\LogModificacion $logModificacion
     *
     * @return FacturaVenta
     */
    public function addLogModificacion(\AppBundle\Entity\LogModificacion $logModificacion)
    {
        $this->logModificacion[] = $logModificacion;

        return $this;
    }

    /**
     * Remove logModificacion
     *
     * @param \AppBundle\Entity\LogModificacion $logModificacion
     */
    public function removeLogModificacion(\AppBundle\Entity\LogModificacion $logModificacion)
    {
        $this->logModificacion->removeElement($logModificacion);
    }

    /**
     * Get logModificacion
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogModificacion()
    {
        return $this->logModificacion;
    }

    /**
     * Set enlaceXml
     *
     * @param string $enlaceXml
     *
     * @return FacturaVenta
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
     * @return FacturaVenta
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
     * Set enlacePdfFerretero
     *
     * @param string $enlacePdfFerretero
     *
     * @return FacturaVenta
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
     * Add transferencium
     *
     * @param \AppBundle\Entity\Transferencia $transferencium
     *
     * @return FacturaVenta
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
     * Get transferencia
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransferencia()
    {
        return $this->transferencia;
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
     * Set emisionElectronica
     *
     * @param boolean $emisionElectronica
     *
     * @return FacturaVenta
     */
    public function setEmisionElectronica($emisionElectronica)
    {
        $this->emisionElectronica = $emisionElectronica;

        return $this;
    }

    /**
     * Get emisionElectronica
     *
     * @return boolean
     */
    public function getEmisionElectronica()
    {
        return $this->emisionElectronica;
    }

    /**
     * Set codigoError
     *
     * @param string $codigoError
     *
     * @return FacturaVenta
     */
    public function setCodigoError($codigoError)
    {
        $this->codigoError = $codigoError;

        return $this;
    }

    /**
     * Get codigoError
     *
     * @return string
     */
    public function getCodigoError()
    {
        return $this->codigoError;
    }

    /**
     * Set mensajeError
     *
     * @param string $mensajeError
     *
     * @return FacturaVenta
     */
    public function setMensajeError($mensajeError)
    {
        $this->mensajeError = $mensajeError;

        return $this;
    }

    /**
     * Get mensajeError
     *
     * @return string
     */
    public function getMensajeError()
    {
        return $this->mensajeError;
    }

    /**
     * Set esGratuita
     *
     * @param boolean $esGratuita
     *
     * @return FacturaVenta
     */
    public function setEsGratuita($esGratuita)
    {
        $this->esGratuita = $esGratuita;

        return $this;
    }

    /**
     * Get esGratuita
     *
     * @return boolean
     */
    public function getEsGratuita()
    {
        return $this->esGratuita;
    }

    /**
     * Set tipoVenta
     *
     * @param string $tipoVenta
     *
     * @return FacturaVenta
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
     * Set ordenCompra
     *
     * @param string $ordenCompra
     *
     * @return FacturaVenta
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
     * @return FacturaVenta
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
     * Set observacion
     *
     * @param string $observacion
     *
     * @return FacturaVenta
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
