<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\ProductoMarca;
use AppBundle\Entity\ProductoCategoria;
use AppBundle\Entity\Producto;
use AppBundle\Entity\ProductoXLocal;
use AppBundle\Entity\Cliente;
use AppBundle\Entity\Proveedor;

/**
 * Importacion controller.
 *
 * @Route("importacion")
 */
class ImportacionController extends Controller
{

    /**
     * Lists all detalleVentum entities.
     *
     * @Route("/", name="importacion_index")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request)
    {

      $em = $this->getDoctrine()->getManager();

      $session        = $request->getSession();
      $local          = $session->get('local');
      $empresa        = $session->get('empresa');

      $empresaObj = $em->getRepository('AppBundle:Empresa')->find($empresa);
      $localObj = $em->getRepository('AppBundle:EmpresaLocal')->find($local);

			$tmpfname = "test.xlsx";

			$objPHPExcel = \PHPExcel_IOFactory::load($tmpfname);

			$i = 0;
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

				//Importamos los productos
				// if($i == 0){

			 //    $highestRow         = $worksheet->getHighestRow();
			 //    $highestColumn      = $worksheet->getHighestColumn();
			 //    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

			 //    for ($row = 2; $row <= $highestRow; ++ $row) {

	   //          $codigo 					= $worksheet->getCellByColumnAndRow(0, $row)->getValue();
	   //          $producto_nombre 	= $worksheet->getCellByColumnAndRow(1, $row)->getValue();
	   //          $marca_nombre 		= $worksheet->getCellByColumnAndRow(2, $row)->getValue();
	   //          $categoria_nombre = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
	   //          $precio_venta 		= $worksheet->getCellByColumnAndRow(4, $row)->getValue();
	   //          $precio_costo 		= $worksheet->getCellByColumnAndRow(5, $row)->getValue();
	   //          $precio_x_mayor 	= $worksheet->getCellByColumnAndRow(6, $row)->getValue();
	   //          $stock 						= $worksheet->getCellByColumnAndRow(7, $row)->getValue();
	   //          $unidad_venta 		= $worksheet->getCellByColumnAndRow(8, $row)->getValue();
	   //          $unidad_compra 		= $worksheet->getCellByColumnAndRow(9, $row)->getValue();

	   //          $marca = $em->getRepository('AppBundle:ProductoMarca')->findOneBy(array('empresa'=>$empresa,'nombre'=>$marca_nombre));

    //         	if(!$marca){

				// 				  $marca = new ProductoMarca();
				// 				  $marca->setNombre($marca_nombre);
				// 				  $marca->setEmpresa($empresaObj);
				// 				  $marca->setEstado(true);
				// 				  $em->persist($marca);

    //         	}		

    //         	$categoria = $em->getRepository('AppBundle:ProductoCategoria')->findOneBy(array('empresa'=>$empresa,'nombre'=>$categoria_nombre));

    //         	if(!$categoria){

				// 				  $categoria = new ProductoCategoria();
				// 				  $categoria->setNombre($categoria_nombre);
				// 				  $categoria->setEmpresa($empresaObj);
				// 				  $categoria->setEstado(true);
				// 				  $em->persist($categoria);

    //         	}

	   //          $producto = $em->getRepository('AppBundle:Producto')->findOneBy(array('empresa'=>$empresa,'codigo'=>$codigo));

    //         	if(!$producto){

				// 				  $producto = new Producto();
				// 				  $producto->setCodigo($codigo);
				// 				  $producto->setNombre($producto_nombre);
				// 				  $producto->setEmpresa($empresaObj);
				// 				  $producto->setEstado(true);
				// 				  $producto->setMarca($marca);
				// 				  $producto->setCategoria($categoria);
				// 				  $producto->setPrecioUnitario($precio_venta);
				// 				  $producto->setPrecioCompra($precio_costo);
				// 				  $producto->setPrecioCantidad($precio_x_mayor);
				// 				  $producto->setEstado(true);

				// 				  $unidad = $em->getRepository('AppBundle:ProductoUnidad')->findOneBy(array('empresa'=>$empresa,'codigo'=>'1'));
				// 				  $producto->setUnidadVenta($unidad);
				// 				  $producto->setUnidadCompra($unidad);
				// 				  $em->persist($producto);

				// 				  $productoXLocal = new ProductoXLocal();
				// 				  $productoXLocal->setLocal($localObj);
				// 				  $productoXLocal->setProducto($producto);
				// 				  $productoXLocal->setStock($stock);
				// 				  $productoXLocal->setEstado(true);

				// 				  //seteamos stock inicial
				// 				  $productoXLocal->setStockInicial($stock);

				// 				  $em->persist($productoXLocal);

    //         	}

    //         	try {

    //         		$em->flush();
            		
    //         	} catch (\Exception $e) {

    //         		return new Response('Hubo un error al importar los productos.'.$e);  
            		
    //         	}

		  //     }

			 //  }

			  //Importamos los clientes
				if($i == 1){

			    $highestRow         = $worksheet->getHighestRow();
			    $highestColumn      = $worksheet->getHighestColumn();
			    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);


			    for ($row = 2; $row <= $highestRow; ++ $row) {

	            $cliente_nombre  	= $worksheet->getCellByColumnAndRow(0, $row)->getValue();
	            $tipo_documento 	= $worksheet->getCellByColumnAndRow(1, $row)->getValue();
	            $numero_documento	= $worksheet->getCellByColumnAndRow(2, $row)->getValue();
	            $direccion        = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
	            $distrito      		= $worksheet->getCellByColumnAndRow(4, $row)->getValue();
	            $telefono 		    = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
	            $email 	          = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

	            $cliente = $em->getRepository('AppBundle:Cliente')->findOneBy(array('local'=>$local,'ruc'=>$numero_documento));

            	if(!$cliente){

								  $cliente = new Cliente();
								  $cliente->setRazonSocial($cliente_nombre);
								  $cliente->setRuc($numero_documento);
								  $cliente->setLocal($localObj);
								  $cliente->setEstado(true);
								  $cliente->setDireccion($direccion);
								  $cliente->setTelefono($telefono);
								  $cliente->setEmail($email);

								  $tipoDocumento = $em->getRepository('AppBundle:TipoDocumento')->findOneBy(array('nombre'=>$tipo_documento));

								  if($tipoDocumento){
								  	$cliente->setTipoDocumento($tipoDocumento);
								  }else{
								  	$tipoDocumento = $em->getRepository('AppBundle:TipoDocumento')->findOneBy(array('nombre'=>'DNI'));
								  	$cliente->setTipoDocumento($tipoDocumento);

								  }
								  
								  $em->persist($cliente);

            	}		


            	try {

            		$em->flush();
            		
            	} catch (\Exception $e) {

            		return new Response('Hubo un error al importar los clientes.'.$e);  
            		
            	}

		      }

			  }

			  //Importamos los proveedores

				// if($i == 2){

			 //    $highestRow         = $worksheet->getHighestRow();
			 //    $highestColumn      = $worksheet->getHighestColumn();
			 //    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);


			 //    for ($row = 2; $row <= $highestRow; ++ $row) {

	   //          $proveedor_nombre = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
	   //          $ruc 							= $worksheet->getCellByColumnAndRow(1, $row)->getValue();
	   //          $direccion        = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
	   //          $distrito      		= $worksheet->getCellByColumnAndRow(3, $row)->getValue();
	   //          $telefono 		    = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
	   //          $email 	          = $worksheet->getCellByColumnAndRow(5, $row)->getValue();

	   //          $proveedor = $em->getRepository('AppBundle:Proveedor')->findOneBy(array('empresa'=>$empresa,'ruc'=>$ruc));

    //         	if(!$proveedor){

    //         			$cod = $row - 1;

    //         			$codigo_proveedor = str_pad($cod, 6, "0", STR_PAD_LEFT);

				// 				  $proveedor = new Proveedor();
				// 				  $proveedor->setCodigo('PROV'.$codigo_proveedor);
				// 				  $proveedor->setNombre($proveedor_nombre);
				// 				  $proveedor->setRuc($ruc);
				// 				  $proveedor->setEmpresa($empresaObj);
				// 				  $proveedor->setEstado(true);
				// 				  $proveedor->setDireccion($direccion);
				// 				  $proveedor->setTelefono($telefono);
				// 				  $proveedor->setEmail($email);
							  
				// 				  $em->persist($proveedor);

    //         	}		


    //         	try {

    //         		$em->flush();
            		
    //         	} catch (\Exception $e) {

    //         		return new Response('Hubo un error al importar los proveedores.'.$e);  
            		
    //         	}

		  //     }

			 //  }

			  $i++;

			}



		    // // $worksheetTitle     = $worksheet->getTitle();
		    // $highestRow         = $worksheet->getHighestRow(); // e.g. 10
		    // $highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
		    // $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
		    // // $nrColumns = ord($highestColumn) - 64;
		    // // echo "<br>The worksheet ".$worksheetTitle." has ";
		    // // echo $nrColumns . ' columns (A-' . $highestColumn . ') ';
		    // // echo ' and ' . $highestRow . ' row.';
		    // // echo '<br>Data: <table border="1"><tr>';
		    // for ($row = 2; $row <= $highestRow; ++ $row) {
		    //     // echo '<tr>';
		    //     for ($col = 0; $col < $highestColumnIndex; ++ $col) {
		    //         $cell = $worksheet->getCellByColumnAndRow($col, $row);
		    //         $val = $cell->getValue();
		    //         $dataType = \PHPExcel_Cell_DataType::dataTypeForValue($val);
		    //         // echo '<td>' . $val . '<br>(Typ ' . $dataType . ')</td>';
		    //     }
		    //     // echo '</tr>';
		    // }
		    // // echo '</table>';

		    

		// $excelObj = $excelReader->load($tmpfname);
		// $worksheet = $excelObj->getSheet(0);
		// $lastRow = $worksheet->getHighestRow();
		
		// echo "<table>";
		// for ($row = 1; $row <= $lastRow; $row++) {
		// 	 echo "<tr><td>";
		// 	 echo $worksheet->getCell('A'.$row)->getValue();
		// 	 echo "</td><td>";
		// 	 echo $worksheet->getCell('B'.$row)->getValue();
		// 	 echo "</td><tr>";
		// }
		// echo "</table>";

		// die();	

			return new Response('Se ingresaron los productos,cliente y proveedores.');  




    }	




}
