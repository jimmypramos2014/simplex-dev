<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Producto controller.
 *
 * @Route("producto")
 */
class ProductoController extends Controller
{
    /**
     * Lists all producto entities.
     *
     * @Route("/", name="producto_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ALMACENERO') or has_role('ROLE_VENDEDOR')")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $dql = "SELECT p FROM AppBundle:Producto p ";
        $dql .= " JOIN p.empresa e";
        $dql .= " WHERE  p.estado = 1  ";

        if($empresa != ''){
            $dql .= " AND e.id =:empresa  ";
        }

        $dql .= " ORDER BY p.nombre ";

        $query = $em->createQuery($dql);

        if($empresa != ''){
            $query->setParameter('empresa',$empresa);        
        }

        $productos =  $query->getResult();    


        return $this->render('producto/index.html.twig', array(
            'productos' => $productos,
            'titulo'    => 'Lista de productos'
        ));
    }

    /**
     * Creates a new producto entity.
     *
     * @Route("/new", name="producto_new")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ALMACENERO') or has_role('ROLE_VENDEDOR')")
     */
    public function newAction(Request $request)
    {
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');

        $producto = new Producto();
        $form = $this->createForm('AppBundle\Form\ProductoType', $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $producto->setEstado(true);


            $file = $producto->getImagen();

            if($file){
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                $file->move(
                    $this->getParameter('imagenes_directorio'),
                    $fileName
                );

                $producto->setImagen($fileName);
            }

            $localObj = $em->getRepository('AppBundle:EmpresaLocal')->find($local);

            $producto->setLocal($localObj);
            $em->persist($producto);
            

            try {

                $em->flush();

                $this->addFlash("success", "El registro fue guardado exitosamente.");

            } catch (\Exception $e) {

                $this->addFlash("danger", "Ocurrió un error inesperado, el registro no se guardó.");                
            }

            
            return $this->redirectToRoute('producto_index');
        }

        return $this->render('producto/new.html.twig', array(
            'producto' => $producto,
            'form' => $form->createView(),
            'titulo'    => 'Agregar producto'
        ));
    }

    /**
     * Finds and displays a producto entity.
     *
     * @Route("/{id}", name="producto_show")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ALMACENERO') or has_role('ROLE_VENDEDOR')")
     */
    public function showAction(Producto $producto)
    {

        return $this->render('producto/show.html.twig', array(
            'producto' => $producto,
        ));
    }

    /**
     * Displays a form to edit an existing producto entity.
     *
     * @Route("/{id}/edit", name="producto_edit")
     * @Method({"GET", "POST"})
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ALMACENERO') or has_role('ROLE_VENDEDOR')")
     */
    public function editAction(Request $request, Producto $producto)
    {
        $session        = $request->getSession();
        $local          = $session->get('local');
        $empresa        = $session->get('empresa');  

        $originalFile = null;

        if($producto->getImagen()){

            $originalFile = $producto->getImagen();

            $producto->setImagen(
                new File($this->getParameter('imagenes_directorio').'/'.$producto->getImagen())
            );
        }



        $editForm = $this->createForm('AppBundle\Form\ProductoType', $producto);
        $editForm->handleRequest($request);


        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $producto->setEstado(true);
            
            $localObj = $em->getRepository('AppBundle:EmpresaLocal')->find($local);

            $producto->setLocal($localObj);

            $file = $producto->getImagen();

            if($file){

                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                $file->move(
                    $this->getParameter('imagenes_directorio'),
                    $fileName
                );


                $producto->setImagen($fileName);

            }else{

                $producto->setImagen($originalFile);
            }

            


            $em->persist($producto);
            
            try {

                $em->flush();
                $this->addFlash("success", "El registro fue guardado exitosamente.");

            } catch (\Exception $e) {
                
                $this->addFlash("danger", "Ocurrió un error inesperado, el registro no se guardó.");                
            }

            

            return $this->redirectToRoute('producto_index');
        }

        return $this->render('producto/edit.html.twig', array(
            'producto'  => $producto,
            'edit_form' => $editForm->createView(),
            'titulo'    => 'Editar producto',
            'originalFile' => $originalFile,
        ));
    }

    /**
     * Deletes a producto entity.
     *
     * @Route("/{id}/delete", name="producto_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_ALMACENERO') or has_role('ROLE_VENDEDOR')")
     */
    public function deleteAction(Request $request, Producto $producto)
    {
        $em = $this->getDoctrine()->getManager();

        $producto->setEstado(false);
        $em->persist($producto);

        try{

            $em->flush();

            $this->addFlash("success", "El registro fue eliminado exitosamente.");

        }catch(\Exception $e){

            $this->addFlash("danger", "Ocurrió un error inesperado, el registro no pudo ser eliminado.");
        }
        
        return $this->redirectToRoute('producto_index');
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }

}
