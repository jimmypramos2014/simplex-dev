<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaccion
 *
 * @ORM\Table(name="transaccion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransaccionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Transaccion
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
     * @ORM\Column(name="tipo", type="string", length=4, nullable=true)
     */
    private $tipo;

    /**
     * @var int
     *
     * @ORM\Column(name="identificador", type="integer", length=32, nullable=true)
     */
    private $identificador;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=32, nullable=true)
     */
    private $numero;

     /**
     * @ORM\OneToMany(targetEntity="TransaccionDetalle", mappedBy="transaccion" , cascade={"persist","remove"})
     */
    protected $transaccionDetalle;

    /**
     * @ORM\ManyToOne(targetEntity="Empresa", inversedBy="transaccion", cascade={"persist"})
     * @ORM\JoinColumn(name="empresa_id", referencedColumnName="id")
     * 
     */
    protected $empresa;

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
     * Constructor
     */
    public function __construct()
    {
        $this->transaccionDetalle = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     *
     * @return Transaccion
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
     * Set identificador
     *
     * @param integer $identificador
     *
     * @return Transaccion
     */
    public function setIdentificador($identificador)
    {
        $this->identificador = $identificador;

        return $this;
    }

    /**
     * Get identificador
     *
     * @return integer
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Transaccion
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
     * Set numero
     *
     * @param string $numero
     *
     * @return Transaccion
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
     * Add transaccionDetalle
     *
     * @param \AppBundle\Entity\TransaccionDetalle $transaccionDetalle
     *
     * @return Transaccion
     */
    public function addTransaccionDetalle(\AppBundle\Entity\TransaccionDetalle $transaccionDetalle)
    {
        //$this->transaccionDetalle[] = $transaccionDetalle;

        $transaccionDetalle->setTransaccion($this);

        $this->transaccionDetalle->add($transaccionDetalle);
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
     * Set empresa
     *
     * @param \AppBundle\Entity\Empresa $empresa
     *
     * @return Transaccion
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
}
