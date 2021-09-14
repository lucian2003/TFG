<?php

namespace GestionBundle\Controller;


use GestionBundle\Entity\planta;
use GestionBundle\Entity\modelo;
use GestionBundle\Entity\submodelo;
use GestionBundle\Entity\modelosubmodelo;
use GestionBundle\Entity\linea;
use GestionBundle\Entity\proceso;
use GestionBundle\Entity\asignarproceso;
use GestionBundle\Entity\asignarprocesore;
use GestionBundle\Entity\asignarprocesoversion;
use GestionBundle\Entity\configuracionlinea;
use GestionBundle\Entity\detalleconfiguracion;
use GestionBundle\Entity\version;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityRepository;

class ConfiguracionLineaController extends Controller
{
   
    public function indexAction(Request $request) {

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

        }

        if (isset($_POST['modelo']) && isset($_POST['submodelo']) && isset($_POST['linea'])) {


            $modelo = $_POST['modelo'];
            $submodelo = $_POST['submodelo'];
            $linea = $_POST['linea'];

            $back = 1;

            $user = $this->getUser();
            $planta = $user->getPlanta();

            $response = $this->render('GestionBundle:gestion/configuracionlinea:index_configuracionlinea.html.twig', array(
                'navconfiguracionlinea' => 1,
                'planta' =>$planta,
                'modelo' =>$modelo,
                'submodelo' =>$submodelo,
                'linea' =>$linea,
                'back' =>$back,
                'permiso' => $permiso
        ));

        } else {

            $modelo = 0;
            $submodelo = 0;
            $linea = 0;

            $user = $this->getUser();
            $planta = $user->getPlanta();

            $back = 0;

            $response = $this->render('GestionBundle:gestion/configuracionlinea:index_configuracionlinea.html.twig', array(
                'navconfiguracionlinea' => 1,
                'planta' =>$planta,
                'modelo' =>$modelo,
                'submodelo' =>$submodelo,
                'linea' =>$linea,
                'back' =>$back,
                'permiso' => $permiso
            ));

        }

