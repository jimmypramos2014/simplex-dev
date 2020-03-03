<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Compra
 *
 * @ORM\Table(name="compra_anulada_nota_credito")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompraAnuladaNotaCreditoRepository")
 */
class CompraAnuladaNotaCredito
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
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=32, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="FacturaCompra", inversedBy="compraAnuladaNotaCredito", cascade={"persist"})
     * @ORM\JoinColumn(name="factura_compra_id", referencedColumnName="id")
     * 
     */
    protected $facturaCompra;



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
     * @return CompraAnuladaNotaCredito
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
     * @return CompraAnuladaNotaCredito
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
     * @return CompraAnuladaNotaCredito
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
     * @return CompraAnuladaNotaCredito
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
     * @return CompraAnuladaNotaCredito
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
     * @return CompraAnuladaNotaCredito
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
     * @return CompraAnuladaNotaCredito
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
     * @return CompraAnuladaNotaCredito
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
     * @return CompraAnuladaNotaCredito
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
     * Set facturaCompra
     *
     * @param \AppBundle\Entity\FacturaCompra $facturaCompra
     *
     * @return CompraAnuladaNotaCredito
     */
    public function setFacturaCompra(\AppBundle\Entity\FacturaCompra $facturaCompra = null)
    {
        $this->facturaCompra = $facturaCompra;

        return $this;
    }

    /**
     * Get facturaCompra
     *
     * @return \AppBundle\Entity\FacturaCompra
     */
    public function getFacturaCompra()
    {
        return $this->facturaCompra;
    }

    /**
     * Set monto
     *
     * @param float $monto
     *
     * @return CompraAnuladaNotaCredito
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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return CompraAnuladaNotaCredito
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
}
