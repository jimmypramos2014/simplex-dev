<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransaccionDetalle
 *
 * @ORM\Table(name="transaccion_detalle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransaccionDetalleRepository")
 * 
 */
class TransaccionDetalle
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
     * @ORM\Column(name="tipo_documento", type="string", length=32, nullable=true)
     */
    private $tipoDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_documento", type="string", length=32, nullable=true)
     */
    private $numeroDocumento;

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float", nullable=true)
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="nota_credito", type="string", length=32, nullable=true)
     */
    private $notaCredito;

    /**
     * @ORM\ManyToOne(targetEntity="Transaccion", inversedBy="transaccionDetalle", cascade={"persist"})
     * @ORM\JoinColumn(name="transaccion_id", referencedColumnName="id")
     * 
     */
    protected $transaccion;

    /**
     * @ORM\ManyToOne(targetEntity="CajaCuentaBanco", inversedBy="transaccionDetalle", cascade={"persist"})
     * @ORM\JoinColumn(name="caja_cuentabanco_id", referencedColumnName="id")
     * 
     */
    protected $cajaCuentaBanco;

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
     * Set tipoDocumento
     *
     * @param string $tipoDocumento
     *
     * @return TransaccionDetalle
     */
    public function setTipoDocumento($tipoDocumento)
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }

    /**
     * Get tipoDocumento
     *
     * @return string
     */
    public function getTipoDocumento()
    {
        return $this->tipoDocumento;
    }

    /**
     * Set numeroDocumento
     *
     * @param string $numeroDocumento
     *
     * @return TransaccionDetalle
     */
    public function setNumeroDocumento($numeroDocumento)
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    /**
     * Get numeroDocumento
     *
     * @return string
     */
    public function getNumeroDocumento()
    {
        return $this->numeroDocumento;
    }

    /**
     * Set monto
     *
     * @param float $monto
     *
     * @return TransaccionDetalle
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
     * Set notaCredito
     *
     * @param string $notaCredito
     *
     * @return TransaccionDetalle
     */
    public function setNotaCredito($notaCredito)
    {
        $this->notaCredito = $notaCredito;

        return $this;
    }

    /**
     * Get notaCredito
     *
     * @return string
     */
    public function getNotaCredito()
    {
        return $this->notaCredito;
    }

    /**
     * Set transaccion
     *
     * @param \AppBundle\Entity\Transaccion $transaccion
     *
     * @return TransaccionDetalle
     */
    public function setTransaccion(\AppBundle\Entity\Transaccion $transaccion = null)
    {
        $this->transaccion = $transaccion;

        return $this;
    }

    /**
     * Get transaccion
     *
     * @return \AppBundle\Entity\Transaccion
     */
    public function getTransaccion()
    {
        return $this->transaccion;
    }

    /**
     * Set cajaCuentaBanco
     *
     * @param \AppBundle\Entity\CajaCuentaBanco $cajaCuentaBanco
     *
     * @return TransaccionDetalle
     */
    public function setCajaCuentaBanco(\AppBundle\Entity\CajaCuentaBanco $cajaCuentaBanco = null)
    {
        $this->cajaCuentaBanco = $cajaCuentaBanco;

        return $this;
    }

    /**
     * Get cajaCuentaBanco
     *
     * @return \AppBundle\Entity\CajaCuentaBanco
     */
    public function getCajaCuentaBanco()
    {
        return $this->cajaCuentaBanco;
    }
}
