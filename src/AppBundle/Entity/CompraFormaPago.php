<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompraFormaPago
 *
 * @ORM\Table(name="compra_forma_pago")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CompraFormaPagoRepository")
 */
class CompraFormaPago
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
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", nullable=true)
     */
    private $cantidad;

    /**
     * @var int
     *
     * @ORM\Column(name="numero_dias", type="integer", nullable=true)
     */
    private $numeroDias;

    /**
     * @ORM\ManyToOne(targetEntity="Moneda", inversedBy="compraFormaPago", cascade={"persist"})
     * @ORM\JoinColumn(name="moneda_id", referencedColumnName="id")
     * 
     */
    protected $moneda;

    /**
     * @ORM\ManyToOne(targetEntity="FormaPago", inversedBy="compraFormaPago", cascade={"persist"})
     * @ORM\JoinColumn(name="forma_pago_id", referencedColumnName="id")
     * 
     */
    protected $formaPago;


    /**
     * @ORM\ManyToOne(targetEntity="Compra", inversedBy="compraFormaPago", cascade={"persist"})
     * @ORM\JoinColumn(name="compra_id", referencedColumnName="id")
     * 
     */
    protected $compra;

    /**
     * @var string
     *
     * @ORM\Column(name="condicion", type="string", length=10, nullable=true)
     */
    private $condicion;


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
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return CompraFormaPago
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
     * Set numeroDias
     *
     * @param integer $numeroDias
     *
     * @return CompraFormaPago
     */
    public function setNumeroDias($numeroDias)
    {
        $this->numeroDias = $numeroDias;

        return $this;
    }

    /**
     * Get numeroDias
     *
     * @return integer
     */
    public function getNumeroDias()
    {
        return $this->numeroDias;
    }

    /**
     * Set moneda
     *
     * @param \AppBundle\Entity\Moneda $moneda
     *
     * @return CompraFormaPago
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
     * @return CompraFormaPago
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
     * Set compra
     *
     * @param \AppBundle\Entity\Compra $compra
     *
     * @return CompraFormaPago
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
     * Set condicion
     *
     * @param string $condicion
     *
     * @return CompraFormaPago
     */
    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;

        return $this;
    }

    /**
     * Get condicion
     *
     * @return string
     */
    public function getCondicion()
    {
        return $this->condicion;
    }
}
