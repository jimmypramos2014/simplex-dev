<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleCompraAnulada
 *
 * @ORM\Table(name="detalle_compra_anulada")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DetalleCompraAnuladaRepository")
 */
class DetalleCompraAnulada
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
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=32, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="Compra", inversedBy="detalleCompraAnulada", cascade={"persist"})
     * @ORM\JoinColumn(name="compra_id", referencedColumnName="id")
     * 
     */
    protected $compra;

    /**
     * @ORM\ManyToOne(targetEntity="ProductoXLocal", inversedBy="detalleCompraAnulada", cascade={"persist"})
     * @ORM\JoinColumn(name="producto_x_local_id", referencedColumnName="id")
     * 
     */
    protected $productoXLocal;
    
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="detalleCompraAnulada", cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * 
     */
    protected $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="DetalleCompra", inversedBy="detalleCompraAnulada", cascade={"persist"})
     * @ORM\JoinColumn(name="detalle_compra_id", referencedColumnName="id")
     * 
     */
    protected $detalleCompra;

    /**
     * @var float
     *
     * @ORM\Column(name="precio", type="float", nullable=true)
     */
    private $precio;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=32, nullable=true)
     */
    private $numero;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_emision", type="datetime", nullable=true)
     */
    private $fechaEmision;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimiento", type="datetime", nullable=true)
     */
    private $fechaVencimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="ruc", type="string", length=11, nullable=true)
     */
    private $ruc;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=100, nullable=true)
     */
    private $denominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo_emision", type="string", length=100, nullable=true)
     */
    private $motivoEmision;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_documento_relacionado", type="string", length=32, nullable=true)
     */
    private $numeroDocumentoRelacionado;

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float", nullable=true)
     */
    private $monto;

    
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
     * @return DetalleCompraAnulada
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
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return DetalleCompraAnulada
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set compra
     *
     * @param \AppBundle\Entity\Compra $compra
     *
     * @return DetalleCompraAnulada
     */
    public function setCompra(\AppBundle\Entity\Compra $compra = null)
    {
        $this->compra = $compra;

        return $this;
    }

    /**
     * Get compra
     *
     * @return \AppBundle\Entity\Compra
     */
    public function getCompra()
    {
        return $this->compra;
    }

    /**
     * Set productoXLocal
     *
     * @param \AppBundle\Entity\ProductoXLocal $productoXLocal
     *
     * @return DetalleCompraAnulada
     */
    public function setProductoXLocal(\AppBundle\Entity\ProductoXLocal $productoXLocal = null)
    {
        $this->productoXLocal = $productoXLocal;

        return $this;
    }

    /**
     * Get productoXLocal
     *
     * @return \AppBundle\Entity\ProductoXLocal
     */
    public function getProductoXLocal()
    {
        return $this->productoXLocal;
    }

    /**
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     *
     * @return DetalleCompraAnulada
     */
    public function setUsuario(\AppBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AppBundle\Entity\Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return DetalleCompraAnulada
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set detalleCompra
     *
     * @param \AppBundle\Entity\DetalleCompra $detalleCompra
     *
     * @return DetalleCompraAnulada
     */
    public function setDetalleCompra(\AppBundle\Entity\DetalleCompra $detalleCompra = null)
    {
        $this->detalleCompra = $detalleCompra;

        return $this;
    }

    /**
     * Get detalleCompra
     *
     * @return \AppBundle\Entity\DetalleCompra
     */
    public function getDetalleCompra()
    {
        return $this->detalleCompra;
    }

    /**
     * Set precio
     *
     * @param float $precio
     *
     * @return DetalleCompraAnulada
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return float
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return DetalleCompraAnulada
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
     * Set fechaEmision
     *
     * @param \DateTime $fechaEmision
     *
     * @return DetalleCompraAnulada
     */
    public function setFechaEmision($fechaEmision)
    {
        $this->fechaEmision = $fechaEmision;

        return $this;
    }

    /**
     * Get fechaEmision
     *
     * @return \DateTime
     */
    public function getFechaEmision()
    {
        return $this->fechaEmision;
    }

    /**
     * Set fechaVencimiento
     *
     * @param \DateTime $fechaVencimiento
     *
     * @return DetalleCompraAnulada
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
     * Set ruc
     *
     * @param string $ruc
     *
     * @return DetalleCompraAnulada
     */
    public function setRuc($ruc)
    {
        $this->ruc = $ruc;

        return $this;
    }

    /**
     * Get ruc
     *
     * @return string
     */
    public function getRuc()
    {
        return $this->ruc;
    }

    /**
     * Set denominacion
     *
     * @param string $denominacion
     *
     * @return DetalleCompraAnulada
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return DetalleCompraAnulada
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
     * Set observacion
     *
     * @param string $observacion
     *
     * @return DetalleCompraAnulada
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
     * Set motivoEmision
     *
     * @param string $motivoEmision
     *
     * @return DetalleCompraAnulada
     */
    public function setMotivoEmision($motivoEmision)
    {
        $this->motivoEmision = $motivoEmision;

        return $this;
    }

    /**
     * Get motivoEmision
     *
     * @return string
     */
    public function getMotivoEmision()
    {
        return $this->motivoEmision;
    }

    /**
     * Set numeroDocumentoRelacionado
     *
     * @param string $numeroDocumentoRelacionado
     *
     * @return DetalleCompraAnulada
     */
    public function setNumeroDocumentoRelacionado($numeroDocumentoRelacionado)
    {
        $this->numeroDocumentoRelacionado = $numeroDocumentoRelacionado;

        return $this;
    }

    /**
     * Get numeroDocumentoRelacionado
     *
     * @return string
     */
    public function getNumeroDocumentoRelacionado()
    {
        return $this->numeroDocumentoRelacionado;
    }

    /**
     * Set monto
     *
     * @param float $monto
     *
     * @return DetalleCompraAnulada
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
}
