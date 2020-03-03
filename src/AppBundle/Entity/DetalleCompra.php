<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleCompra
 *
 * @ORM\Table(name="detalle_compra")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DetalleCompraRepository")
 */
class DetalleCompra
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
     * @ORM\Column(name="subtotal", type="float", nullable=true)
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="precio", type="float", nullable=true)
     */
    private $precio;

    /**
     * @ORM\ManyToOne(targetEntity="Compra", inversedBy="detalleCompra", cascade={"persist"})
     * @ORM\JoinColumn(name="compra_id", referencedColumnName="id")
     * 
     */
    protected $compra;

    /**
     * @ORM\ManyToOne(targetEntity="ProductoXLocal", inversedBy="detalleCompra", cascade={"persist"})
     * @ORM\JoinColumn(name="producto_x_local_id", referencedColumnName="id")
     * 
     */
    protected $productoXLocal;

    /**
     * @ORM\ManyToOne(targetEntity="Proveedor", inversedBy="detalleCompra", cascade={"persist"})
     * @ORM\JoinColumn(name="proveedor_id", referencedColumnName="id")
     * 
     */
    protected $proveedor;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo_anulacion", type="string", length=32,nullable=true)
     * 
     */
    private $tipoAnulacion;
    
    public function __toString()
    {
        return 'Numero : '.$this->getId();
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
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return DetalleCompra
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
     * Set subtotal
     *
     * @param float $subtotal
     *
     * @return DetalleCompra
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
     * Set compra
     *
     * @param \AppBundle\Entity\Compra $compra
     *
     * @return DetalleCompra
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
     * @return DetalleCompra
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
     * Set proveedor
     *
     * @param \AppBundle\Entity\Proveedor $proveedor
     *
     * @return DetalleCompra
     */
    public function setProveedor(\AppBundle\Entity\Proveedor $proveedor = null)
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    /**
     * Get proveedor
     *
     * @return \AppBundle\Entity\Proveedor
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set precio
     *
     * @param float $precio
     *
     * @return DetalleCompra
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
     * Set estado
     *
     * @param boolean $estado
     *
     * @return DetalleCompra
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
     * Set tipoAnulacion
     *
     * @param string $tipoAnulacion
     *
     * @return DetalleCompra
     */
    public function setTipoAnulacion($tipoAnulacion)
    {
        $this->tipoAnulacion = $tipoAnulacion;

        return $this;
    }

    /**
     * Get tipoAnulacion
     *
     * @return string
     */
    public function getTipoAnulacion()
    {
        return $this->tipoAnulacion;
    }
}
