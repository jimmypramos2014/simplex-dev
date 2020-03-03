<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Empresa
 *
 * @ORM\Table(name="empresa")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EmpresaRepository")
 */
class Empresa
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
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="ruc", type="string", length=11, nullable=true)
     */
    private $ruc;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

    /**
     * @var bool
     *
     * @ORM\Column(name="estado", type="boolean", nullable=true)
     */
    private $estado;
    
    /**
     * @ORM\ManyToOne(targetEntity="Distrito", inversedBy="empresa", cascade={"persist"})
     * @ORM\JoinColumn(name="distrito_id", referencedColumnName="id")
     * 
     */
    protected $distrito;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=100, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="url_facturacion", type="string", length=100,nullable=true)
     */
    private $urlFacturacion;

    /**
     * @var string
     *
     * @ORM\Column(name="token_facturacion", type="string", length=100,nullable=true)
     */
    private $tokenFacturacion;

     /**
     * @ORM\OneToMany(targetEntity="CuentaBanco", mappedBy="empresa" , cascade={"remove"})
     */
    protected $cuentaBanco;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_corto", type="string", length=32,nullable=true)
     */
    private $nombreCorto;

    /**
     * @var bool
     *
     * @ORM\Column(name="mostrar_servicios", type="boolean", nullable=true)
     */
    private $mostrarServicios;

    /**
     * @var string
     *
     * @ORM\Column(name="proforma_formato", type="string", length=32,nullable=true)
     */
    private $proformaFormato;

    /**
     * @var string
     *
     * @ORM\Column(name="proforma_orientacion", type="string", length=32,nullable=true)
     */
    private $proformaOrientacion;

    /**
     * @var string
     *
     * @ORM\Column(name="guiaremision_formato", type="string", length=32,nullable=true)
     */
    private $guiaremisionFormato;

    /**
     * @var string
     *
     * @ORM\Column(name="guiaremision_orientacion", type="string", length=32,nullable=true)
     */
    private $guiaremisionOrientacion;

    /**
     * @var string
     *
     * @ORM\Column(name="guiaremision_ancho", type="string", length=32,nullable=true)
     */
    private $guiaremisionAncho;

    /**
     * @var string
     *
     * @ORM\Column(name="guiaremision_largo", type="string", length=32,nullable=true)
     */
    private $guiaremisionLargo;


    /**
     * @var string
     *
     * @ORM\Column(name="direccion_web", type="string", length=100, nullable=true)
     */
    private $direccionWeb;

    /**
     * @var string
     *
     * @ORM\Column(name="prefijo_codigo_producto", type="string", length=32, nullable=true)
     */
    private $prefijoCodigoProducto;

    /**
     * @var bool
     *
     * @ORM\Column(name="permitir_mismo_prefijo_multilocal", type="boolean", nullable=true)
     */
    private $permitirMismoPrefijoMultilocal;

    /**
     * @var string
     *
     * @ORM\Column(name="correo_remitente", type="string", length=100, nullable=true)
     */
    private $correoRemitente;

    /**
     * @var string
     *
     * @ORM\Column(name="subdominio", type="string", length=100,nullable=true)
     */
    private $subdominio;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=100,nullable=true)
     */
    private $logo;

    /**
     * @var bool
     *
     * @ORM\Column(name="formato_ferretero", type="boolean", nullable=true)
     */
    private $formatoFerretero;

    /**
     * 
     * @ORM\OneToMany(targetEntity="EmpresaLocal", mappedBy="empresa")
     */
    private $locales;
    
    public function __toString()
    {
        return $this->getNombre();
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cuentaBanco = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Empresa
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set ruc
     *
     * @param string $ruc
     *
     * @return Empresa
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
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Empresa
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set estado
     *
     * @param boolean $estado
     *
     * @return Empresa
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
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Empresa
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
     * Set urlFacturacion
     *
     * @param string $urlFacturacion
     *
     * @return Empresa
     */
    public function setUrlFacturacion($urlFacturacion)
    {
        $this->urlFacturacion = $urlFacturacion;

        return $this;
    }

    /**
     * Get urlFacturacion
     *
     * @return string
     */
    public function getUrlFacturacion()
    {
        return $this->urlFacturacion;
    }

    /**
     * Set tokenFacturacion
     *
     * @param string $tokenFacturacion
     *
     * @return Empresa
     */
    public function setTokenFacturacion($tokenFacturacion)
    {
        $this->tokenFacturacion = $tokenFacturacion;

        return $this;
    }

    /**
     * Get tokenFacturacion
     *
     * @return string
     */
    public function getTokenFacturacion()
    {
        return $this->tokenFacturacion;
    }

    /**
     * Set nombreCorto
     *
     * @param string $nombreCorto
     *
     * @return Empresa
     */
    public function setNombreCorto($nombreCorto)
    {
        $this->nombreCorto = $nombreCorto;

        return $this;
    }

    /**
     * Get nombreCorto
     *
     * @return string
     */
    public function getNombreCorto()
    {
        return $this->nombreCorto;
    }

    /**
     * Set mostrarServicios
     *
     * @param boolean $mostrarServicios
     *
     * @return Empresa
     */
    public function setMostrarServicios($mostrarServicios)
    {
        $this->mostrarServicios = $mostrarServicios;

        return $this;
    }

    /**
     * Get mostrarServicios
     *
     * @return boolean
     */
    public function getMostrarServicios()
    {
        return $this->mostrarServicios;
    }

    /**
     * Set proformaFormato
     *
     * @param string $proformaFormato
     *
     * @return Empresa
     */
    public function setProformaFormato($proformaFormato)
    {
        $this->proformaFormato = $proformaFormato;

        return $this;
    }

    /**
     * Get proformaFormato
     *
     * @return string
     */
    public function getProformaFormato()
    {
        return $this->proformaFormato;
    }

    /**
     * Set proformaOrientacion
     *
     * @param string $proformaOrientacion
     *
     * @return Empresa
     */
    public function setProformaOrientacion($proformaOrientacion)
    {
        $this->proformaOrientacion = $proformaOrientacion;

        return $this;
    }

    /**
     * Get proformaOrientacion
     *
     * @return string
     */
    public function getProformaOrientacion()
    {
        return $this->proformaOrientacion;
    }

    /**
     * Set guiaremisionFormato
     *
     * @param string $guiaremisionFormato
     *
     * @return Empresa
     */
    public function setGuiaremisionFormato($guiaremisionFormato)
    {
        $this->guiaremisionFormato = $guiaremisionFormato;

        return $this;
    }

    /**
     * Get guiaremisionFormato
     *
     * @return string
     */
    public function getGuiaremisionFormato()
    {
        return $this->guiaremisionFormato;
    }

    /**
     * Set guiaremisionOrientacion
     *
     * @param string $guiaremisionOrientacion
     *
     * @return Empresa
     */
    public function setGuiaremisionOrientacion($guiaremisionOrientacion)
    {
        $this->guiaremisionOrientacion = $guiaremisionOrientacion;

        return $this;
    }

    /**
     * Get guiaremisionOrientacion
     *
     * @return string
     */
    public function getGuiaremisionOrientacion()
    {
        return $this->guiaremisionOrientacion;
    }

    /**
     * Set guiaremisionAncho
     *
     * @param string $guiaremisionAncho
     *
     * @return Empresa
     */
    public function setGuiaremisionAncho($guiaremisionAncho)
    {
        $this->guiaremisionAncho = $guiaremisionAncho;

        return $this;
    }

    /**
     * Get guiaremisionAncho
     *
     * @return string
     */
    public function getGuiaremisionAncho()
    {
        return $this->guiaremisionAncho;
    }

    /**
     * Set guiaremisionLargo
     *
     * @param string $guiaremisionLargo
     *
     * @return Empresa
     */
    public function setGuiaremisionLargo($guiaremisionLargo)
    {
        $this->guiaremisionLargo = $guiaremisionLargo;

        return $this;
    }

    /**
     * Get guiaremisionLargo
     *
     * @return string
     */
    public function getGuiaremisionLargo()
    {
        return $this->guiaremisionLargo;
    }

    /**
     * Set direccionWeb
     *
     * @param string $direccionWeb
     *
     * @return Empresa
     */
    public function setDireccionWeb($direccionWeb)
    {
        $this->direccionWeb = $direccionWeb;

        return $this;
    }

    /**
     * Get direccionWeb
     *
     * @return string
     */
    public function getDireccionWeb()
    {
        return $this->direccionWeb;
    }

    /**
     * Set prefijoCodigoProducto
     *
     * @param string $prefijoCodigoProducto
     *
     * @return Empresa
     */
    public function setPrefijoCodigoProducto($prefijoCodigoProducto)
    {
        $this->prefijoCodigoProducto = $prefijoCodigoProducto;

        return $this;
    }

    /**
     * Get prefijoCodigoProducto
     *
     * @return string
     */
    public function getPrefijoCodigoProducto()
    {
        return $this->prefijoCodigoProducto;
    }

    /**
     * Set permitirMismoPrefijoMultilocal
     *
     * @param boolean $permitirMismoPrefijoMultilocal
     *
     * @return Empresa
     */
    public function setPermitirMismoPrefijoMultilocal($permitirMismoPrefijoMultilocal)
    {
        $this->permitirMismoPrefijoMultilocal = $permitirMismoPrefijoMultilocal;

        return $this;
    }

    /**
     * Get permitirMismoPrefijoMultilocal
     *
     * @return boolean
     */
    public function getPermitirMismoPrefijoMultilocal()
    {
        return $this->permitirMismoPrefijoMultilocal;
    }

    /**
     * Set correoRemitente
     *
     * @param string $correoRemitente
     *
     * @return Empresa
     */
    public function setCorreoRemitente($correoRemitente)
    {
        $this->correoRemitente = $correoRemitente;

        return $this;
    }

    /**
     * Get correoRemitente
     *
     * @return string
     */
    public function getCorreoRemitente()
    {
        return $this->correoRemitente;
    }

    /**
     * Set subdominio
     *
     * @param string $subdominio
     *
     * @return Empresa
     */
    public function setSubdominio($subdominio)
    {
        $this->subdominio = $subdominio;

        return $this;
    }

    /**
     * Get subdominio
     *
     * @return string
     */
    public function getSubdominio()
    {
        return $this->subdominio;
    }

    /**
     * Set distrito
     *
     * @param \AppBundle\Entity\Distrito $distrito
     *
     * @return Empresa
     */
    public function setDistrito(\AppBundle\Entity\Distrito $distrito = null)
    {
        $this->distrito = $distrito;

        return $this;
    }

    /**
     * Get distrito
     *
     * @return \AppBundle\Entity\Distrito
     */
    public function getDistrito()
    {
        return $this->distrito;
    }

    /**
     * Add cuentaBanco
     *
     * @param \AppBundle\Entity\CuentaBanco $cuentaBanco
     *
     * @return Empresa
     */
    public function addCuentaBanco(\AppBundle\Entity\CuentaBanco $cuentaBanco)
    {
        $this->cuentaBanco[] = $cuentaBanco;

        return $this;
    }

    /**
     * Remove cuentaBanco
     *
     * @param \AppBundle\Entity\CuentaBanco $cuentaBanco
     */
    public function removeCuentaBanco(\AppBundle\Entity\CuentaBanco $cuentaBanco)
    {
        $this->cuentaBanco->removeElement($cuentaBanco);
    }

    /**
     * Get cuentaBanco
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCuentaBanco()
    {
        return $this->cuentaBanco;
    }

    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return Empresa
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set formatoFerretero
     *
     * @param boolean $formatoFerretero
     *
     * @return Empresa
     */
    public function setFormatoFerretero($formatoFerretero)
    {
        $this->formatoFerretero = $formatoFerretero;

        return $this;
    }

    /**
     * Get formatoFerretero
     *
     * @return boolean
     */
    public function getFormatoFerretero()
    {
        return $this->formatoFerretero;
    }

    /**
     * Add locale
     *
     * @param \AppBundle\Entity\EmpresaLocal $locale
     *
     * @return Empresa
     */
    public function addLocale(\AppBundle\Entity\EmpresaLocal $locale)
    {
        $this->locales[] = $locale;

        return $this;
    }

    /**
     * Remove locale
     *
     * @param \AppBundle\Entity\EmpresaLocal $locale
     */
    public function removeLocale(\AppBundle\Entity\EmpresaLocal $locale)
    {
        $this->locales->removeElement($locale);
    }

    /**
     * Get locales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocales()
    {
        return $this->locales;
    }
}
