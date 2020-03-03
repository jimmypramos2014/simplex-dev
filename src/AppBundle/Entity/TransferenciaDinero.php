<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransferenciaDinero
 *
 * @ORM\Table(name="transferencia_dinero")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransferenciaDineroRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TransferenciaDinero
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
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=32, nullable=true)
     */
    private $tipo;

     /**
     * @ORM\OneToMany(targetEntity="TransaccionDetalle", mappedBy="transaccion" , cascade={"persist","remove"})
     */
    protected $transaccionDetalle;

    /**
     * @ORM\ManyToOne(targetEntity="CajaCuentaBanco", inversedBy="transferenciaDinero", cascade={"persist"})
     * @ORM\JoinColumn(name="salida", referencedColumnName="id")
     * 
     */
    protected $salida;

    /**
     * @ORM\ManyToOne(targetEntity="CajaCuentaBanco", inversedBy="transferenciaDinero", cascade={"persist"})
     * @ORM\JoinColumn(name="entrada", referencedColumnName="id")
     * 
     */
    protected $entrada;

    /**
     * @var float
     *
     * @ORM\Column(name="monto", type="float", nullable=true)
     */
    private $monto;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="caja", cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_creacion", referencedColumnName="id")
     */
    private $usuarioCreacion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario", inversedBy="caja", cascade={"persist"})
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
  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transaccionDetalle = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return TransferenciaDinero
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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return TransferenciaDinero
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
     * Set monto
     *
     * @param float $monto
     *
     * @return TransferenciaDinero
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
     * Add transaccionDetalle
     *
     * @param \AppBundle\Entity\TransaccionDetalle $transaccionDetalle
     *
     * @return TransferenciaDinero
     */
    public function addTransaccionDetalle(\AppBundle\Entity\TransaccionDetalle $transaccionDetalle)
    {
        $this->transaccionDetalle[] = $transaccionDetalle;

        return $this;
    }

    /**
     * Remove transaccionDetalle
     *
     * @param \AppBundle\Entity\TransaccionDetalle $transaccionDetalle
     */
    public function removeTransaccionDetalle(\AppBundle\Entity\TransaccionDetalle $transaccionDetalle)
    {
        $this->transaccionDetalle->removeElement($transaccionDetalle);
    }

    /**
     * Get transaccionDetalle
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTransaccionDetalle()
    {
        return $this->transaccionDetalle;
    }

    /**
     * Set salida
     *
     * @param \AppBundle\Entity\CajaCuentaBanco $salida
     *
     * @return TransferenciaDinero
     */
    public function setSalida(\AppBundle\Entity\CajaCuentaBanco $salida = null)
    {
        $this->salida = $salida;

        return $this;
    }

    /**
     * Get salida
     *
     * @return \AppBundle\Entity\CajaCuentaBanco
     */
    public function getSalida()
    {
        return $this->salida;
    }

    /**
     * Set entrada
     *
     * @param \AppBundle\Entity\CajaCuentaBanco $entrada
     *
     * @return TransferenciaDinero
     */
    public function setEntrada(\AppBundle\Entity\CajaCuentaBanco $entrada = null)
    {
        $this->entrada = $entrada;

        return $this;
    }

    /**
     * Get entrada
     *
     * @return \AppBundle\Entity\CajaCuentaBanco
     */
    public function getEntrada()
    {
        return $this->entrada;
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
}
