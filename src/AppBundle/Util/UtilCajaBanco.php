<?php

namespace AppBundle\Util;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\Security\Core\Security;

use AppBundle\Entity\LogModificacion;
use AppBundle\Entity\SunatF121;
use AppBundle\Entity\SunatF131;

use AppBundle\Entity\Transaccion;
use AppBundle\Entity\TransaccionDetalle;

use Symfony\Component\DependencyInjection\ContainerInterface;

class UtilCajaBanco{

    protected $router;
    protected $security;
    protected $session;
    protected $em;
    protected $conn;
    protected $container;
    
    public function __construct(RouterInterface $router, SessionInterface $session,EntityManagerInterface $em,Connection $connection,Security $security,ContainerInterface $container)
    {
        $this->router   = $router;
        $this->session  = $session;
        $this->em       = $em;
        $this->conn     = $connection;
        $this->security = $security;
        $this->container = $container;     
    }


  
    public function guardarPagoTransaccion($factura,$monto,$tipo)
    {
        $empresa = $this->session->get('empresa');

        if($tipo == 'v'){
            $caja_id = $factura->getCaja()->getId();

        }
       
        if($tipo == 'c'){
            //$factura = $this->em->getRepository('AppBundle:FacturaCompra')->find($id);
        }

        $transaccion = new Transaccion();
        $transaccion->setTipo($tipo);
        $transaccion->setIdentificador($factura->getId());
        $transaccion->setFecha($factura->getFecha());
        $empresaObj = $this->em->getRepository('AppBundle:Empresa')->find($empresa);
        $transaccion->setEmpresa($empresaObj);
        $this->em->persist($transaccion);

        $transaccionDetalle = new TransaccionDetalle();
        $transaccionDetalle->setTransaccion($transaccion);
        $transaccionDetalle->setTipoDocumento('t1');
        $transaccionDetalle->setNumeroDocumento($factura->getTicket());
        $transaccionDetalle->setMonto($monto);

        $cajaCuentaBanco = $this->em->getRepository('AppBundle:CajaCuentaBanco')->findOneBy(array('identificador'=>$caja_id));
        // $monto_anterior = $cajaCuentaBanco->getMonto();
        // $monto_nuevo = $monto + $monto_anterior;
        // $cajaCuentaBanco->setMonto($monto_nuevo);
        // $this->em->persist($cajaCuentaBanco);

        $transaccionDetalle->setCajaCuentaBanco($cajaCuentaBanco);
        $this->em->persist($transaccionDetalle);

        try {

            $this->em->flush();
            return true;

        } catch (\Exception $e) {

            return false;

        }
        
    }    

}