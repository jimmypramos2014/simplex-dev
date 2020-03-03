<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Empleado
 *
 * @ORM\Table(name="empleado")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmpleadoRepository")
 */
class Empleado
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
     * @ORM\Column(name="nombres", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message = "Este valor es requerido")
     */
    private $nombres;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido_paterno", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message = "Este valor es requerido")
     */
    private $apellidoPaterno;

    /**
     * @var string
     *
     * @ORM\Column(name="apellido_materno", type="string", length=100, nullable=true)
     * @Assert\NotBlank(message = "Este valor es requerido")
     */
    private $apellidoMaterno;

    /**
     * @var string
     *
     * @ORM\Column(name="dni", type="string", length=8, nullable=true, unique=true)
     */
    private $dni;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     */
    private $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="empleado", cascade={"persist"})
     * @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    protected $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="EmpresaLocal", inversedBy="empleado", cascade={"persist"})
     * @ORM\JoinColumn(name="empresa_local_id", referencedColumnName="id")
     * 
     */
    protected $local;

    /**
     * @ORM\ManyToOne(targetEntity="Puesto", inversedBy="empleado", cascade={"persist"})
     * @ORM\JoinColumn(name="puesto_id", referencedColumnName="id")
     * 
     */
    protected $puesto;

    /**
     * @var bool
     *
     * @ORM\Column(name="oculto", type="boolean", nullable=true)
     */
    private $oculto;
    

    public function __toString()
    {
        return strtoupper($this->getDni().' - '.$this->getNombres().' '.$this->getApellidoPaterno().' '.$this->getApellidoMaterno());
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
     * Set nombres
     *
     * @param string $nombres
     *
     * @return Empleado
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;

        return $this;
    }

    /**
     * Get nombres
     *
     * @return string
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * Set apellidoPaterno
     *
     * @param string $apellidoPaterno
     *
     * @return Empleado
     */
    public function setApellidoPaterno($apellidoPaterno)
    {
        $this->apellidoPaterno = $apellidoPaterno;

        return $this;
    }

    /**
     * Get apellidoPaterno
     *
     * @return string
     */
    public function getApellidoPaterno()
    {
        return $this->apellidoPaterno;
    }

    /**
     * Set apellidoMaterno
     *
     * @param string $apellidoMaterno
     *
     * @return Empleado
     */
    public function setApellidoMaterno($apellidoMaterno)
    {
        $this->apellidoMaterno = $apellidoMaterno;

        return $this;
    }

    /**
     * Get apellidoMaterno
     *
     * @return string
     */
    public function getApellidoMaterno()
    {
        return $this->apellidoMaterno;
    }

    /**
     * Set dni
     *
     * @param string $dni
     *
     * @return Empleado
     */
    public function setDni($dni)
    {
        $this->dni = $dni;

        return $this;
    }

    /**
     * Get dni
     *
     * @return string
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     *
     * @return Empleado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return bool
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     *
     * @return Empleado
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
     * Set local
     *
     * @param \AppBundle\Entity\EmpresaLocal $local
     *
     * @return Empleado
     */
    public function setLocal(\AppBundle\Entity\EmpresaLocal $local = null)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return \AppBundle\Entity\EmpresaLocal
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set puesto
     *
     * @param \AppBundle\Entity\Puesto $puesto
     *
     * @return Empleado
     */
    public function setPuesto(\AppBundle\Entity\Puesto $puesto = null)
    {
        $this->puesto = $puesto;

        return $this;
    }

    /**
     * Get puesto
     *
     * @return \AppBundle\Entity\Puesto
     */
    public function getPuesto()
    {
        return $this->puesto;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Empleado
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set oculto
     *
     * @param boolean $oculto
     *
     * @return Empleado
     */
    public function setOculto($oculto)
    {
        $this->oculto = $oculto;

        return $this;
    }

    /**
     * Get oculto
     *
     * @return boolean
     */
    public function getOculto()
    {
        return $this->oculto;
    }
}
