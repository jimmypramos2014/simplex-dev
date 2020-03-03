<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaDebitoDetalle
 *
 * @ORM\Table(name="nota_debito_detalle")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NotaDebitoDetalleRepository")
 */
class NotaDebitoDetalle
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
     * @var float
     *
     * @ORM\Column(name="precio", type="float", nullable=true)
     */
    private $precio;

    /**
     * @var float
     *
     * @ORM\Column(name="impuesto", type="float", nullable=true)
     */
    private $impuesto;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", nullable=true)
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="descuento", type="float", nullable=true)
     */
    private $descuento;

    /**
     * @ORM\ManyToOne(targetEntity="NotaDebito", inversedBy="notaDebitoDetalle", cascade={"persist"})
     * @ORM\JoinColumn(name="nota_debito_id", referencedColumnName="id")
     * 
     */
    protected $notaDebito;

    /**
     * @ORM\ManyToOne(targetEntity="ProductoXLocal", inversedBy="notaDebitoDetalle", cascade={"persist"})
     * @ORM\JoinColumn(name="producto_x_local_id", referencedColumnName="id")
     * 
     */
    protected $productoXLocal;
    
    /**
     * @ORM\ManyToOne(targetEntity="TipoImpuesto", inversedBy="notaDebitoDetalle", cascade={"persist"})
     * @ORM\JoinColumn(name="tipo_impuesto_id", referencedColumnName="id")
     * 
     */
    protected $tipoImpuesto;


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
     * @return NotaDebitoDetalle
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
     * Set precio
     *
     * @param float $precio
     *
     * @return NotaDebitoDetalle
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
     * Set impuesto
     *
     * @param float $impuesto
     *
     * @return NotaDebitoDetalle
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
     * Set subtotal
     *
     * @param float $subtotal
     *
     * @return NotaDebitoDetalle
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Set descuento
     *
     * @param float $descuento
     *
     * @return NotaDebitoDetalle
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
     * Set notaDebito
     *
     * @param \AppBundle\Entity\NotaDebito $notaDebito
     *
     * @return NotaDebitoDetalle
     */
    public function setNotaDebito(\AppBundle\Entity\NotaDebito $notaDebito = null)
    {
        $this->notaDebito = $notaDebito;

        return $this;
    }

    /**
     * Get notaDebito
     *
     * @return \AppBundle\Entity\NotaDebito
     */
    public function getNotaDebito()
    {
        return $this->notaDebito;
    }

    /**
     * Set productoXLocal
     *
     * @param \AppBundle\Entity\ProductoXLocal $productoXLocal
     *
     * @return NotaDebitoDetalle
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
     * Set tipoImpuesto
     *
     * @param \AppBundle\Entity\TipoImpuesto $tipoImpuesto
     *
     * @return NotaDebitoDetalle
     */
    public function setTipoImpuesto(\AppBundle\Entity\TipoImpuesto $tipoImpuesto = null)
    {
        $this->tipoImpuesto = $tipoImpuesto;

        return $this;
    }

    /**
     * Get tipoImpuesto
     *
     * @return \AppBundle\Entity\TipoImpuesto
     */
    public function getTipoImpuesto()
    {
        return $this->tipoImpuesto;
    }
}
