<?php

namespace AppBundle\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Empresa;
use AppBundle\Entity\Producto;
use AppBundle\Entity\ProductoXLocal;
use AppBundle\Entity\EmpresaLocal;
use AppBundle\Entity\Caja;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\CajaCuentaBanco;
use AppBundle\Entity\Empleado;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class SecurityController extends \FOS\UserBundle\Controller\SecurityController
{
    /**
     * We override loginAction to redirect the user depending on their role.
     * If they try to go to /login, they will be redirected accordingly based on their role
     *
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        $auth_checker = $this->get('security.authorization_checker');
        $router = $this->get('router');

        // 307: Internal Redirect
        if ($auth_checker->isGranted(['ROLE_SUPER_ADMIN'])) {
            // SUPER_ADMIN roles go to the `admin_home` route
            return new RedirectResponse($router->generate('empresa_index'), 307);
        }

        if ($auth_checker->isGranted('ROLE_ADMIN')) {
            // Everyone else goes to the `home` route
            return new RedirectResponse($router->generate('dashboard'), 307);
        }

        if ($auth_checker->isGranted('ROLE_VENDEDOR')) {
            // Everyone else goes to the `home` route
            return new RedirectResponse($router->generate('almacen_productosxlocal'), 307);
        }

        if ($auth_checker->isGranted('ROLE_ALMACENERO')) {
            // Everyone else goes to the `home` route
            return new RedirectResponse($router->generate('almacen_productosxlocal'), 307);
        }

        // Always call the parent unless you provide the ENTIRE implementation
        return parent::loginAction($request);
    }



    /**
     * Ingresar email para verificar y enviar link de activacion.
     *
     * @Route("/set/email/registro", name="set_email_registro")
     * @Method({"GET", "POST"})
     */
    public function setEmailRegistroAction(Request $request,\Swift_Mailer $mailer)
    {

        $form = $this->createFormBuilder()
            ->add('email', TextType::class,array(
                'attr'      => array('class' => 'form-control ','placeholder'=>'Ingrese su correo electrónico'),
                'label'     => 'Correo electrónico',
                'required'  => true,               
                ))
            ->add('url', HiddenType::class,array(
                'label'     => false,
                'required'  => true,
                //'data'      => 'http://stackoverflow.com'               
                ))            
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() ) {//&& $form->isValid()

            $email              = $form->getData()['email'];
            $url_redireccion    = $form->getData()['url'];

            $url = $this->generateUrl('usuario_registro', array('email'=>$email),UrlGeneratorInterface::ABSOLUTE_URL);

            $mensaje = (new \Swift_Message('Activar usuario'))
                ->setFrom('jpena@intimedia.net')
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        "FOSUserBundle:Security:mensaje_activacion.html.twig", array(
                        'confirmationUrl' => $url,
                        'mensaje'=> 'Se ha verificado su correo electrónico.Por favor siga las instrucciones.' )
                    ),
                    'text/html'
                )

            ;

            try {

                $mailer->send($mensaje);
                $res = 'ok';

                $this->addFlash("success", 'Se ha enviado un link de verificación a su correo electrónico. Revíselo y siga las instrucciones.');                   
                
            } catch (\Exception $e) {

                $res = 'error';
                $this->addFlash("danger", $e.' .Error en el envío. Consulte al administrador del sistema.');                   
                
            }

            return $this->redirect($url_redireccion.'?res='.$res);

            //return $this->redirectToRoute('set_email_registro');

        }

        return $this->render('@FOSUser/Security/set_email.html.twig', array(
            'form'   => $form->createView()
        ));

    }


    /**
     * Registro de usuario.
     *
     * @Route("/usuario/registro", name="usuario_registro")
     * @Method({"GET", "POST"})
     */
    public function usuarioRegistroAction(Request $request)
    {
        $session        = $request->getSession();

        $email = $request->query->get('email');

        $options = array('email'=>$email);

        $form = $this->createFormBuilder($options)
            ->add('razon_social', TextType::class,array(
                'attr'      => array('class' => 'form-control ','placeholder'=>''),
                'label'     => 'Razón social',
                'required'  => true,               
                ))
            ->add('subdominio', TextType::class,array(
                'attr'      => array('class' => 'form-control ','placeholder'=>''),
                'label'     => 'Nombre en Simplex',
                'required'  => true,               
                ))            
            ->add('dni', TextType::class,array(
                'attr'      => array('class' => 'form-control ','placeholder'=>''),
                'label'     => 'Número de DNI',
                'required'  => true,               
                ))
            ->add('email', TextType::class,array(
                'attr'      => array('class' => 'form-control ','placeholder'=>''),
                'label'     => 'Email',
                'required'  => true,
                'data'      => $options['email']              
                ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraeñas deben coincidir.',
                'options' => array('attr' => array('class' => 'form-control password-field')),
                'required' => true,
                'first_options'  => array('label' => 'Contraseña'),
                'second_options' => array('label' => 'Repetir contraseña'),
            ))                                         
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //dump($form->getData());
            //die();

            $em   = $this->getDoctrine()->getManager();
            $conn = $this->get('database_connection');
            $userManager = $this->get('fos_user.user_manager');

            $empresa = new Empresa();
            $empresa->setNombre($form->getData()['razon_social']);
            $empresa->setRuc('00000000000');
            $empresa->setEstado(true);
            $distrito = $em->getRepository('AppBundle:Distrito')->find(5210);
            $empresa->setDistrito($distrito);
            $empresa->setSubdominio($form->getData()['subdominio']);

            $empresa->setProformaFormato('A4');
            $empresa->setProformaOrientacion('Landscape');
            $empresa->setPrefijoCodigoProducto('PROD-');
            $empresa->setGuiaremisionAncho('210');
            $empresa->setGuiaremisionLargo('297');

            $em->persist($empresa);

            //Creamos un local general por defecto
            $localObj = new EmpresaLocal();
            $localObj->setCodigo('001');
            $localObj->setNombre('Local General');
            $localObj->setEstado(true);
            $localObj->setPrefijoVoucher('PG1');
            $localObj->setSerieBoleta('BBB1');
            $localObj->setSerieFactura('FFF1');
            $localObj->setEmpresa($empresa);
            $localObj->setImagenProductoDefault('producto_default.png');
            $localObj->setImagenCategoriaDefault('categoria_default.png');
            $localObj->setFacturacionElectronica(false);
            $localObj->setVentaNegativo(false);            

            $em->persist($localObj);  

            //Creamos un caja general por defecto
            $caja = new Caja();
            $caja->setLocal($localObj);
            $caja->setNombre('Caja general');
            $caja->setEstado(true);
            $caja->setCondicion('cerrado');

            $em->persist($caja);

            //Creamos el usuario administrador  

            $user = $userManager->findUserByUsername($form->getData()['dni']);

            if($user){
                $this->addFlash("danger", "El usuario ya existe. Verifique si su nombre de usuario ya está siendo usado.");
                return $this->redirect($this->generateUrl('usuario_registro'));
            }

            $user = $userManager->findUserByEmail($form->getData()['email']);

            if($user){
                $this->addFlash("danger", "El correo ya existe. Verifique si el correo electrónico ya está siendo usado.");
                return $this->redirect($this->generateUrl('usuario_registro'));
            }

            $user = $userManager->createUser();

            $user->setUsername($form->getData()['dni']);
            $user->setUsernameCanonical($form->getData()['dni']);
            $user->setEmail($form->getData()['email']);
            $user->setEmailCanonical($form->getData()['email']);
            $user->setPlainPassword($form->getData()['password']);
            $user->setEnabled(true);
            $role = 'ROLE_ADMIN';            
            $roleArray = array($role);            
            $user->setRoles($roleArray);


            $empleado = new Empleado();
            $puesto = $em->getRepository('AppBundle:Puesto')->find(3);
            $empleado->setPuesto($puesto);
            $empleado->setUsuario($user);
            $empleado->setEstado(true);
            $empleado->setDni($form->getData()['dni']);
            $empleado->setEmail($form->getData()['email']);
            $empleado->setLocal($localObj);

            $em->persist($empleado);


            //Creamos un cliente x defecto
            $clienteObj = new Cliente();
            $clienteObj->setRazonSocial('---');
            $clienteObj->setRuc('---');
            $clienteObj->setEstado(true);
            $clienteObj->setLocal($localObj);
            $clienteObj->setCodigo('000000');
            $tipoDocumento = $em->getRepository('AppBundle:TipoDocumento')->find(1);
            $clienteObj->setTipoDocumento($tipoDocumento);

            $em->persist($clienteObj);          


            try {

                $userManager->updateUser($user);
                $em->flush();


                //Insertamos valores en la tabla producto_unidad_categoria
                $sql  = "INSERT INTO producto_unidad_categoria (codigo,nombre,descripcion,empresa_id) ";
                $sql .= " SELECT codigo,nombre,descripcion,? FROM producto_unidad_categoria_base ";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $empresa->getId());
                $stmt->execute();


                //Insertamos valores en la tabla producto_unidad
                $sql  = "INSERT INTO producto_unidad (codigo,nombre,descripcion,abreviatura,categoria_id,empresa_id,tipo,estado,ratio) ";
                $sql .= " SELECT codigo,nombre,descripcion,abreviatura,categoria_id,?,tipo,estado,ratio FROM producto_unidad_base ";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $empresa->getId());
                $stmt->execute();

                //Insertamos valores en la tabla proveedor_servicio
                $sql  = "INSERT INTO proveedor_servicio (codigo,nombre,descripcion,empresa_id) ";
                $sql .= " SELECT codigo,nombre,descripcion,? FROM proveedor_servicio_base ";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $empresa->getId());
                $stmt->execute();

                //Insertamo valor en caja_cuenta_banco
                $sql  = "INSERT INTO caja_cuenta_banco (cuenta_tipo_id,numero,identificador,empresa_id) ";
                $sql .= " VALUES (1,?,?,?) ";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, 'General');
                $stmt->bindValue(2, $caja->getId());
                $stmt->bindValue(3, $empresa->getId());
                $stmt->execute();


                //Insertamos valores en la tabla documento
                $sql  = "INSERT INTO documento (codigo,nombre,descripcion,empresa_id,empresa_local_id) ";
                $sql .= " SELECT codigo,nombre,descripcion,?,? FROM documento_base ";

                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $empresa->getId());
                $stmt->bindValue(2, $localObj->getId());
                $stmt->execute();

                //Generamos registros en la tabla componente_x_documento,
                $documentos = $em->getRepository('AppBundle:Documento')->findBy(array('local'=>$localObj->getId()));

                foreach($documentos as $documento){

                    $codigoDocumento = $documento->getCodigo();

                    //Seleccionamos la tabla componente_x_documento_base
                    $sql = " SELECT cxd.posicion_x,cxd.estado,cxd.documento_id,cxd.componente_id,cxd.posicion_y FROM componente_x_documento_base cxd 
                                INNER JOIN documento d ON cxd.documento_id = d.id WHERE d.codigo = ? ";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindValue(1, $codigoDocumento);
                    $stmt->execute();
                    $componentes = $stmt->fetchAll();

                    foreach($componentes as $componente)
                    {

                        //Insertamos valores en la tabla componente_x_documento
                        $sql  = "INSERT INTO componente_x_documento (posicion_x,estado,documento_id,componente_id,posicion_y) ";
                        $sql .= " VALUES (?,?,?,?,?) ";

                        $stmt = $conn->prepare($sql);
                        $stmt->bindValue(1, $componente['posicion_x']);
                        $stmt->bindValue(2, $componente['estado']);
                        $stmt->bindValue(3, $documento->getId());
                        $stmt->bindValue(4, $componente['componente_id']);
                        $stmt->bindValue(5, $componente['posicion_y']);
                        $stmt->execute();

                    }

                }



                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->get('security.token_storage')->setToken($token);

                $this->get('session')->set('_security_main', serialize($token));
                
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                $session->set('local',$localObj->getId());        
                $session->set('empresa',$empresa->getId());
                $session->set('empleado',$empleado->getId());
                $session->set('rol','Administrador');
                $session->set('usuario',$user->getId());
                $session->set('departamento',$empresa->getDistrito()->getProvincia()->getDepartamento()->getId());

                //Creamos la carpeta donde se guardaran los formatos de venta
                $fileSystem = new Filesystem();
                $fileSystem->mkdir('formatos/'.$empresa->getId());
                $fileSystem->mkdir('uploads/files/'.$empresa->getId());


                $this->addFlash("success", 'Su registro fue finalizado exitosamente. Ingrese al sistema con los datos registrados.');

                $response = new RedirectResponse($this->get('router')->generate('fos_user_security_logout')); 
                return $response;

                //return $this->redirectToRoute('fos_user_security_logout');                  
                
            } catch (\Exception $e) {

                $this->addFlash("danger", $e.' .Error en el registro. Consulte al administrador del sistema.');
                $response = new RedirectResponse($this->get('router')->generate('fos_user_security_logout')); 
                return $response;        
                
            }
            //return $this->redirect('http://simplex.test/main/dashboard');
            //return $this->redirectToRoute('fos_user_security_login');

        }

        return $this->render('@FOSUser/Security/usuario_registro.html.twig', array(
            'form'   => $form->createView()
        ));

    }




}