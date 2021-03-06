<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SunatF121
 *
 * @ORM\Table(name="sunat_f121")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SunatF121Repository")
 */
class SunatF121
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
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="serie", type="string", length=32, nullable=true)
     */
    private $serie;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=32, nullable=true)
     */
    private $numero;

    /**
     * @var float
     *
     * @ORM\Column(name="entrada", type="float", nullable=true)
     */
    private $entrada;

    /**
     * @var float
     *
     * @ORM\Column(name="salida", type="float", nullable=true)
     */
    private $salida;

    /**
     * @ORM\ManyToOne(targetEntity="SunatT10", inversedBy="sunatf121", cascade={"persist"})
     * @ORM\JoinColumn(name="tipo", referencedColumnName="id")
     * 
     */
    protected $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="SunatT12", inversedBy="sunatf121", cascade={"persist"})
     * @ORM\JoinColumn(name="operacion", referencedColumnName="id")
     * 
     */
    protected $operacion;

    /**
     * @ORM\ManyToOne(targetEntity="ProductoXLocal", inversedBy="sunatf121", cascade={"persist"})
     * @ORM\JoinColumn(name="producto_x_local_id", referencedColumnName="id")
     * 
     */
    protected $productoXLocal;
 

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return SunatF121
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
     * Set serie
     *
     * @param string $serie
     *
     * @return SunatF121
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
     * Set numero
     *
     * @param string $numero
     *
     * @return SunatF121
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
     * Set entrada
     *
     * @param float $entrada
     *
     * @return SunatF121
     */
    public function setEntrada($entrada)
    {
        $this->entrada = $entrada;

        return $this;
    }

    /**
     * Get entrada
     *
     * @return float
     */
    public function getEntrada()
    {
        return $this->entrada;
    }

    /**
     * Set salida
     *
     * @param float $salida
     *
     * @return SunatF121
     */
    public function setSalida($salida)
    {
        $this->salida = $salida;

        return $this;
    }

    /**
     * Get salida
     *
     * @return float
     */
    public function getSalida()
    {
        return $this->salida;
    }

    /**
     * Set tipo
     *
     * @param \AppBundle\Entity\SunatT10 $tipo
     *
     * @return SunatF121
     */
    public function setTipo(\AppBundle\Entity\SunatT10 $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return \AppBundle\Entity\SunatT10
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set operacion
     *
     * @param \AppBundle\Entity\SunatT12 $operacion
     *
     * @return SunatF121
     */
    public function setOperacion(\AppBundle\Entity\SunatT12 $operacion = null)
    {
        $this->operacion = $operacion;

        return $this;
    }

    /**
     * Get operacion
     *
     * @return \AppBundle\Entity\SunatT12
     */
    public function getOperacion()
    {
        return $this->operacion;
    }

    /**
     * Set productoXLocal
     *
     * @param \AppBundle\Entity\ProductoXLocal $productoXLocal
     *
     * @return SunatF121
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
}