        return $response;
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/configuracionlinea:index_configuracionlinea.html.twig');
    }


    public function getConfModelos(Request $request)
    {
        $area = $request->get('area');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM modelo WHERE area = '$area' AND active = 1 ORDER BY nombre ASC";
        $statement=$db->prepare($query);
        $statement->execute();
        $modelos=$statement->fetchAll();

        return $response = new JsonResponse(['data' => $modelos]);

    }


    public function getConfSubmodelos(Request $request)
    {
        $modelo = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT id_submodelo FROM modelosubmodelo WHERE id_modelo = $modelo ORDER BY id DESC";
        $statement=$db->prepare($query);
        $statement->execute();
        $id_submodelos=$statement->fetchAll();

        $submodelos_id = '';
        foreach ($id_submodelos as $value) {
            $submodelos_id .= $value['id_submodelo'] . ','; 
        }

        $submodelos_id = trim($submodelos_id, ',');

        $query2 = "SELECT * FROM submodelo WHERE id in ($submodelos_id) AND active = 1 ORDER BY nombre ASC";
        $statement2=$db->prepare($query2);
        //$params2=array($submodelos_id);
        $statement2->execute();
        $submodelos=$statement2->fetchAll();


        return $response = new JsonResponse(['data' => $submodelos]);

    }

    public function getConfLineas(Request $request)
    {
        $submodelo = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();


        $query = "SELECT id_linea FROM asignarproceso WHERE id_submodelo = $submodelo AND active = 1";
        $statement=$db->prepare($query);
        $statement->execute();
        $id_lineas=$statement->fetchAll();

        $lineas_id = '';
        foreach ($id_lineas as $value) {
            $lineas_id .= $value['id_linea'] . ',';
        }

        $lineas_id = trim($lineas_id, ',');

        $query2 = "SELECT * FROM linea WHERE id in ($lineas_id) AND active = 1 ORDER BY nombre ASC";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $lineas=$statement2->fetchAll();


        return $response = new JsonResponse(['data' => $lineas]);

    }


    public function searchAction(Request $request) {

        $planta=$_POST['planta'];
        $modelo=$_POST['modelo'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        //$proceso=$_POST['proceso'];
        $proceso=1;

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

        }

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $submodelo, 'idLinea'=> $linea, 'idProceso'=> $proceso]);

        $id_asignarproceso = $asignarproceso->getId();

        $configuracionlineas = $this->getDoctrine()
            ->getRepository('GestionBundle:configuracionlinea')
            ->findByid_asignarproceso($id_asignarproceso, array('version' => 'DESC'));


        return $this->render('GestionBundle:gestion/configuracionlinea:filter_configuracionlinea.html.twig', array(
                'configuracionlineas' => $configuracionlineas,
                'planta' => $planta,
                'modelo' => $modelo,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'idAsignarproceso' => $id_asignarproceso,
                'permiso' => $permiso
            ));


        }


    public function createAction(Request $request) {

        $planta=$_POST['planta'];
        $modelo=$_POST['modelo'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $proceso=$_POST['proceso'];
        $id_asignarproceso=$_POST['idAsignarproceso'];
        $operarios=$_POST['operarios'];
        $equiposxsemana=$_POST['eq_sem'];
        $estado=$_POST['estado'];

        $ids_asignarprocesoversion=[];
        $positions=[];
        $estadolt='';
        $tiempo_trabajo_semana=37.5*3600;

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $lineaseleccionada = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->find($linea);

        $estaciones = $lineaseleccionada->getEstaciones();
        $productividad = floatval($lineaseleccionada->getProductividad());


        //VER EL NUMERO DE LTs PARA ESTE ID_ASIGNARPROCESO
        $query2a = "SELECT id_asignarprocesoversion FROM asignarprocesore WHERE id_asignarproceso = $id_asignarproceso";
        $statement2a=$db->prepare($query2a);
        $statement2a->execute();
        $id_asignarprocesoversiones=$statement2a->fetchAll();

        $asignarprocesoversiones_id = '';
            foreach ($id_asignarprocesoversiones as $value) {
                $asignarprocesoversiones_id .= $value['id_asignarprocesoversion'] . ',';
            }

        $array_ids = trim($asignarprocesoversiones_id, ',');


        $query = "SELECT DISTINCT(nombre_lt) FROM asignarprocesoversion WHERE id IN ($array_ids)";
        $statement=$db->prepare($query);
        $statement->execute();
        $nombreslts=$statement->fetchAll();

        $cantidadlts=count($nombreslts);

        /**
        * CONDICIÓN: NÚMERO DE LTS (NOMBRES DE LTS DISTINTOS) < 1
        */
        if ($cantidadlts < 1) {

            //NINGUNA LT
            $response = new Response(
                'Content',
                Response::HTTP_OK,
                ['content-type' => 'text/html']
            );

            // ERROR 1
            $response->setContent('No hay ninguna LT con los datos seleccionados. [ERROR 1(createAction)]'); 

        } //FIN CONDICION LTS < 1


        /**
        * CONDICIÓN: NÚMERO DE LTS (NOMBRES DE LTS DISTINTOS) > 1
        */
        else if ($cantidadlts > 1) {

            //MÁS DE UNA LT SELECCIONADA
            $tiempo_tareas_detalle=0;

            // BUCLE FOR PARA RECORRER CADA UNO DE LOS NOMBRES DE LT Y COMPROBARL QUE ESTEN EN EL ESTADO SELECCIONADO
            for ($i=0; $i <$cantidadlts ; $i++) {  

                $nombre_lt = $nombreslts[$i]['nombre_lt'];


                $query = "SELECT estado FROM asignarprocesoversion WHERE nombre_lt = '$nombre_lt'";
                $statement=$db->prepare($query);
                $statement->execute();
                $estados=$statement->fetchAll();


                // CONDICION: ESTA LT TIENE UNA VERSIÓN QUE TENGA EL ESTADO ELECCIONADO?
                if (in_array($estado, array_column($estados, 'estado'))) {

                    $estadolt=$estado;

                // CONDICION: EL ESTADO SELECCIONADO ES "PRE-PRODUCCION" Y ALGUNA DE LAS VERSIONES DE ESTA LT ESTA EN ESTADO "PRODUCCION"?    
                } else if (($estado == "PRE-PRODUCCION") && (in_array('PRODUCCION', array_column($estados, 'estado')))) {

                    $estadolt="PRODUCCION";


                // NO HAGO NADA, PASO AL SIGUIENTE BUCLE    
                } else {

                    continue;

                }

                
                // ESTO SOLO OCURRE SI NO ESTOY EN EL CASO DE ~CONTINUE~ ANTERIOR
                $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->findOneBy(['nombre_lt'=> $nombre_lt, 'estado'=> $estadolt]);

                $id_asignarprocesoversion = $asignarprocesoversion->getId();

                $asignarprocesore = $em->getRepository('GestionBundle:asignarprocesore')->findOneBy(['idAsignarproceso'=> $id_asignarproceso, 'idAsignarprocesoversion'=> $id_asignarprocesoversion]);

                $position = $asignarprocesore->getPosition();


                $ids_asignarprocesoversion[] = $id_asignarprocesoversion;
                $positions[] = $position;

                
                $query3 = "SELECT SUM(tiempo) as sumatiempotareas FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
                $statement3=$db->prepare($query3);
                $statement3->execute();
                $tiempo_tareas=$statement3->fetchAll();

                $tiempo_tareas_detalle = $tiempo_tareas_detalle + $tiempo_tareas[0]['sumatiempotareas'];

            } // FIN DEL BUCLE FOR

            array_multisort($positions, $ids_asignarprocesoversion);


            // CONDICION: COMPROBAMOS SI EL ARRAY DE IDS ESTA VACIO
            if (empty($ids_asignarprocesoversion)) {

                //NINGUNA LT
                $response = new Response(
                    'Content',
                    Response::HTTP_OK,
                    ['content-type' => 'text/html']
                );

                // ERROR 2
                $response->setContent('No hay ninguna LT con los datos seleccionados. [ERROR 2(createAction)]');

            } else {

                //CALCULAMOS NUMERO DE OPERARIOS SI LO QUE SE HA INTRODUCIDO SON LOS EQUIPOS POR SEMANA
                if ($operarios == null) {

                    $tack_equipo_real=$tiempo_trabajo_semana/$equiposxsemana;
                    $tack_teorico=$tack_equipo_real/$productividad;
                    $operarios=(int)ceil($tiempo_tareas_detalle/$tack_teorico);

                } else {

                    //NO HAGO NADA, PORQUE YA TENGO OPERARIOS
                 }

                $query5 = "SELECT DISTINCT version FROM configuracionlinea WHERE id_asignarproceso = $id_asignarproceso AND operarios = $operarios AND estaciones = $estaciones ORDER BY version DESC";
                $statement5=$db->prepare($query5);
                $statement5->execute();
                $versiones=$statement5->fetchAll();
                

                // CONDICION: COMPROBAR SI HAY ALGUNA VERSION
                if ($versiones == null) {

                    $new_version = 1;
                    $version = $em->getRepository('GestionBundle:version')->find($new_version);
                    $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->find($id_asignarproceso);
                    $submodelo_object = $em->getRepository('GestionBundle:submodelo')->find($submodelo);
                    $linea_object = $em->getRepository('GestionBundle:linea')->find($linea);

                    $nombre_version = $version->getNombre();
                    $nombre_submodelo = $submodelo_object->getNombre();
                    $nombre_linea = $linea_object->getNombre();

                    $nombre_config = "CONFIG " . $nombre_version . "/" . $nombre_submodelo . "/" . $nombre_linea . "/" . $operarios . " OP";

                    $configuracionlinea = new configuracionlinea();

                    $configuracionlinea->setOperarios($operarios);
                    $configuracionlinea->setEstaciones($estaciones);
                    $configuracionlinea->setVersion($version);
                    $configuracionlinea->setIdAsignarproceso($asignarproceso);
                    $configuracionlinea->setEstado("DESARROLLO");
                    $configuracionlinea->setNombre($nombre_config);
                    

                    $em->persist($configuracionlinea);
                    $em->flush();

                    $id_configuracionlinea = $configuracionlinea->getId();


                        $response = $this->forward('GestionBundle:ConfiguracionLinea:createDetails', [
                        'planta' => $planta,
                        'modelo' => $modelo,
                        'submodelo' => $submodelo,
                        'linea' => $linea,
                        'proceso' => $proceso,
                        'id_asignarproceso' => $id_asignarproceso,
                        'operarios' => $operarios,
                        'estaciones' => $estaciones,
                        'id_configuracionlinea' => $id_configuracionlinea,
                        'estado' => $estadolt,
                        'ids_asignarprocesoversion' =>$ids_asignarprocesoversion
                    ]);
             

                } else {

                    $query13 = "SELECT id FROM configuracionlinea WHERE id_asignarproceso = $id_asignarproceso AND operarios = $operarios AND estaciones = $estaciones";
                    $statement13=$db->prepare($query13);
                    $statement13->execute();
                    $conf_igual=$statement13->fetchAll();

                    /*// compruebo si ya existe una configuracion con los mismos numeros de operarios y estaciones
                    if (!empty($conf_igual)) {

                        //YA EXISTE CONFIGURACION
                        $response = new Response(
                            'Content',
                            Response::HTTP_OK,
                            ['content-type' => 'text/html']
                        );

                        // ERROR 5
                        $response->setContent('Ya existe una configuración con el mismo número de operarios y de estaciones. [ERROR 5(createAction)]');

                        return $response;

                    } else {*/


                    $actual_version = $versiones[0]['version'];

                    $new_version = $actual_version + 1;

                    $version = $em->getRepository('GestionBundle:version')->find($new_version);
                    $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->find($id_asignarproceso);
                    $submodelo_object = $em->getRepository('GestionBundle:submodelo')->find($submodelo);
                    $linea_object = $em->getRepository('GestionBundle:linea')->find($linea);

                    $nombre_version = $version->getNombre();
                    $nombre_submodelo = $submodelo_object->getNombre();
                    $nombre_linea = $linea_object->getNombre();

                    $nombre_config = "CONFIG " . $nombre_version . "/" . $nombre_submodelo . "/" . $nombre_linea . "/" . $operarios . " OP";

                    $configuracionlinea = new configuracionlinea();

                    $configuracionlinea->setOperarios($operarios);
                    $configuracionlinea->setEstaciones($estaciones);
                    $configuracionlinea->setVersion($version);
                    $configuracionlinea->setIdAsignarproceso($asignarproceso);
                    $configuracionlinea->setEstado("DESARROLLO");
                    $configuracionlinea->setNombre($nombre_config);
                    

                    $em->persist($configuracionlinea);
                    $em->flush();

                    $id_configuracionlinea = $configuracionlinea->getId();


                        $response = $this->forward('GestionBundle:ConfiguracionLinea:createDetails', [
                        'planta' => $planta,
                        'modelo' => $modelo,
                        'submodelo' => $submodelo,
                        'linea' => $linea,
                        'proceso' => $proceso,
                        'id_asignarproceso' => $id_asignarproceso,
                        'operarios' => $operarios,
                        'estaciones' => $estaciones,
                        'id_configuracionlinea' => $id_configuracionlinea,
                        'estado' => $estadolt,
                        'ids_asignarprocesoversion' =>$ids_asignarprocesoversion
                        ]);

                    //} // fin else comprobar configuraciones con el mismo numero de operarios y estaciones

                } // FIN ELSE VERSION
                
            } // FIN ELSE EMPTY ARRAY


        } // FIN CONDICION NUMERO LTS > 1

        /**
        * CONDICIÓN: NÚMERO DE LTS (NOMBRES DE LTS DISTINTOS) = 1
        */
        else {

            // 1 LT SELECCIONADA

            $nombre_lt = $nombreslts[0]['nombre_lt'];

            $query6 = "SELECT estado FROM asignarprocesoversion WHERE nombre_lt = '$nombre_lt'";
            $statement6=$db->prepare($query6);
            $statement6->execute();
            $estados=$statement6->fetchAll();


            // CONDICION: ESTA LT TIENE UNA VERSIÓN QUE TENGA EL ESTADO ELECCIONADO?
            if (in_array($estado, array_column($estados, 'estado'))) {

                $estadolt=$estado;

            // CONDICION: EL ESTADO SELECCIONADO ES "PRE-PRODUCCION" Y ALGUNA DE LAS VERSIONES DE ESTA LT ESTA EN ESTADO "PRODUCCION"?    
            } else if (($estado == "PRE-PRODUCCION") && (in_array('PRODUCCION', array_column($estados, 'estado')))) {

                $estadolt="PRODUCCION";


            // NO HAGO NADA, RETURN   
            } else {

                //NINGUNA LT
                $response = new Response(
                    'Content',
                    Response::HTTP_OK,
                    ['content-type' => 'text/html']
                );

                // ERROR 3
                $response->setContent('No hay ninguna LT con los datos seleccionados. [ERROR 3(createAction)]');

                return $response;

            }


            $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->findOneBy(['nombre_lt'=> $nombre_lt, 'estado'=> $estadolt]);

            $id_asignarprocesoversion = $asignarprocesoversion->getId();

            $ids_asignarprocesoversion[] = $id_asignarprocesoversion;

            // CONDICION: COMPROBAMOS SI EL ARRAY DE IDS ESTA VACIO
            if (empty($ids_asignarprocesoversion)) {

                //NINGUNA LT
                $response = new Response(
                    'Content',
                    Response::HTTP_OK,
                    ['content-type' => 'text/html']
                );

                // ERROR 4
                $response->setContent('No hay ninguna LT con los datos seleccionados. [ERROR 4(createAction)]');

            } else {

                //tiempo de todas las tareas
                $query = "SELECT SUM(tiempo) as sumatiempostareas FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
                $statement=$db->prepare($query);
                $statement->execute();
                $tiempo_tareas=$statement->fetchAll();

                //tiempo de la primera tarea(MOVER EQUIPO)
                $query2 = "SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = 1";
                $statement2=$db->prepare($query2);
                $statement2->execute();
                $tiempo_tarea1=$statement2->fetchAll();

                //tiempo de la ultima tarea(TRAZABILIDAD)
                $query3 = "SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = (SELECT MAX(position) FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion)";
                $statement3=$db->prepare($query3);
                $statement3->execute();
                $tiempo_ultimatarea=$statement3->fetchAll();

                $tiempo_tareas_detalle=($tiempo_tareas[0]['sumatiempostareas']+($tiempo_tarea1[0]['tiempo']*((int)$estaciones-1))+($tiempo_ultimatarea[0]['tiempo']*((int)$estaciones-1)));

                //CALCULAMOS NUMERO DE OPERARIOS SI LO QUE SE HA INTRODUCIDO SON LOS EQUIPOS POR SEMANA
                if ($operarios == null) {

                $tack_teorico=$tiempo_trabajo_semana/$equiposxsemana;
                //$tack_teorico=$tack_equipo_real/$productividad;
                $operarios=(int)ceil($tiempo_tareas_detalle/$tack_teorico);

                } else {

                    //NO HAGO NADA, PORQUE YA TENGO OPERARIOS
                }


                $query4 = "SELECT DISTINCT version FROM configuracionlinea WHERE id_asignarproceso = $id_asignarproceso AND operarios = $operarios AND estaciones = $estaciones ORDER BY version DESC";
                $statement4=$db->prepare($query4);
                $statement4->execute();
                $versiones=$statement4->fetchAll();


                // CONDICION: COMPROBAR SI HAY ALGUNA VERSION
                if ($versiones == null) {

                    $new_version = 1;
                    $version = $em->getRepository('GestionBundle:version')->find($new_version);
                    $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->find($id_asignarproceso);
                    $submodelo_object = $em->getRepository('GestionBundle:submodelo')->find($submodelo);
                    $linea_object = $em->getRepository('GestionBundle:linea')->find($linea);

                    $nombre_version = $version->getNombre();
                    $nombre_submodelo = $submodelo_object->getNombre();
                    $nombre_linea = $linea_object->getNombre();

                    $nombre_config = "CONFIG " . $nombre_version . "/" . $nombre_submodelo . "/" . $nombre_linea . "/" . $operarios . " OP"; 

                    $configuracionlinea = new configuracionlinea();

                    $configuracionlinea->setOperarios($operarios);
                    $configuracionlinea->setEstaciones($estaciones);
                    $configuracionlinea->setVersion($version);
                    $configuracionlinea->setIdAsignarproceso($asignarproceso);
                    $configuracionlinea->setEstado("DESARROLLO");
                    $configuracionlinea->setNombre($nombre_config);
                    

                    $em->persist($configuracionlinea);
                    $em->flush();

                    $id_configuracionlinea = $configuracionlinea->getId();


                        $response = $this->forward('GestionBundle:ConfiguracionLinea:createDetails', [
                        'planta' => $planta,
                        'modelo' => $modelo,
                        'submodelo' => $submodelo,
                        'linea' => $linea,
                        'proceso' => $proceso,
                        'id_asignarproceso' => $id_asignarproceso,
                        'operarios' => $operarios,
                        'estaciones' => $estaciones,
                        'id_configuracionlinea' => $id_configuracionlinea,
                        'estado' => $estadolt,
                        'ids_asignarprocesoversion' =>$ids_asignarprocesoversion
                    ]);
             

                } else {

                    $query14 = "SELECT id FROM configuracionlinea WHERE id_asignarproceso = $id_asignarproceso AND operarios = $operarios AND estaciones = $estaciones";
                    $statement14=$db->prepare($query14);
                    $statement14->execute();
                    $conf_igual=$statement14->fetchAll();

                    /*// compruebo si ya existe una configuracion con los mismos numeros de operarios y estaciones
                    if (!empty($conf_igual)) {

                        //YA EXISTE CONFIGURACION
                        $response = new Response(
                            'Content',
                            Response::HTTP_OK,
                            ['content-type' => 'text/html']
                        );

                        // ERROR 6
                        $response->setContent('Ya existe una configuración con el mismo número de operarios y de estaciones. [ERROR 6(createAction)]');

                        return $response;

                    } else {*/

                    $actual_version = $versiones[0]['version'];

                    $new_version = $actual_version + 1;

                    $version = $em->getRepository('GestionBundle:version')->find($new_version);
                    $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->find($id_asignarproceso);
                    $submodelo_object = $em->getRepository('GestionBundle:submodelo')->find($submodelo);
                    $linea_object = $em->getRepository('GestionBundle:linea')->find($linea);

                    $nombre_version = $version->getNombre();
                    $nombre_submodelo = $submodelo_object->getNombre();
                    $nombre_linea = $linea_object->getNombre();

                    $nombre_config = "CONFIG " . $nombre_version . "/" . $nombre_submodelo . "/" . $nombre_linea . "/" . $operarios . " OP"; 

                    $configuracionlinea = new configuracionlinea();

                    $configuracionlinea->setOperarios($operarios);
                    $configuracionlinea->setEstaciones($estaciones);
                    $configuracionlinea->setVersion($version);
                    $configuracionlinea->setIdAsignarproceso($asignarproceso);
                    $configuracionlinea->setEstado("DESARROLLO");
                    $configuracionlinea->setNombre($nombre_config);
                    

                    $em->persist($configuracionlinea);
                    $em->flush();

                    $id_configuracionlinea = $configuracionlinea->getId();


                        $response = $this->forward('GestionBundle:ConfiguracionLinea:createDetails', [
                        'planta' => $planta,
                        'modelo' => $modelo,
                        'submodelo' => $submodelo,
                        'linea' => $linea,
                        'proceso' => $proceso,
                        'id_asignarproceso' => $id_asignarproceso,
                        'operarios' => $operarios,
                        'estaciones' => $estaciones,
                        'id_configuracionlinea' => $id_configuracionlinea,
                        'estado' => $estadolt,
                        'ids_asignarprocesoversion' =>$ids_asignarprocesoversion
                        ]);

                    //} // fin else comprobar configuraciones con el mismo numero de operarios y estaciones
                    
                } // FIN ELSE VERSION

            } // FIN ELSE EMPTY ARRAY

        } // FIN CONDICION NUMERO LTS > 1

        return $response;

    }


    public function createDetailsAction(Request $request, $id_configuracionlinea, $operarios, $estaciones, $id_asignarproceso, $estado, $ids_asignarprocesoversion) { //ajaxAction

        $planta=$_POST['planta'];
        $modelo=$_POST['modelo'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $proceso=$_POST['proceso'];
        
        
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $cantidadlts=count($ids_asignarprocesoversion);

        /**
        * CONDICIÓN: NÚMERO DE LTS (NOMBRES DE LTS DISTINTOS) > 1
        */
        if ($cantidadlts > 1) {

            $position_1=0;

            //BUCLE PARA RECORRER TODAS LAS LT Y SUS TAREAS ASIGNADAS
            for ($i=0; $i < $cantidadlts; $i++) { 

                $lts = $ids_asignarprocesoversion[$i];

                //CALCULO DEL NUMERO DE TAREAS DE CADA LT PARA SABER HASTA DONDE IR EN EL BUCLE
                $query_1 = "SELECT COUNT(*) as numero_tareas FROM tarea_asignada WHERE id_asignarprocesoversion = $lts";
                $statement_1=$db->prepare($query_1);
                $statement_1->execute();
                $hasta3=$statement_1->fetchAll();

                //BUCLE PARA RECORRER LAS TAREAS DE CADA LT Y INSERTARLAS EN DETALLECONFIGURACION (AHORA NO INSERTO NI EL CAMPO ESTACION, NI OPERARIO)
                for ($j=0; $j < ($hasta3[0]['numero_tareas']); $j++) { 

                    $position_1=$position_1+1;

                    $query_2 = "INSERT INTO detalleconfiguracion (id_configuracionLinea, id_tareaAsignada, estacion, position, tiempo, operario) VALUES ($id_configuracionlinea, (SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $lts AND position = $j+1), 0, $position_1, (SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $lts AND position = $j+1), 0)";
                    $statement_2=$db->prepare($query_2);
                    $statement_2->execute();
                }
                
            }

            //numero de tareas DETALLECONFIGURACION
            $query20 = "SELECT COUNT(*) as numerotareasdetalle FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea";
            $statement20=$db->prepare($query20);
            $statement20->execute();
            $totaltareasdetalle2=$statement20->fetchAll();

            //tiempo de todas las tareas DE DETALLECONFIGURACION
            $query21 = "SELECT SUM(tiempo) as tiempotareasdetalle FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea";
            $statement21=$db->prepare($query21);
            $statement21->execute();
            $tiempo_tareas_detalle2=$statement21->fetchAll();

            //array tiempos tareas DETALLECONFIGURACION
            $query22 = "SELECT tiempo FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea ORDER BY position";
            $statement22=$db->prepare($query22);
            $statement22->execute();
            $arraytiemposdetalle2=$statement22->fetchAll();

            //variables
            $tiempoacumulado3=$arraytiemposdetalle2[0]['tiempo'];
            $estacionactual2=0;
            $DE3=1;
            $HASTA3=0;
            $indice3=0;

            //CALCULO DEL TIEMPO POR estacion
            $tiempoxestacion2=$tiempo_tareas_detalle2[0]['tiempotareasdetalle']/$estaciones;

            for ($i=1; $i <= ($totaltareasdetalle2[0]['numerotareasdetalle'] + ($estaciones-1)); $i++) {

                if ($tiempoacumulado3 > $tiempoxestacion2) {

                    /*if ($tiempoacumulado2 > $tack_teorico_ok){

                      */

                    //UPDATE OPERARIO EN DETALLECONFIGURACION
                    $estacionactual2=$estacionactual2+1;

                    if ($estacionactual2 < $estaciones) {

                        $HASTA3 = $HASTA3+1;

                        for ($j=$DE3; $j<=$HASTA3+1; $j++) {

                            $query23 = "UPDATE detalleconfiguracion SET estacion = '$estacionactual2' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                            $statement23=$db->prepare($query23);
                            $statement23->execute();

                        }

                    } else {

                        for ($j=$DE3; $j<=$totaltareasdetalle2[0]['numerotareasdetalle']; $j++) {

                            $query25 = "UPDATE detalleconfiguracion SET estacion = '$estacionactual2' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                            $statement25=$db->prepare($query25);
                            $statement25->execute();

                        }
                    }

                    $DE3=$HASTA3+2;

                    $tiempoacumulado3=$arraytiemposdetalle2[$indice3]['tiempo'];


                } else {

                    $indice3=$indice3+1;

                    $HASTA3=$indice3;

                    //EVITO QUE BUSQUE UN INDICE SUPERIOR AL OFFSET
                    if ($indice3 < $totaltareasdetalle2[0]['numerotareasdetalle']) {

                        $tiempoacumulado3=$tiempoacumulado3+$arraytiemposdetalle2[$indice3]['tiempo'];

                    } else {

                        //INSERTO estacion DE LA ULTIMA ESTACION

                        if ($estaciones == 1){

                            $estacionactual2=1;

                        } else {

                            $estacionactual2=$estacionactual2+1;

                        }

                        for ($j=$DE3; $j<=($totaltareasdetalle2[0]['numerotareasdetalle']); $j++) {

                            $query24 = "UPDATE detalleconfiguracion SET estacion = '$estacionactual2' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                            $statement24=$db->prepare($query24);
                            $statement24->execute();
                        }

                    }
                    
                }

            }
  
        
        } 

        /**
        * CONDICIÓN: NÚMERO DE LTS (NOMBRES DE LTS DISTINTOS) = 1
        */
        else {


            $id_asignarprocesoversion = $ids_asignarprocesoversion[0];

            //tiempo de todas las tareas
            $query = "SELECT SUM(tiempo) as tiempotareas FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
            $statement=$db->prepare($query);
            $statement->execute();
            $tiempo_tareas=$statement->fetchAll();

            //tiempo de la primera tarea(MOVER EQUIPO)
            $query2 = "SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = 1";
            $statement2=$db->prepare($query2);
            $statement2->execute();
            $tiempo_tarea1=$statement2->fetchAll();

            //tiempo de la ultima tarea(TRAZABILIDAD)
            $query3 = "SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = (SELECT MAX(position) FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion)";
            $statement3=$db->prepare($query3);
            $statement3->execute();
            $tiempo_ultimatarea=$statement3->fetchAll();

            //CALCULO DEL TIEMPO POR ESTACION
            $tiempoxestacion=($tiempo_tareas[0]['tiempotareas']+($tiempo_tarea1[0]['tiempo']*((int)$estaciones-1))+($tiempo_ultimatarea[0]['tiempo']*((int)$estaciones-1)))/(int)$estaciones;

            //numero de tareas
            $query4 = "SELECT COUNT(*) as numerotareas FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
            $statement4=$db->prepare($query4);
            $statement4->execute();
            $totaltareas=$statement4->fetchAll();

            //array tiempos tareas
            $query5 = "SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion ORDER BY position";
            $statement5=$db->prepare($query5);
            $statement5->execute();
            $arraytiempos=$statement5->fetchAll();


            $tiempoacumulado=0;
            $DE=2;
            $HASTA=0;
            $indice=0;
            $estacionactual=0;
            $posinicial=0;

            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //REPARTICION POR ESTACION

            /**
            * BUCLE FOR: RECORRER TODAS LAS TAREAS + (EL NUMERO DE ESTACIONES -1)
            */
            for ($i=1; $i < ($totaltareas[0]['numerotareas'] + ($estaciones-1)); $i++) { 

                if ((($tiempo_tarea1[0]['tiempo']+$tiempo_ultimatarea[0]['tiempo']+$tiempoacumulado) > $tiempoxestacion) || ($i == ($totaltareas[0]['numerotareas']))) {

                    $estacionactual=$estacionactual+1;
                    
                    $posinicial=$posinicial+1;

                    $query7 = "INSERT INTO detalleconfiguracion (id_configuracionLinea, id_tareaAsignada, estacion, position, tiempo, operario) VALUES ($id_configuracionlinea, (SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = 1), $estacionactual, $posinicial, (SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = 1), 0)";
                    $statement7=$db->prepare($query7);
                    $statement7->execute();

                    //INSERTAR TAREAS QUE NO SEAN LA PRIMERA Y LA ULTIMA
                    for ($j=$DE; $j<=$HASTA; $j++) { 

                        $posinicial=$posinicial+1;

                        $query9 = "INSERT INTO detalleconfiguracion (id_configuracionLinea, id_tareaAsignada, estacion, position, tiempo, operario) VALUES ($id_configuracionlinea, (SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = $j), $estacionactual, $posinicial, (SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = $j), 0)";
                        $statement9=$db->prepare($query9);
                        $statement9->execute();
                    }

                    //COMPROBAMOS SI ES LA ULTIMA ESTACION
                    if ($estacionactual<$estaciones){

                        //INSERTAMOS LA ULTIMA TAREA (TRAZABILIDAD)
                        $posinicial=$posinicial+1;

                        $query8 = "INSERT INTO detalleconfiguracion (id_configuracionLinea, id_tareaAsignada, estacion, position, tiempo, operario) VALUES ($id_configuracionlinea, (SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = (SELECT MAX(position) FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion)), $estacionactual, $posinicial, (SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = (SELECT MAX(position) FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion)), 0)";
                        $statement8=$db->prepare($query8);
                        $statement8->execute();

                    } else {

                        //INSERTAMOS LAS TAREAS RESTANTESE EN LA ULTIMA ESTACION
                        for ($k=$HASTA+1; $k<($totaltareas[0]['numerotareas']); $k++) { 

                            $posinicial=$posinicial+1;    

                            $query10 = "INSERT INTO detalleconfiguracion (id_configuracionLinea, id_tareaAsignada, estacion, position, tiempo, operario) VALUES ($id_configuracionlinea, (SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = $k), $estacionactual, $posinicial, (SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = $k), 0)";
                            $statement10=$db->prepare($query10);
                            $statement10->execute();
                        }

                        //INSERTAMOS LA ULTIMA TAREA (TRAZABILIDAD)
                        $posinicial=$posinicial+1;

                        $query_8 = "INSERT INTO detalleconfiguracion (id_configuracionLinea, id_tareaAsignada, estacion, position, tiempo, operario) VALUES ($id_configuracionlinea, (SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = (SELECT MAX(position) FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion)), $estacionactual, $posinicial, (SELECT tiempo FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion AND position = (SELECT MAX(position) FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion)), 0)";
                        $statement_8=$db->prepare($query_8);
                        $statement_8->execute();

                        $i=($totaltareas[0]['numerotareas'] + ($estaciones-1));
                    }

                    $DE=$HASTA+1;
                    $indice=$indice+1;

                    $tiempoacumulado=$arraytiempos[$indice-1]['tiempo'];
                    

                } else {

                    $indice=$indice+1;

                    $HASTA=$indice;

                    //EVITO QUE BUSQUE UN INDICE SUPERIOR AL OFFSET
                    if ($indice < $totaltareas[0]['numerotareas']) {

                        $tiempoacumulado=$tiempoacumulado+$arraytiempos[$indice-1]['tiempo'];

                    } else {

                        //NO HAGO NADA...OFFSET INEXISTENTE (MAYOR)
                    }
       
                }
            }

        }
            
            //numero de tareas DETALLECONFIGURACION
            $query10 = "SELECT COUNT(*) as numerotareasdetalle FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea";
            $statement10=$db->prepare($query10);
            $statement10->execute();
            $totaltareasdetalle=$statement10->fetchAll();

            //tiempo de todas las tareas DE DETALLECONFIGURACION
            $query11 = "SELECT SUM(tiempo) as tiempotareasdetalle FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea";
            $statement11=$db->prepare($query11);
            $statement11->execute();
            $tiempo_tareas_detalle=$statement11->fetchAll();

            //array tiempos tareas DETALLECONFIGURACION
            $query12 = "SELECT tiempo FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea ORDER BY position";
            $statement12=$db->prepare($query12);
            $statement12->execute();
            $arraytiemposdetalle=$statement12->fetchAll();

            //ACTUALIZAR CAMPO OPERARIO EN LA TABLA DE DETALLESLINEA
            $tiempoacumulado2=$arraytiemposdetalle[0]['tiempo'];
            $operarioactual=0;
            $DE2=1;
            $HASTA2=0;
            $indice2=0;
            //$tack_teorico_ok=0;

            //CALCULO DEL TIEMPO POR OPERARIO
            $tiempoxoperario=$tiempo_tareas_detalle[0]['tiempotareasdetalle']/$operarios;

            for ($i=1; $i <= $totaltareasdetalle[0]['numerotareasdetalle']; $i++) {

                if ($tiempoacumulado2 > $tiempoxoperario) {

                    //UPDATE OPERARIO EN DETALLECONFIGURACION
                    $operarioactual=$operarioactual+1;

                    if ($operarioactual < $operarios) {

                        for ($j=$DE2; $j<=$HASTA2+1; $j++) {

                            $query13 = "UPDATE detalleconfiguracion SET operario = '$operarioactual' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                            $statement13=$db->prepare($query13);
                            $statement13->execute();

                        }

                    } else {

                        for ($j=$DE2; $j<=$totaltareasdetalle[0]['numerotareasdetalle']; $j++) {

                            $query15 = "UPDATE detalleconfiguracion SET operario = '$operarioactual' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                            $statement15=$db->prepare($query15);
                            $statement15->execute();

                        }
                    }

                    $DE2=$HASTA2+2;

                    $indice2=$indice2+1;
                    $HASTA2=$indice2;

                    $tiempoacumulado2=$arraytiemposdetalle[$indice2]['tiempo'];


                } else {

                    $indice2=$indice2+1;

                    $HASTA2=$indice2;

                    //EVITO QUE BUSQUE UN INDICE SUPERIOR AL OFFSET
                    if ($indice2 < ($totaltareasdetalle[0]['numerotareasdetalle'])) {

                        $tiempoacumulado2=$tiempoacumulado2+$arraytiemposdetalle[$indice2]['tiempo'];

                    } else {

                        //INSERTO OPERARIO DE LA ULTIMA ESTACION

                        if ($operarios == 1){

                            $operarioactual=1;

                        } else {

                            $operarioactual=$operarioactual+1;

                        }

                        for ($j=$DE2; $j<=$totaltareasdetalle[0]['numerotareasdetalle']; $j++) {

                            $query14 = "UPDATE detalleconfiguracion SET operario = '$operarioactual' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                            $statement14=$db->prepare($query14);
                            $statement14->execute();
                        }

                    }
                    
                }

            }

        $response = $this->forward('GestionBundle:ConfiguracionLinea:search', [
            'planta' => $planta,
            'modelo' => $modelo,
            'submodelo' => $submodelo,
            'linea' => $linea,
            'proceso' => $proceso
        ]);

        return $response;

    }


    public function detailsAction(Request $request) {

        $id_planta=$_POST['planta'];
        $id_modelo=$_POST['modelo'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $planta = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findOneById($id_planta);

        $modelo = $this->getDoctrine()
            ->getRepository('GestionBundle:modelo')
            ->findOneById($id_modelo);

        $submodelo = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->findOneById($id_submodelo);

        $linea = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->findOneById($id_linea);

        $proceso = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findOneById($id_proceso);

        $configuracionlinea = $this->getDoctrine()
            ->getRepository('GestionBundle:configuracionlinea')
            ->findOneById($id_configuracionLinea);


        return $this->render('GestionBundle:gestion/configuracionlinea:details_configuracionlinea.html.twig', array(
                'planta' => $planta,
                'modelo' => $modelo,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'configuracionlinea' => $configuracionlinea
            ));


        }

    public function ajaxAction(Request $request) {

        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

        }

        $detalleconfiguracions = $this->getDoctrine()
            ->getRepository('GestionBundle:detalleconfiguracion')
            ->findByid_configuracionlinea($id_configuracionLinea, array('position' => 'ASC'));

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT COUNT(DISTINCT(operario)) as numero_operarios FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea";
        $statement=$db->prepare($query);
        $statement->execute();
        $numero_operarios=$statement->fetchAll();

        $operarios = $numero_operarios[0]['numero_operarios'];
        $tack_teorico_oficial=0;

        if ($operarios == 1) {

            $query4 = "SELECT id_tareaAsignada FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea";
            $statement4=$db->prepare($query4);
            $statement4->execute();
            $id_tareas=$statement4->fetchAll();

            $tiempo_acumulado_tareas=0;

            $numero_tareas=COUNT($id_tareas);

            for ($j=0; $j < $numero_tareas; $j++) { 

                $idtarea=$id_tareas[$j]['id_tareaAsignada'];
                    
                $query5 = "SELECT tiempo FROM tarea_asignada WHERE id = $idtarea";
                $statement5=$db->prepare($query5);
                $statement5->execute();
                $tiempotarea=$statement5->fetchAll();

                $tiempo_acumulado_tareas=$tiempo_acumulado_tareas+$tiempotarea[0]['tiempo'];

            }

            $tack_teorico_oficial = $tiempo_acumulado_tareas;


        } else {

            for ($i=1; $i<=$operarios ; $i++) { 
                
                $query2 = "SELECT id_tareaAsignada FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea AND operario = '$i'";
                $statement2=$db->prepare($query2);
                $statement2->execute();
                $id_tareas=$statement2->fetchAll();

                $tiempo_acumulado_tareas=0;

                $numero_tareas=COUNT($id_tareas);

                for ($j=0; $j < $numero_tareas; $j++) { 

                    $idtarea=$id_tareas[$j]['id_tareaAsignada'];
                    
                    $query3 = "SELECT tiempo FROM tarea_asignada WHERE id = $idtarea";
                    $statement3=$db->prepare($query3);
                    $statement3->execute();
                    $tiempotarea=$statement3->fetchAll();

                    $tiempo_acumulado_tareas=$tiempo_acumulado_tareas+$tiempotarea[0]['tiempo'];

                }
               
                if ($tiempo_acumulado_tareas > $tack_teorico_oficial) { 

                    $tack_teorico_oficial = $tiempo_acumulado_tareas;
                }

            }

        }

            $configuracionlinea = $this->getDoctrine()
            ->getRepository('GestionBundle:configuracionlinea')
            ->find($id_configuracionLinea);

            $equipos_semana_oficial=37.5*3600/$tack_teorico_oficial;
            $eq_semana_oficial=bcdiv($equipos_semana_oficial, '1', 2);

            $configuracionlinea->setTackTeorico($tack_teorico_oficial);
            $configuracionlinea->setEqSemana($eq_semana_oficial);

            $em->persist($configuracionlinea);
            $em->flush();

            $tiempo_operarios=[];

            for ($i=1; $i<=$operarios; $i++) { 
                
            //tiempo de todas las tareas DE DETALLECONFIGURACION por OPERARIO
            $query6 = "SELECT SUM(tiempo) as tiempotareasoperario FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea AND operario = $i";
            $statement6=$db->prepare($query6);
            $statement6->execute();
            $tiempo_tareas_operario=$statement6->fetchAll();

            $tiempo_operarios[] = $tiempo_tareas_operario[0]['tiempotareasoperario'];

            }


        return $this->render('GestionBundle:gestion/configuracionlinea:ajax_configuracionlinea.html.twig', array(
                'detalleconfiguracions' => $detalleconfiguracions,
                'idConfiguracionLinea' => $id_configuracionLinea,
                'tiempo_operarios' => $tiempo_operarios,
                'permiso' => $permiso
            ));

    }

    public function updateAction(Request $request) {

        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

            $em = $this->getDoctrine()->getManager();

            if (isset($_POST['update'])) {
            foreach($_POST['positions'] as $position) {
               $index = $position[0];
               $newPosition = $position[1];

               $em = $this->getDoctrine()->getManager();
               $db = $em->getConnection();

               $query = "UPDATE detalleconfiguracion SET position = '$newPosition' WHERE id='$index'";
               $statement=$db->prepare($query);
               $statement->execute();

                }
            }

        } //endif permiso

        $detalleconfiguracions = $this->getDoctrine()
            ->getRepository('GestionBundle:detalleconfiguracion')
            ->findByid_configuracionlinea($id_configuracionLinea, array('position' => 'ASC'));

        $query = "SELECT COUNT(DISTINCT(operario)) as numero_operarios FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea";
        $statement=$db->prepare($query);
        $statement->execute();
        $numero_operarios=$statement->fetchAll();

        $operarios = $numero_operarios[0]['numero_operarios'];

        $tiempo_operarios=[];

            for ($i=1; $i<=$operarios; $i++) { 
                
            //tiempo de todas las tareas DE DETALLECONFIGURACION por OPERARIO
            $query6 = "SELECT SUM(tiempo) as tiempotareasoperario FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea AND operario = $i";
            $statement6=$db->prepare($query6);
            $statement6->execute();
            $tiempo_tareas_operario=$statement6->fetchAll();

            $tiempo_operarios[] = $tiempo_tareas_operario[0]['tiempotareasoperario'];

            }

        return $this->render('GestionBundle:gestion/configuracionlinea:ajax_configuracionlinea.html.twig', array(
                'detalleconfiguracions' => $detalleconfiguracions,
                'idConfiguracionLinea' => $id_configuracionLinea,
                'tiempo_operarios' => $tiempo_operarios,
                'permiso' => $permiso
            ));

    }

    public function editAction(Request $request) {

        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $value_estacion=$_POST['value_estacion'];
        $value_operario=$_POST['value_operario'];
        $value_position=$_POST['value_position'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
       
            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $query = "UPDATE detalleconfiguracion SET estacion = '$value_estacion', operario = '$value_operario' WHERE id_configuracionlinea = $id_configuracionLinea AND position ='$value_position'";
            $statement=$db->prepare($query);
            $statement->execute();

        } //endif permiso


            $detalleconfiguracions = $this->getDoctrine()
                ->getRepository('GestionBundle:detalleconfiguracion')
                ->findByid_configuracionlinea($id_configuracionLinea, array('position' => 'ASC'));

            $query7 = "SELECT DISTINCT(operario) FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea";
            $statement7=$db->prepare($query7);
            $statement7->execute();
            $numoperarios=$statement7->fetchAll();

            $operarios=count($numoperarios);

            $tiempo_operarios=[];

            for ($i=1; $i<=$operarios; $i++) { 
                    
            //tiempo de todas las tareas DE DETALLECONFIGURACION por OPERARIO
            $query6 = "SELECT SUM(tiempo) as tiempotareasoperario FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea AND operario = $i";
            $statement6=$db->prepare($query6);
            $statement6->execute();
            $tiempo_tareas_operario=$statement6->fetchAll();

            $tiempo_operarios[] = $tiempo_tareas_operario[0]['tiempotareasoperario'];

            }

        return $this->render('GestionBundle:gestion/configuracionlinea:ajax_configuracionlinea.html.twig', array(
                'detalleconfiguracions' => $detalleconfiguracions,
                'idConfiguracionLinea' => $id_configuracionLinea,
                'tiempo_operarios' => $tiempo_operarios,
                'permiso' => $permiso
            ));

    }

    public function ltsAction(Request $request) {

        $planta=$_POST['planta'];
        $modelo=$_POST['modelo'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        //$proceso=$_POST['proceso'];
        $proceso=1;

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $submodelo, 'idLinea'=> $linea, 'idProceso'=> $proceso]);

        $id_asignarproceso = $asignarproceso->getId();

        $query2 = "SELECT id_asignarprocesoversion FROM asignarprocesore WHERE id_asignarproceso = $id_asignarproceso";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $id_asignarprocesoversiones=$statement2->fetchAll();

        $asignarprocesoversiones_id = '';
            foreach ($id_asignarprocesoversiones as $value) {
                $asignarprocesoversiones_id .= $value['id_asignarprocesoversion'] . ',';
            }

        $array_ids = trim($asignarprocesoversiones_id, ',');


        $query = "SELECT DISTINCT(nombre_lt) FROM asignarprocesoversion WHERE id IN ($array_ids) AND active = 1 GROUP BY nombre_lt";
        $statement=$db->prepare($query);
        $statement->execute();
        $asignarprocesoversions=$statement->fetchAll();

        return $this->render('GestionBundle:gestion/configuracionlinea:ajax_lts_configuracionlinea.html.twig', array(
                'asignarprocesoversions' => $asignarprocesoversions
            ));

    }

    public function duplicateAction(Request $request) {

        $id_planta=$_POST['planta'];
        $id_modelo=$_POST['modelo'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_configuracionLinea_actual=$_POST['idConfiguracionLinea'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;


            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

            $id_asignarproceso = $asignarproceso->getId();

            $configuracionLinea_actual = $this->getDoctrine()
                ->getRepository('GestionBundle:configuracionlinea')
                ->find($id_configuracionLinea_actual);

            $operarios = $configuracionLinea_actual->getOperarios();
            $estaciones = $configuracionLinea_actual->getEstaciones();
            $version = $configuracionLinea_actual->getVersion();
            $actual_version = $version->getId();
            $actual_tack = $configuracionLinea_actual->getTackTeorico();
            $actual_eq_semana = $configuracionLinea_actual->getEqSemana();

            $estado = $configuracionLinea_actual->getEstado();

            if ($estado == "DESARROLLO") {

                $message = ('Ya existe una versión en desarrollo.');

                return $this->render('GestionBundle:gestion:error.html.twig', array(
                        'message' => $message
                ));
                
            } else {

                $new_version = $actual_version + 1;

                $version = $em->getRepository('GestionBundle:version')->find($new_version);
                $submodelo_object = $em->getRepository('GestionBundle:submodelo')->find($id_submodelo);
                $linea_object = $em->getRepository('GestionBundle:linea')->find($id_linea);

                $nombre_version = $version->getNombre();
                $nombre_submodelo = $submodelo_object->getNombre();
                $nombre_linea = $linea_object->getNombre();

                $nombre_config = "CONFIG " . $nombre_version . "/" . $nombre_submodelo . "/" . $nombre_linea . "/" . $operarios . " OP";
                    

                $configuracionlinea = new configuracionlinea();

                $configuracionlinea->setIdAsignarproceso($asignarproceso);
                $configuracionlinea->setVersion($version);
                $configuracionlinea->setEstado("DESARROLLO");
                $configuracionlinea->setOperarios($operarios);
                $configuracionlinea->setEstaciones($estaciones);
                $configuracionlinea->setTackTeorico($actual_tack);
                $configuracionlinea->setEqSemana($actual_eq_semana);
                $configuracionlinea->setNombre($nombre_config);


                $em->persist($configuracionlinea);
                $em->flush();


                $id_configuracionlinea = $configuracionlinea->getId();

                $query2 = "SELECT * FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea_actual";
                $statement2=$db->prepare($query2);
                $statement2->execute();
                $datos=$statement2->fetchAll();


                if ($datos == null) {

                } else {

                    //COPIAMOS TODAS LAS TAREAS ASIGNADAS A LA VERSION ACTUAL DE LA LT
                    $query3 = "INSERT INTO detalleconfiguracion (id_configuracionlinea, estacion, operario, tiempo, position, id_tareaAsignada) VALUES ";
                    foreach ($datos as $key=>$value) {
                        $query3 = $query3 . "(".$id_configuracionlinea .",". $value['estacion'] . ",". $value['operario'] . ",". $value['tiempo']. ",".$value['position'].",". $value['id_tareaAsignada'] . "),";
                    }

                    $query3 = trim($query3, ','); 

                    $statement3=$db->prepare($query3);
                    $statement3->execute();

                }
            }

        } //endif permiso
        

        $response = $this->forward('GestionBundle:ConfiguracionLinea:index');

        return $response;

    }

    public function oficializarConfiguracion(Request $request) {

        $id_planta=$_POST['planta'];
        $id_modelo=$_POST['modelo'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();


            $configuracionlinea = $em->getRepository('GestionBundle:configuracionlinea')->find($id_configuracionLinea);

            $estado = $configuracionlinea->getEstado();

            if ($estado == "DESARROLLO") {
        
            return $this->render('GestionBundle:gestion/configuracionlinea:oficializar_configuracionlinea.html.twig', array(
                'planta' =>$id_planta,
                'modelo' =>$id_modelo,
                'submodelo' =>$id_submodelo,
                'linea' =>$id_linea,
                'proceso' =>$id_proceso,
                'idConfiguracionLinea' =>$id_configuracionLinea
            ));

            } else {

                $message = ('Solo se pueden oficializar las versiones en DESARROLLO.');

                return $this->render('GestionBundle:gestion:error.html.twig', array(
                        'message' => $message
                ));
            }

        } //endif permiso

    }

    public function getFecha(Request $request) {

        $fecha=$_POST['fecha'];
        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $id_planta=$_POST['planta'];
        $id_modelo=$_POST['modelo'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
        
            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();


            $configuracionlinea = $em->getRepository('GestionBundle:configuracionlinea')->find($id_configuracionLinea);

            $configuracionlinea->setFechaInicio(new \DateTime($fecha));
            $configuracionlinea->setEstado("PRE-PRODUCCION");

            $em->persist($configuracionlinea);
            $em->flush();

        } //endif permiso
    
        $response = $this->forward('GestionBundle:ConfiguracionLinea:index', array(
                'planta' => $id_planta,
                'modelo' =>$id_modelo,
                'submodelo' => $id_submodelo,
                'linea' => $id_linea,
                'proceso' => $id_proceso

            ));

    return $response;

    }

    public function duplicate2Action(Request $request) {

        $id_planta=$_POST['planta'];
        $id_modelo=$_POST['modelo'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_configuracionLinea_actual=$_POST['idConfiguracionLinea'];
        $operarios=$_POST['op'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

            $configuracionLinea_actual = $this->getDoctrine()
                ->getRepository('GestionBundle:configuracionlinea')
                ->find($id_configuracionLinea_actual);

            $estaciones = $configuracionLinea_actual->getEstaciones();
            $version = $configuracionLinea_actual->getVersion();
            $actual_version = $version->getId();
            $actual_tack = $configuracionLinea_actual->getTackTeorico();
            $actual_eq_semana = $configuracionLinea_actual->getEqSemana();
            $actual_nombre = $configuracionLinea_actual->getNombre();

            $query = "SELECT * FROM configuracionlinea WHERE nombre = '$actual_nombre' AND estaciones = $estaciones AND operarios = $operarios";
            $statement=$db->prepare($query);
            $statement->execute();
            $configs=$statement->fetchAll();

            if (!empty($configs)) {

                // ERROR 7
                $message = ('Ya existe una configuración con los datos seleccionados. [ERROR 7(createAction)]');

                return $this->render('GestionBundle:gestion:error.html.twig', array(
                        'message' => $message
                ));
                
            } else {


                $new_version = 1;
                $version = $em->getRepository('GestionBundle:version')->find($new_version);
                $submodelo_object = $em->getRepository('GestionBundle:submodelo')->find($id_submodelo);
                $linea_object = $em->getRepository('GestionBundle:linea')->find($id_linea);

                $nombre_version = $version->getNombre();
                $nombre_submodelo = $submodelo_object->getNombre();
                $nombre_linea = $linea_object->getNombre();

                $nombre_config = "CONFIG " . $nombre_version . "/" . $nombre_submodelo . "/" . $nombre_linea . "/" . $operarios . " OP";


                $configuracionlinea = new configuracionlinea();

                $configuracionlinea->setIdAsignarproceso($asignarproceso);
                $configuracionlinea->setVersion($version);
                $configuracionlinea->setEstado("DESARROLLO");
                $configuracionlinea->setOperarios($operarios);
                $configuracionlinea->setEstaciones($estaciones);
                $configuracionlinea->setNombre($nombre_config);


                $em->persist($configuracionlinea);
                $em->flush();


                $id_configuracionlinea = $configuracionlinea->getId();

                $query2 = "SELECT * FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionLinea_actual";
                $statement2=$db->prepare($query2);
                $statement2->execute();
                $datos=$statement2->fetchAll();

                if (empty($datos)) {

                    } else {

                        //COPIAMOS TODAS LAS TAREAS ASIGNADAS A LA VERSION ACTUAL DE LA LT
                        
                        $query3 = "INSERT INTO detalleconfiguracion (id_configuracionlinea, estacion, operario, tiempo, position, id_tareaAsignada) VALUES ";
                        foreach ($datos as $key=>$value) {
                            $query3 = $query3 . "(".$id_configuracionlinea .",". $value['estacion'] . ",". 0 . ",". $value['tiempo']. ",".$value['position'].",". $value['id_tareaAsignada'] . "),";
                        }

                        $query3 = trim($query3, ','); 

                        $statement3=$db->prepare($query3);
                        $statement3->execute();

                    }

                    
                    //numero de tareas DETALLECONFIGURACION
                    $query10 = "SELECT COUNT(*) as numerotareasdetalle FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea";
                    $statement10=$db->prepare($query10);
                    $statement10->execute();
                    $totaltareasdetalle=$statement10->fetchAll();

                    //tiempo de todas las tareas DE DETALLECONFIGURACION
                    $query11 = "SELECT SUM(tiempo) as tiempotareasdetalle FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea";
                    $statement11=$db->prepare($query11);
                    $statement11->execute();
                    $tiempo_tareas_detalle=$statement11->fetchAll();

                    //array tiempos tareas DETALLECONFIGURACION
                    $query12 = "SELECT tiempo FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracionlinea ORDER BY position";
                    $statement12=$db->prepare($query12);
                    $statement12->execute();
                    $arraytiemposdetalle=$statement12->fetchAll();


                    //ACTUALIZAR CAMPO OPERARIO EN LA TABLA DE DETALLESLINEA
                    $tiempoacumulado2=$arraytiemposdetalle[0]['tiempo'];
                    $operarioactual=0;
                    $DE2=1;
                    $HASTA2=0;
                    $indice2=0;
                    //$tack_teorico_ok=0;


                    //CALCULO DEL TIEMPO POR OPERARIO
                    $tiempoxoperario=$tiempo_tareas_detalle[0]['tiempotareasdetalle']/$operarios;

                    for ($i=1; $i <= $totaltareasdetalle[0]['numerotareasdetalle']; $i++) {

                        if ($tiempoacumulado2 > $tiempoxoperario) {

                            //UPDATE OPERARIO EN DETALLECONFIGURACION
                            $operarioactual=$operarioactual+1;

                            if ($operarioactual < $operarios) {

                                //$HASTA2 = $HASTA2+1;

                                for ($j=$DE2; $j<=$HASTA2+2; $j++) {

                                    $query13 = "UPDATE detalleconfiguracion SET operario = '$operarioactual' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                                    $statement13=$db->prepare($query13);
                                    $statement13->execute();

                                }

                            } else {

                                for ($j=$DE2; $j<=$totaltareasdetalle[0]['numerotareasdetalle']; $j++) {

                                    $query15 = "UPDATE detalleconfiguracion SET operario = '$operarioactual' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                                    $statement15=$db->prepare($query15);
                                    $statement15->execute();

                                }
                            }

                            $DE2=$HASTA2+3;

                            $indice2=$indice2+1;
                            $HASTA2=$indice2;

                            $tiempoacumulado2=$arraytiemposdetalle[$indice2]['tiempo'];


                        } else {

                            $indice2=$indice2+1;

                            $HASTA2=$indice2;

                            //EVITO QUE BUSQUE UN INDICE SUPERIOR AL OFFSET
                            if ($indice2 < ($totaltareasdetalle[0]['numerotareasdetalle'])) {

                                $tiempoacumulado2=$tiempoacumulado2+$arraytiemposdetalle[$indice2]['tiempo'];

                            } else {

                                //INSERTO OPERARIO DE LA ULTIMA ESTACION
                                if ($operarios == 1){

                                    $operarioactual=1;

                                } else {

                                    $operarioactual=$operarioactual+1;

                                }

                                for ($j=$DE2; $j<=$totaltareasdetalle[0]['numerotareasdetalle']; $j++) {

                                    $query14 = "UPDATE detalleconfiguracion SET operario = '$operarioactual' WHERE id_configuracionlinea = '$id_configuracionlinea' AND position = '$j'";
                                    $statement14=$db->prepare($query14);
                                    $statement14->execute();
                                }

                            }
                            
                        }

                    }
                }

            } //endif permiso


    $response = $this->forward('GestionBundle:ConfiguracionLinea:index');

        return $response;

    }


    public function deleteAction(Request $request) {

        
        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;


            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $qb = $em->createQueryBuilder();

            //elimino el listado de detalleconfiguracion
            $query = $qb->delete('GestionBundle:detalleconfiguracion', 'detalleconfiguracion')
                        ->where('detalleconfiguracion.idConfiguracionlinea = :configuracionlinea_id')
                        ->setParameter('configuracionlinea_id', $id_configuracionLinea)
                        ->getQuery();

            $query->execute();

            //elimino la configuracion
            $query2 = $qb->delete('GestionBundle:configuracionlinea', 'configuracionlinea')
                        ->where('configuracionlinea.id = :configuracionlinea_id')
                        ->setParameter('configuracionlinea_id', $id_configuracionLinea)
                        ->getQuery();

            $query2->execute();

        } //endif permiso


        $response = $this->forward('GestionBundle:ConfiguracionLinea:index');

        return $response;

    }    

}