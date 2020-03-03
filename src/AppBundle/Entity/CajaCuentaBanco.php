<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CajaCuentaBanco
 *
 * @ORM\Table(name="caja_cuenta_banco")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CajaCuentaBancoRepository")
 */
class CajaCuentaBanco
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
     * @var int
     *
     * @ORM\Column(name="identificador", type="integer", nullable=true)
     */
    private $identificador;

    /**
     * @ORM\ManyToOne(targetEntity="CuentaTipo", inversedBy="cajaCuentaBanco", cascade={"persist"})
     * @ORM\JoinColumn(name="cuenta_tipo_id", referencedColumnName="id")
     * 
     */
    protected $cuentaTipo;


    /**
     * @ORM\ManyToOne(targetEntity="Empresa", inversedBy="cajaCuentaBanco", cascade={"persist"})
     * @ORM\JoinColumn(name="empresa_id", referencedColumnName="id")
     * 
     */
    protected $empresa;
    

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float", nullable=true)
     */
    private $monto;


    public function __toString()
    {
        return $this->getCuentaTipo().' - '.$this->getNumero();
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
     * Set numero
     *
     * @param string $numero
     *
     * @return CajaCuentaBanco
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
     * Set identificador
     *
     * @param integer $identificador
     *
     * @return CajaCuentaBanco
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;

        return $this;
    }

    /**
     * Get identificador
     *
     * @return int
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Set cuentaTipo
     *
     * @param \AppBundle\Entity\CuentaTipo $cuentaTipo
     *
     * @return CajaCuentaBanco
     */
    public function setCuentaTipo(\AppBundle\Entity\CuentaTipo $cuentaTipo = null)
    {
        $this->cuentaTipo = $cuentaTipo;

        return $this;
    }

    /**
     * Get cuentaTipo
     *
     * @return \AppBundle\Entity\CuentaTipo
     */
    public function getCuentaTipo()
    {
        return $this->cuentaTipo;
    }

    /**
     * Set empresa
     *
     * @param \AppBundle\Entity\Empresa $empresa
     *
     * @return CajaCuentaBanco
     */
    public function setEmpresa(\AppBundle\Entity\Empresa $empresa = null)
    {
        $this->empresa = $empresa;

        return $this;
    }

    /**
     * Get empresa
     *
     * @return \AppBundle\Entity\Empresa
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * Set monto
     *
     * @param float $monto
     *
     * @return CajaCuentaBanco
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
