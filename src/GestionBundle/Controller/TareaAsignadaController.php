<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\planta;
use GestionBundle\Entity\modelo;
use GestionBundle\Entity\submodelo;
use GestionBundle\Entity\linea;
use GestionBundle\Entity\proceso;
use GestionBundle\Entity\tarea;
use GestionBundle\Entity\tareaAsignada;
use GestionBundle\Entity\asignarproceso;
use GestionBundle\Entity\asignarprocesore;
use GestionBundle\Entity\asignarprocesoversion; 
use GestionBundle\Entity\operacionAsignada;
use GestionBundle\Entity\amfe;
use GestionBundle\Entity\tarea_amfe;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;

use Doctrine\ORM\EntityRepository;

class TareaAsignadaController extends Controller
{
   
    public function indexAction(Request $request) {


        if (isset($_POST['submodelo']) && isset($_POST['linea']) && isset($_POST['proceso'])) {
            $submodelo = $_POST['submodelo'];
            $linea = $_POST['linea'];
            $proceso = $_POST['proceso'];

            $back = 1;

            $user = $this->getUser();
            $planta = $user->getPlanta();

            $response = $this->render('GestionBundle:gestion/tareaasignada:index_tareaasignada.html.twig', array(
            'submodelo' =>$submodelo,
            'linea' =>$linea,
            'proceso' =>$proceso,
            'navtareaasignada' => 1,
            'back' =>$back
            ));
            
        } else {

            $submodelo = 0;
            $linea = 0;
            $proceso = 0;

            $user = $this->getUser();
            $planta = $user->getPlanta();

            $back = 0;

            $response = $this->render('GestionBundle:gestion/tareaasignada:index_tareaasignada.html.twig', array(
                'planta' =>$planta,
                'submodelo' =>$submodelo,
                'linea' =>$linea,
                'proceso' =>$proceso,
                'navtareaasignada' => 1,
                'back' => $back
            ));

        }

        return $response;
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/tareaasignada:index_tareaasignada.html.twig');
    }

    public function getTAsigModelos(Request $request)
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


    public function getTAsigSubmodelos(Request $request)
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
        $statement2->execute();
        $submodelos=$statement2->fetchAll();

        return $response = new JsonResponse(['data' => $submodelos]);

    }

    public function getTAsigLineas(Request $request)
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

    public function getTAsigProcesos(Request $request)
    {
        $linea = $request->get('id');
        $submodelo = $request->get('submodelo');

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();


        $query = "SELECT id_proceso FROM asignarproceso WHERE id_linea = $linea AND id_submodelo = $submodelo AND active = 1";
        $statement=$db->prepare($query);
        $statement->execute();
        $id_procesos=$statement->fetchAll();

        $procesos_id = '';
        foreach ($id_procesos as $value) {
            $procesos_id .= $value['id_proceso'] . ',';
        }

        $procesos_id = trim($procesos_id, ',');

        $query2 = "SELECT * FROM proceso WHERE id in ($procesos_id) AND active = 1 ORDER BY nombre ASC";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $procesos=$statement2->fetchAll();

        return $response = new JsonResponse(['data' => $procesos]);

    }


    public function getEstados(Request $request)
    {
        $ltligada = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM asignarprocesoversion WHERE nombre_lt = '$ltligada'";
        $statement=$db->prepare($query);
        $statement->execute();
        $estados=$statement->fetchAll();

        return $response = new JsonResponse(['data' => $estados]);

    }

    public function dragAction(Request $request) {

        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
        }

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

        $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);


        $proceso = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findOneById($id_proceso);

        $tipo_proceso = $proceso->getTipo();
        $nombre_proceso = $proceso->getNombre();

        if($nombre_proceso == "ALMACEN"){
            $lote=1;
        }else{
            $lote=0;
        }

        $repository = $this->getDoctrine()
            ->getRepository(amfe::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.tipo = :tipo')
            ->setParameter('tipo', 'STD')
            ->getQuery();

        $amfes = $query->getResult();

        if ($tipo_proceso == "INT") {
        
            $response = $this->render('GestionBundle:gestion/tareaasignada:create_tareaasignada.html.twig', array(
                'planta' => $id_planta,
                'submodelo' => $id_submodelo,
                'linea' => $id_linea,
                'proceso' => $id_proceso,
                'asignarproceso' =>$asignarproceso,
                'asignarprocesoversion' =>$asignarprocesoversion,
                'idAsignarprocesoversion' => $id_asignarprocesoversion,
                'lote' => $lote,
                'permiso' => $permiso,
                'amfes' => $amfes
            ));

        } else { // tipo proceso = EXT

            $t1=$asignarprocesoversion->getTiempoStddesp();
            $t2=$asignarprocesoversion->getTiempoStd();
            $t3=$asignarprocesoversion->getTiempoStddespsub();
            $t4=$asignarprocesoversion->getTiempoStdsub();

            $response = $this->render('GestionBundle:gestion/tareaasignada:create_ext_tareaasignada.html.twig', array(
                'planta' => $id_planta,
                'submodelo' => $id_submodelo,
                'linea' => $id_linea,
                'proceso' => $id_proceso,
                'asignarproceso' =>$asignarproceso,
                'asignarprocesoversion' =>$asignarprocesoversion,
                'idAsignarprocesoversion' => $id_asignarprocesoversion,
                'permiso' => $permiso,
                't1' => $t1,
                't2' => $t2,
                't3' => $t3,
                't4' => $t4

            ));
        } 

        return $response;
    }

    public function ajaxExtTarea(Request $request) {

        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

        $t1=$asignarprocesoversion->getTiempoStddesp();
        $t2=$asignarprocesoversion->getTiempoStd();
        $t3=$asignarprocesoversion->getTiempoStddespsub();
        $t4=$asignarprocesoversion->getTiempoStdsub();

        return $this->render('GestionBundle:gestion/tareaasignada:ajax_ext_tareaasignada.html.twig', array(
            'idAsignarprocesoversion' => $id_asignarprocesoversion,
            't1' => $t1,
            't2' => $t2,
            't3' => $t3,
            't4' => $t4
            
        ));

    }


    public function updateAction(Request $request) {

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_asignarproceso=$_POST['asignarproceso'];
        $id_asignarprocesoversion=$_POST['asignarprocesoversion'];

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

               $query = "UPDATE tarea_asignada SET position = '$newPosition' WHERE id='$index'";
               $statement=$db->prepare($query);
               $statement->execute();

                }
            }

            $tareaasignadas = $this->getDoctrine()
                ->getRepository('GestionBundle:tareaAsignada')
                ->findByid_asignarprocesoversion($id_asignarprocesoversion, array('position' => 'ASC'));

            
            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

            $proceso = $this->getDoctrine()
                ->getRepository('GestionBundle:proceso')
                ->find($id_proceso);

            $nombre_proceso = $proceso->getNombre();

            if($nombre_proceso == "ALMACEN"){
                $lote=1;
            }else{
                $lote=0;
            }

            $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

        }

        $repository = $this->getDoctrine()
            ->getRepository(amfe::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.tipo = :tipo')
            ->setParameter('tipo', 'STD')
            ->getQuery();

        $amfes = $query->getResult();

        return $this->render('GestionBundle:gestion/tareaasignada:ajax_tareaasignada.html.twig', array(
            'tareaasignadas' => $tareaasignadas,
            'planta' => $id_planta,
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso,
            'asignarproceso' =>$asignarproceso,
            'asignarprocesoversion' =>$asignarprocesoversion,
            'idAsignarprocesoversion' => $id_asignarprocesoversion,
            'lote' => $lote,
            'permiso' => $permiso,
            'amfes' => $amfes
        ));

    }


    public function newTareaAction(Request $request)
    {

        $new_tareaES=$_POST['tareaES'];
        $new_tareaEN=$_POST['tareaEN'];
        $id_planta=$_POST['planta'];
        $id_asignarprocesoversion=$_POST['asignarprocesoversion'];

        
        if( isset($_POST['lote']) ) {
            $lote=$_POST['lote'];
        } else {
            $lote=0;
        }

        $em = $this->getDoctrine()->getManager();

        $planta = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findById($id_planta);

        $tarea = new tarea;

        $tareaexist = $em->getRepository('GestionBundle:tarea')->findOneBy([
            'nombreES' => $new_tareaES
        ]);

            $tarea->setIdPlanta($planta[0]);
            $tarea->setNombreES($new_tareaES);
            $tarea->setNombreEN($new_tareaEN);

            $em->persist($tarea);    
            $em->flush();

        $id_tarea = $tarea->getId();

        $response = $this->forward('GestionBundle:TareaAsignada:newAsignada', [
            'id_tarea'  => $id_tarea,
            'id_asignarprocesoversion' => $id_asignarprocesoversion,
            'lote' => $lote
        ]);

        return $response;

    }

    public function newAsignadaAction(Request $request, $id_tarea, $id_asignarprocesoversion, $lote)
    {

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT COUNT(*) as numerotareas FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
        $statement=$db->prepare($query);
        $statement->execute();
        $totaltareas=$statement->fetchAll();

        $asignarprocesoversion = $this->getDoctrine()
            ->getRepository('GestionBundle:asignarprocesoversion')
            ->find($id_asignarprocesoversion);

        $tarea = $this->getDoctrine()
            ->getRepository('GestionBundle:tarea')
            ->find($id_tarea);

        $tareaasignada = new tareaAsignada();

        if($lote == 0){

            $tareaasignada->setIdAsignarprocesoversion($asignarprocesoversion);
            $tareaasignada->setIdTarea($tarea);
            $tareaasignada->setPosition(($totaltareas[0]['numerotareas']+1));

            $em->persist($tareaasignada);
            $em->flush();

        } else {

            $tareaasignada->setIdAsignarprocesoversion($asignarprocesoversion);
            $tareaasignada->setIdTarea($tarea);
            $tareaasignada->setPosition(($totaltareas[0]['numerotareas']+1));
            $tareaasignada->setLote($lote);

            $em->persist($tareaasignada);
            $em->flush();
        }

        $response = $this->forward('GestionBundle:TareaAsignada:drag', [
            'planta' =>$id_planta,
            'submodelo' =>$id_submodelo,
            'linea' =>$id_linea,
            'proceso' =>$id_proceso,
            'id_asignarprocesoversion' => $id_asignarprocesoversion
        ]);

        return $response;

    }

    public function deleteAction(Request $request) {

        $id=$_POST['idTareaAsignada'];
        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        //ELIMINAMOS LAS OPERACIONES ASIGNADAS
        $query = "DELETE FROM operacion_asignada WHERE id_tareaAsignada = $id";
        $statement=$db->prepare($query);
        $statement->execute();

        $tareaasignada = $em->getRepository('GestionBundle:tareaAsignada')->find($id);

        $em->remove($tareaasignada);
        $em->flush();
    
        $response = $this->forward('GestionBundle:TareaAsignada:drag', [
            'planta' =>$id_planta,
            'submodelo' =>$id_submodelo,
            'linea' =>$id_linea,
            'proceso' =>$id_proceso,
            'idAsignarprocesoversion' => $id_asignarprocesoversion
        ]);

        return $response;

    }

    public function filterAction(Request $request) {

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
        }
        
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

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

        $asignarprocesores = $em->getRepository('GestionBundle:asignarprocesore')->findBy(
             array('idAsignarproceso'=> $id_asignarproceso), 
             array('position' => 'ASC')
           );

        if (!empty($array_ids)) {

        $query = "SELECT id, nombre_lt FROM asignarprocesoversion WHERE id IN ($array_ids) AND active = 1 GROUP BY nombre_lt, id";
        $statement=$db->prepare($query);
        $statement->execute();
        $lts=$statement->fetchAll();

        $query3 = "SELECT nombre_lt FROM asignarprocesoversion WHERE active = 1 GROUP BY nombre_lt";
        $statement3=$db->prepare($query3);
        $statement3->execute();
        $lts2=$statement3->fetchAll();

        } else {

            $query = "SELECT id, nombre_lt FROM asignarprocesoversion WHERE active = 1 GROUP BY nombre_lt, id";
            $statement=$db->prepare($query);
            $statement->execute();
            $lts=$statement->fetchAll();

            $query3 = "SELECT nombre_lt FROM asignarprocesoversion WHERE active = 1 GROUP BY nombre_lt";
            $statement3=$db->prepare($query3);
            $statement3->execute();
            $lts2=$statement3->fetchAll();
        }


    return $this->render('GestionBundle:gestion/tareaasignada:filter_tareaasignada.html.twig', array(
            'asignarprocesores' => $asignarprocesores,
            'planta' => $id_planta,
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso,
            'lts' => $lts,
            'lts2' => $lts2,
            'permiso' => $permiso
        ));

    }

    public function ajaxTarea(Request $request) {

        $id_asignarproceso=$_POST['asignarproceso'];
        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
        }
    

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $proceso = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->find($id_proceso);

        $nombre_proceso = $proceso->getNombre();

        if($nombre_proceso == "ALMACEN"){
            $lote=1;
        }else{
            $lote=0;
        }

        $tareaasignadas = $this->getDoctrine()
            ->getRepository('GestionBundle:tareaAsignada')
            ->findByIdAsignarprocesoversion($id_asignarprocesoversion, array('position' => 'ASC'));

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

        $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

        $tipo = $asignarprocesoversion->getTipo();
        $ligada = $asignarprocesoversion->getLigada();

        $query = "SELECT SUM(tiempo) as tiempototal FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
        $statement=$db->prepare($query);
        $statement->execute();
        $tiempo_tareas=$statement->fetchAll();

        //seleccionamos los id's de las tareas asignadas 
        $query2 = "SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $id_tareas_asignadas=$statement2->fetchAll();

        $tareas_asignadas_id = '';
            foreach ($id_tareas_asignadas as $value) {
                $tareas_asignadas_id .= $value['id'] . ',';
            }

        $tareas_asignadas_id = trim($tareas_asignadas_id, ',');


        if ($tareas_asignadas_id == null) {

        } else {

            //seleccionamos los id's de las operaciones asignadas de tipo DES
            $query3 = "SELECT id FROM opbasica WHERE tipo in ('DES', 'DNEC') AND active = 1";
            $statement3=$db->prepare($query3);
            $statement3->execute();
            $id_op_asignadas=$statement3->fetchAll();       

            $op_asignadas_id = '';
                foreach ($id_op_asignadas as $value) {
                    $op_asignadas_id .= $value['id'] . ',';
                }

            $op_asignadas_id = trim($op_asignadas_id, ',');

            if ($op_asignadas_id == null) {

            } else {

                if ($tipo == "SUBCONTRATADO") {

                    $query3 = "SELECT SUM(tiempo) as tiempodesperdicios FROM operacion_asignada WHERE id_tareaAsignada in ($tareas_asignadas_id) AND id_operacionbasica in ($op_asignadas_id)";
                    $statement3=$db->prepare($query3);
                    $statement3->execute();
                    $tiempo_std=$statement3->fetchAll();

                    $asignarprocesoversion->setTiempoStddespsub($tiempo_tareas[0]['tiempototal']);
                    $asignarprocesoversion->setTiempoStdsub($tiempo_tareas[0]['tiempototal']-$tiempo_std[0]['tiempodesperdicios']);


                    $em->persist($asignarprocesoversion);
                    $em->flush();

                } else {

                    if ($ligada == 0) {

                        $query2 = "SELECT SUM(tiempo) as tiempodesperdicios FROM operacion_asignada WHERE id_tareaAsignada in ($tareas_asignadas_id) AND id_operacionbasica in ($op_asignadas_id)";
                        $statement2=$db->prepare($query2);
                        $statement2->execute();
                        $tiempo_std=$statement2->fetchAll();

                        $asignarprocesoversion->setTiempoStddesp($tiempo_tareas[0]['tiempototal']);
                        $asignarprocesoversion->setTiempoStd($tiempo_tareas[0]['tiempototal']-$tiempo_std[0]['tiempodesperdicios']);
                        $asignarprocesoversion->setTiempoStddespsub($tiempo_tareas[0]['tiempototal']);
                        $asignarprocesoversion->setTiempoStdsub($tiempo_tareas[0]['tiempototal']-$tiempo_std[0]['tiempodesperdicios']);


                        $em->persist($asignarprocesoversion);
                        $em->flush();


                    } else {

                        $query4 = "SELECT SUM(tiempo) as tiempodesperdicios FROM operacion_asignada WHERE id_tareaAsignada in ($tareas_asignadas_id) AND id_operacionbasica in ($op_asignadas_id)";
                        $statement4=$db->prepare($query4);
                        $statement4->execute();
                        $tiempo_std=$statement4->fetchAll();

                        $asignarprocesoversion->setTiempoStddesp($tiempo_tareas[0]['tiempototal']);
                        $asignarprocesoversion->setTiempoStd($tiempo_tareas[0]['tiempototal']-$tiempo_std[0]['tiempodesperdicios']);

                        $em->persist($asignarprocesoversion);
                        $em->flush();


                    }

                }

            }

            $query6 = "SELECT id FROM amfe WHERE nombre = 'NO APLICA'";
            $statement6=$db->prepare($query6);
            $statement6->execute();
            $id_no_aplica=$statement6->fetchAll();

            $total_tareas = count($id_tareas_asignadas);
            $amfes = [];

            for ($i=0; $i < $total_tareas; $i++) { 

                $tarea_asignada = $id_tareas_asignadas[$i]['id'];

                $query5 = "SELECT id_amfe FROM tarea_amfe WHERE id_tareaAsignada = $tarea_asignada";
                $statement5=$db->prepare($query5);
                $statement5->execute();
                $id_amfe=$statement5->fetchAll();

                if (count($id_amfe) == 1) {

                    if ($id_amfe[0]['id_amfe'] == $id_no_aplica[0]['id']) {

                        $amfes[] = 1;

                    } else {

                        $amfes[] = 2;
                    }
                
                } elseif (count($id_amfe) > 1)  {
                   
                    $amfes[] = 2;
                
                } else {

                    $amfes[] = 0;
                } 

            }

        }


    return $this->render('GestionBundle:gestion/tareaasignada:ajax_tareaasignada.html.twig', array(
            'tareaasignadas' => $tareaasignadas,
            'planta' => $id_planta,
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso,
            'asignarproceso' =>$asignarproceso,
            'asignarprocesoversion' =>$asignarprocesoversion,
            'idAsignarprocesoversion' => $id_asignarprocesoversion,
            'lote' => $lote,
            'permiso' => $permiso,
            'amfes' => $amfes
            
        ));

    }

    public function newLT(Request $request) {

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $nombre_lt=$_POST['nombreLT'];
        $comentario_lt=$_POST['comentarioLT'];
        
        
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query2 = "SELECT nombre_lt FROM asignarprocesoversion WHERE nombre_lt = '$nombre_lt'";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $datos=$statement2->fetchAll();


        if ($datos == null) {

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

        $id_asignarproceso = $asignarproceso->getId();

        $new_version = 1;
        $version = $em->getRepository('GestionBundle:version')->find($new_version);

        $asignarprocesoversion = new asignarprocesoversion();


        
            if ($_POST['tipo'] == "SUBCONTRATADO") {
                

                    $nombre_ltligada=$_POST['ltligada'];
                    $estado=$_POST['estado'];

                    $query = "UPDATE asignarprocesoversion SET tiempo_stddespsub = 0, tiempo_stdsub = 0, ligada = 1 WHERE nombre_lt = '$nombre_ltligada' AND estado = '$estado'";
                    $statement=$db->prepare($query);
                    $statement->execute();

                    $query2 = "SELECT id AS id_ligada FROM asignarprocesoversion WHERE nombre_lt = '$nombre_ltligada' AND estado = '$estado'";
                    $statement2=$db->prepare($query2);
                    $statement2->execute();
                    $id=$statement2->fetchAll();


                $asignarprocesoversion->setVersion($version);
                $asignarprocesoversion->setEstado("DESARROLLO");
                $asignarprocesoversion->setNombreLt($nombre_lt);
                $asignarprocesoversion->setComentario($comentario_lt);
                $asignarprocesoversion->setTipo($_POST['tipo']);
                $asignarprocesoversion->setLigada(($id[0]['id_ligada']));
            
            } else {

                $asignarprocesoversion->setVersion($version);
                $asignarprocesoversion->setEstado("DESARROLLO");
                $asignarprocesoversion->setNombreLt($nombre_lt);
                $asignarprocesoversion->setComentario($comentario_lt);
                $asignarprocesoversion->setTipo($_POST['tipo']);

            }


        $em->persist($asignarprocesoversion);
        $em->flush();

        
        //creamos la relacion en ASIGNAREPROCESORE
        $id_asignarprocesoversion = $asignarprocesoversion->getId();

        $query = "SELECT COUNT(*) as numerolistados FROM asignarprocesore WHERE id_asignarproceso = $id_asignarproceso";
        $statement=$db->prepare($query);
        $statement->execute();
        $totalistados=$statement->fetchAll();

        $entityManager = $this->getDoctrine()->getManager();

        $asignarprocesore = new asignarprocesore();

        $asignarprocesoset = $entityManager->getRepository('GestionBundle:asignarproceso')->find($id_asignarproceso);
        $asignarprocesore->setIdAsignarproceso($asignarprocesoset);

        $asignarprocesoversionset = $entityManager->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);
        $asignarprocesore->setIdAsignarprocesoversion($asignarprocesoversionset);

        $asignarprocesore->setPosition(($totalistados[0]['numerolistados']+1));
        
        $em->persist($asignarprocesore);
        $em->flush();

        $back = 1;

        $user = $this->getUser();
        $planta = $user->getPlanta();

        $repository = $this->getDoctrine()
            ->getRepository(modelo::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.idPlanta = :idPlanta')
            ->andWhere('p.active = :active')
            ->setParameter('idPlanta', $planta)
            ->setParameter('active', 1)
            ->orderBy('p.nombre', 'ASC')
            ->getQuery();

        $modelos = $query->getResult();


        $response = $this->render('GestionBundle:gestion/tareaasignada:index_tareaasignada.html.twig', array(
            'submodelo' =>$id_submodelo,
            'linea' =>$id_linea,
            'proceso' =>$id_proceso,
            'navtareaasignada' => 1,
            'back' =>$back,
            'modelos' =>$modelos
            ));

        return $response;

    } else {

        $message = ('Ya existe una LT con el mismo nombre.');

        return $this->render('GestionBundle:gestion:error.html.twig', array(
                    'message' => $message
        ));
    }

    }

    public function deleteLTAction(Request $request) {

        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

        $id_asignarproceso = $asignarproceso->getId();

        //COMPROBAMOS QUE EL ESTADO SEA "DESARROLLO"

        $asignarprocesoversion = $this->getDoctrine()
            ->getRepository('GestionBundle:asignarprocesoversion')
            ->find($id_asignarprocesoversion);

        $estado = $asignarprocesoversion->getEstado();

        if ($estado == "DESARROLLO") {

            //SELECCIONAMOS LAS TAREAS_ASIGNADAS del LT

            $query = "SELECT * FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
            $statement=$db->prepare($query);
            $statement->execute();
            $datos=$statement->fetchAll();

            if ($datos == null) {

            } else {

                $array_id_tareas = '';
                    foreach ($datos as $value) {
                        $array_id_tareas .= $value['id'] . ',';
                    }

                $array_id_tareas = trim($array_id_tareas, ',');


                //ELIMINAMOS LAS OPERACIONES ASIGNADAS
                $query2 = "DELETE FROM operacion_asignada WHERE id_tareaAsignada IN ($array_id_tareas)";
                $statement2=$db->prepare($query2);
                $statement2->execute();

                //ELIMINAMOS LAS TAREAS ASIGNADAS
                $query3 = "DELETE FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
                $statement3=$db->prepare($query3);
                $statement3->execute();

            }

            $qb = $em->createQueryBuilder();
            $query = $qb->delete('GestionBundle:asignarprocesore', 'asignarprocesore')
                        ->where('asignarprocesore.idAsignarproceso = :asignarproceso_id')
                        ->andWhere('asignarprocesore.idAsignarprocesoversion = :asignarprocesoversion_id')
                        ->setParameter('asignarproceso_id', $id_asignarproceso)
                        ->setParameter('asignarprocesoversion_id', $id_asignarprocesoversion)
                        ->getQuery();

            $query->execute();

            $qb2 = $em->createQueryBuilder();
            $query2 = $qb2->delete('GestionBundle:asignarprocesoversion', 'asignarprocesoversion')
                        ->where('asignarprocesoversion.id = :asignarprocesoversion_id')
                        ->setParameter('asignarprocesoversion_id', $id_asignarprocesoversion)
                        ->getQuery();

            $query2->execute();

            $response = $this->forward('GestionBundle:TareaAsignada:index');

            return $response;

        } else {

            $message = ('Solo se pueden eliminar listados de tareas en estado "DESARROLLO".');

            return $this->render('GestionBundle:gestion:error.html.twig', array(
                    'message' => $message
            ));
        }


    }

    public function newVersion(Request $request) {

        $id_asignarprocesoversionactual=$_POST['idAsignarprocesoversion'];

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $comentario_lt=$_POST['comentarioLT'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

            $id_asignarproceso = $asignarproceso->getId();

            $asignarprocesoversion_actual = $this->getDoctrine()
                ->getRepository('GestionBundle:asignarprocesoversion')
                ->find($id_asignarprocesoversionactual);

            $version = $asignarprocesoversion_actual->getVersion();
            $actual_version = $version->getId();


            $estado = $asignarprocesoversion_actual->getEstado();

            if ($estado == "DESARROLLO") {

                $message = ('Ya existe una versión en desarrollo.');

                return $this->render('GestionBundle:gestion:error.html.twig', array(
                        'message' => $message
                ));
                
            } else {

                $new_version = $actual_version + 1;

                $nombre_ltexistente = $asignarprocesoversion_actual->getNombreLt();
                $tipo_ltexistente = $asignarprocesoversion_actual->getTipo();

                $asignarprocesoversion = new asignarprocesoversion();

                $version = $em->getRepository('GestionBundle:version')->find($new_version);

                $asignarprocesoversion->setVersion($version);
                $asignarprocesoversion->setEstado("DESARROLLO");
                $asignarprocesoversion->setNombreLt($nombre_ltexistente);
                $asignarprocesoversion->setTipo($tipo_ltexistente);
                $asignarprocesoversion->setComentario($comentario_lt);


                $em->persist($asignarprocesoversion);
                $em->flush();

                $id_asignarprocesoversion = $asignarprocesoversion->getId();

                $query2 = "SELECT * FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversionactual";
                $statement2=$db->prepare($query2);
                $statement2->execute();
                $datos=$statement2->fetchAll();

                if ($datos == null) {

                } else {

                    //COPIAMOS TODAS LAS TAREAS ASIGNADAS A LA VERSION ACTUAL DE LA LT
                    
                    $query3 = "INSERT INTO tarea_asignada (id_asignarprocesoversion, id_tarea, tiempo, position) VALUES ";
                    foreach ($datos as $key=>$value) {
                        $query3 = $query3 . "(".$id_asignarprocesoversion .",". $value['id_tarea'] . ",". $value['tiempo']. ",".$value['position']."),";
                    }
                    $query3 = trim($query3, ','); 

                    $statement3=$db->prepare($query3);
                    $statement3->execute();


                    //COPIAMOS TODAS LAS OPERACIONES BASICAS ASIGNADAS A CADA TAREA LA VERSION ACTUAL DE LA LT

                    //seleccionamos los id's de las tareas asignadas a la version actual
                    $query4 = "SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversionactual";
                    $statement4=$db->prepare($query4);
                    $statement4->execute();
                    $id_tarea_asignada_actual=$statement4->fetchAll();

                    $tarea_asignada_actual_id = '';
                    foreach ($id_tarea_asignada_actual as $value) {
                        $tarea_asignada_actual_id .= $value['id'] . ',';
                    }

                    $tarea_asignada_actual_id = trim($tarea_asignada_actual_id, ',');

                    //selecionamos los id's de las tareas asignadas a la nueva version
                    $query7 = "SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
                    $statement7=$db->prepare($query7);
                    $statement7->execute();
                    $id_tarea_asignada_nueva=$statement7->fetchAll();

                    $cantidad_tareas=count($id_tarea_asignada_nueva);

                    //insertamos las operaciones basicas con los ids de las tareas de la nueva version

                    for ($i=0; $i < $cantidad_tareas; $i++) { 

                        $tarea_asignada=$id_tarea_asignada_actual[$i]['id'];

                        $query5 = "SELECT id_operacionbasica, repeticion, position, tiempo, comentario, active FROM operacion_asignada WHERE id_tareaAsignada = $tarea_asignada";
                        $statement5=$db->prepare($query5);
                        $statement5->execute();
                        $datos_opb=$statement5->fetchAll();

                        $cantidad_opb=count($datos_opb);

                        $query8 = "INSERT INTO operacion_asignada (id_operacionbasica, repeticion, position, tiempo, comentario, active, id_tareaAsignada) VALUES ";

                        for ($j=0; $j < $cantidad_opb; $j++) { 
                            
                            $query8 = $query8 . "(".$datos_opb[$j]['id_operacionbasica'] .",". $datos_opb[$j]['repeticion'] . ",". $datos_opb[$j]['position'] . ",". $datos_opb[$j]['tiempo']. ",'".$datos_opb[$j]['comentario']."',". $datos_opb[$j]['active'] . ",". $id_tarea_asignada_nueva[$i]['id'] . "),";

                        }

                        $query8 = trim($query8, ','); 

                        $statement8=$db->prepare($query8);
                        $statement8->execute();

                        //asignamos amfe's a los nuevos id's de tareas
                        $query50 = "SELECT id_amfe FROM tarea_amfe WHERE id_tareaAsignada = $tarea_asignada";
                        $statement50=$db->prepare($query50);
                        $statement50->execute();
                        $datos_amfe=$statement50->fetchAll();

                        $cantidad_amfe=count($datos_amfe);

                        $query80 = "INSERT INTO tarea_amfe (id_amfe, id_tareaAsignada) VALUES ";

                        for ($k=0; $k < $cantidad_amfe; $k++) { 
                            
                            $query80 = $query80 . "(".$datos_amfe[$k]['id_amfe'] .",". $id_tarea_asignada_nueva[$i]['id'] . "),";

                        }

                        $query80 = trim($query80, ','); 

                        $statement80=$db->prepare($query80);
                        $statement80->execute();

                        } // endfor  

                    } //endelse datos!=null


                //ASIGNAMOS la relacion en ASIGNAREPROCESORE

                    $query = "SELECT COUNT(*) as numerolistados FROM asignarprocesore WHERE id_asignarproceso = $id_asignarproceso";
                    $statement=$db->prepare($query);
                    $statement->execute();
                    $totalistados=$statement->fetchAll();

                    $entityManager = $this->getDoctrine()->getManager();

                    $asignarprocesore = new asignarprocesore();

                    $asignarprocesoset = $entityManager->getRepository('GestionBundle:asignarproceso')->find($id_asignarproceso);
                    $asignarprocesore->setIdAsignarproceso($asignarprocesoset);

                    $asignarprocesoversionset = $entityManager->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);
                    $asignarprocesore->setIdAsignarprocesoversion($asignarprocesoversionset);

                    $asignarprocesore->setPosition(($totalistados[0]['numerolistados']+1));
                    
                    $em->persist($asignarprocesore);
                    $em->flush();

            } //end else estado!=null
        } //endif permiso


    $response = $this->forward('GestionBundle:TareaAsignada:index', [
            'planta' =>$id_planta,
            'submodelo' =>$id_submodelo,
            'linea' =>$id_linea,
            'proceso' =>$id_proceso
        ]);

    return $response;

    }

    public function newExtAction(Request $request) {

        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $value_t1=$_POST['value_t1'];
        $value_t2=$_POST['value_t2'];
        $value_t3=$_POST['value_t3'];
        $value_t4=$_POST['value_t4'];

        $time_t1 = explode(':', $value_t1);
        $h1 = (int)($time_t1[0]);
        $m1 = (int)($time_t1[1]);
        $s1 = (int)($time_t1[2]);

        $time_t2 = explode(':', $value_t2);
        $h2 = (int)($time_t2[0]);
        $m2 = (int)($time_t2[1]);
        $s2 = (int)($time_t2[2]);

        $time_t3 = explode(':', $value_t3);
        $h3 = (int)($time_t3[0]);
        $m3 = (int)($time_t3[1]);
        $s3 = (int)($time_t3[2]);

        $time_t4 = explode(':', $value_t4);
        $h4 = (int)($time_t4[0]);
        $m4 = (int)($time_t4[1]);
        $s4 = (int)($time_t4[2]);

        $t1=((int)$h1*3600) + ((int)$m1*60) + (int)$s1;
        $t2=((int)$h2*3600) + ((int)$m2*60) + (int)$s2;
        $t3=((int)$h3*3600) + ((int)$m3*60) + (int)$s3;
        $t4=((int)$h4*3600) + ((int)$m4*60) + (int)$s4;

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

        $tipo_lt = $asignarprocesoversion->getTipo();

        if ($tipo_lt == "SUBCONTRATADO"){

        $asignarprocesoversion->setTiempoStddespsub($t3);
        $asignarprocesoversion->setTiempoStdsub($t4);

        } else {

        $asignarprocesoversion->setTiempoStddesp($t1);
        $asignarprocesoversion->setTiempoStd($t2);
        $asignarprocesoversion->setTiempoStddespsub($t3);
        $asignarprocesoversion->setTiempoStdsub($t4);

        }

        $em->persist($asignarprocesoversion);
        $em->flush();

        return $this->render('GestionBundle:gestion/tareaasignada:ajax_ext_tareaasignada.html.twig', array(
            'idAsignarprocesoversion' => $id_asignarprocesoversion,
            't1' => $t1,
            't2' => $t2,
            't3' => $t3,
            't4' => $t4
            
        ));

    }

    public function oficializarLT(Request $request) {

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

            $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

            $estado = $asignarprocesoversion->getEstado();

            if ($estado == "DESARROLLO") {
        
            return $this->render('GestionBundle:gestion/tareaasignada:oficializar_tareaasignada.html.twig', array(
                'planta' =>$id_planta,
                'submodelo' =>$id_submodelo,
                'linea' =>$id_linea,
                'proceso' =>$id_proceso,
                'asignarproceso' =>$asignarproceso,
                'idAsignarprocesoversion' =>$id_asignarprocesoversion,
                'asignarprocesoversion' =>$asignarprocesoversion,
                'permiso' => $permiso
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
        $comentario=$_POST['comentario'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
        
            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();


            $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

            $asignarprocesoversion->setFechaInicio(new \DateTime($fecha));
            $asignarprocesoversion->setComentario($comentario);
            $asignarprocesoversion->setEstado("PRE-PRODUCCION");

            $em->persist($asignarprocesoversion);
            $em->flush();

        } //endif permiso

    
        $response = $this->forward('GestionBundle:TareaAsignada:index', [
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso
        ]);

    return $response;

    }


    public function editAction(Request $request) {

        $id_asignarproceso=$_POST['asignarproceso'];
        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $value_nombre=$_POST['value_nombre'];
        $value_position=$_POST['value_position'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
       
            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $proceso = $this->getDoctrine()
                ->getRepository('GestionBundle:proceso')
                ->find($id_proceso);

            $nombre_proceso = $proceso->getNombre();

            if($nombre_proceso == "ALMACEN"){
                $lote=1;
            }else{
                $lote=0;
            }

            $tarea = new tarea;

            $plantaset = $em->getRepository('GestionBundle:planta')->find($id_planta);
            $tarea->setIdPlanta($plantaset);
            $tarea->setNombreES($value_nombre);
            
            $em->persist($tarea);
            $em->flush();

            $id_tarea=$tarea->getId();

            $query = "UPDATE tarea_asignada SET id_tarea = $id_tarea WHERE id_asignarprocesoversion = '$id_asignarprocesoversion' AND position = '$value_position'";
            $statement=$db->prepare($query);
            $statement->execute();

            $tareaasignadas = $this->getDoctrine()
                ->getRepository('GestionBundle:tareaAsignada')
                ->findByIdAsignarprocesoversion($id_asignarprocesoversion, array('position' => 'ASC'));

            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

            $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);    

        } //endif permiso  

        $repository = $this->getDoctrine()
            ->getRepository(amfe::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.tipo = :tipo')
            ->setParameter('tipo', 'STD')
            ->getQuery();

        $amfes = $query->getResult();

        return $this->render('GestionBundle:gestion/tareaasignada:ajax_tareaasignada.html.twig', array(
            'tareaasignadas' => $tareaasignadas,
            'planta' => $id_planta,
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso,
            'asignarproceso' =>$asignarproceso,
            'asignarprocesoversion' =>$asignarprocesoversion,
            'idAsignarprocesoversion' => $id_asignarprocesoversion,
            'lote' => $lote,
            'permiso' => $permiso,
            'amfes' => $amfes
            
        ));

    }

    public function update2Action(Request $request) {

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
        

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            if (isset($_POST['update'])) {
            foreach($_POST['positions'] as $position) {
               $index = $position[0];
               $newPosition = $position[1];

               $em = $this->getDoctrine()->getManager();
               $db = $em->getConnection();

               $query = "UPDATE asignarprocesore SET position = '$newPosition' WHERE id='$index'";
               $statement=$db->prepare($query);
               $statement->execute();

                }
            }

            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

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

            $asignarprocesores = $em->getRepository('GestionBundle:asignarprocesore')->findBy(
                 array('idAsignarproceso'=> $id_asignarproceso), 
                 array('position' => 'ASC')
               );


            $query3 = "SELECT id, nombre_lt FROM asignarprocesoversion WHERE id IN ($array_ids) AND active = 1 GROUP BY nombre_lt, id";
            $statement3=$db->prepare($query3);
            $statement3->execute();
            $lts=$statement3->fetchAll();

            $query4 = "SELECT nombre_lt FROM asignarprocesoversion WHERE active = 1 GROUP BY nombre_lt";
            $statement4=$db->prepare($query4);
            $statement4->execute();
            $lts2=$statement4->fetchAll();

        } //endif permiso


        return $this->render('GestionBundle:gestion/tareaasignada:filter_tareaasignada.html.twig', array(
            'asignarprocesores' => $asignarprocesores,
            'planta' => $id_planta,
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso,
            'lts' => $lts,
            'lts2' => $lts2,
            'permiso' => $permiso
        ));

    }

    public function asignarLT(Request $request) {


        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];

        $nombre_lt=$_POST['ltligada2'];
        $estado=$_POST['estado2'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT id AS id_ligada FROM asignarprocesoversion WHERE nombre_lt = '$nombre_lt' AND estado = '$estado'";
        $statement=$db->prepare($query);
        $statement->execute();
        $id=$statement->fetchAll();

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

        $id_asignarproceso = $asignarproceso->getId();

        //ASIGNAMOS la relacion en ASIGNAREPROCESORE

        $query = "SELECT COUNT(*) as numerolistados FROM asignarprocesore WHERE id_asignarproceso = $id_asignarproceso";
        $statement=$db->prepare($query);
        $statement->execute();
        $totalistados=$statement->fetchAll();

        $entityManager = $this->getDoctrine()->getManager();

        $asignarprocesore = new asignarprocesore();

        $asignarprocesoset = $entityManager->getRepository('GestionBundle:asignarproceso')->find($id_asignarproceso);
        $asignarprocesore->setIdAsignarproceso($asignarprocesoset);

        $asignarprocesoversionset = $entityManager->getRepository('GestionBundle:asignarprocesoversion')->find($id[0]['id_ligada']);
        $asignarprocesore->setIdAsignarprocesoversion($asignarprocesoversionset);

        $asignarprocesore->setPosition(($totalistados[0]['numerolistados']+1));
        
        $em->persist($asignarprocesore);
        $em->flush();


        $response = $this->forward('GestionBundle:TareaAsignada:index');

    return $response;

    }

    public function editCamposAction(Request $request) {

        
        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $idAsignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;
        

            $value_nombre=$_POST['value_nombre'];
            $value_comentario=$_POST['value_comentario'];
           
            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $query = "UPDATE asignarprocesoversion SET nombre_lt = '$value_nombre', comentario = '$value_comentario' WHERE id = '$idAsignarprocesoversion'";
            $statement=$db->prepare($query);
            $statement->execute();

            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

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

            $asignarprocesores = $em->getRepository('GestionBundle:asignarprocesore')->findBy(
                 array('idAsignarproceso'=> $id_asignarproceso), 
                 array('position' => 'ASC')
               );

            if (!empty($array_ids)) {

            $query = "SELECT id, nombre_lt FROM asignarprocesoversion WHERE id IN ($array_ids) AND active = 1 GROUP BY nombre_lt, id";
            $statement=$db->prepare($query);
            $statement->execute();
            $lts=$statement->fetchAll();

            $query3 = "SELECT nombre_lt FROM asignarprocesoversion WHERE active = 1 GROUP BY nombre_lt";
            $statement3=$db->prepare($query3);
            $statement3->execute();
            $lts2=$statement3->fetchAll();

            } else {

                $query = "SELECT id, nombre_lt FROM asignarprocesoversion WHERE active = 1 GROUP BY nombre_lt, id";
                $statement=$db->prepare($query);
                $statement->execute();
                $lts=$statement->fetchAll();

                $query3 = "SELECT nombre_lt FROM asignarprocesoversion WHERE active = 1 GROUP BY nombre_lt";
                $statement3=$db->prepare($query3);
                $statement3->execute();
                $lts2=$statement3->fetchAll();
            }
        }//ebdif permiso


    return $this->render('GestionBundle:gestion/tareaasignada:filter_tareaasignada.html.twig', array(
            'asignarprocesores' => $asignarprocesores,
            'planta' => $id_planta,
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso,
            'lts' => $lts,
            'lts2' => $lts2,
            'permiso' => $permiso
        ));

    }


    public function index_editltAction(Request $request) {

        $user = $this->getUser();
        $planta = $user->getPlanta();

        $repository = $this->getDoctrine()
            ->getRepository(submodelo::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.idPlanta = :idPlanta')
            ->andWhere('p.active = :active')
            ->setParameter('idPlanta', $planta)
            ->setParameter('active', 1)
            ->orderBy('p.nombre', 'ASC')
            ->getQuery();

        $submodelos = $query->getResult();


        return $this->render('GestionBundle:gestion/tareaasignada:index_editlt_tareaasignada.html.twig', array(
            'naveditlttareaasignada' => 1,
            'submodelos' => $submodelos
            ));

    }


    public function getAmfes(Request $request)
    {
        $tarea_asignada = $request->get('tareaAsignada');

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query2 = "SELECT * FROM amfe WHERE tipo = 'STD'";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $amfes=$statement2->fetchAll();

        $cantidadamfes=count($amfes);

        $checked_amfes = [];

        for ($i=0; $i < $cantidadamfes; $i++) { 

            $id_amfe = $amfes[$i]['id'];

            $query = "SELECT id FROM tarea_amfe WHERE id_tareaAsignada = $tarea_asignada AND id_amfe = '$id_amfe'";
            $statement=$db->prepare($query);
            $statement->execute();
            $amfes_checked=$statement->fetchAll();

            if ($amfes_checked == null) {

                $checked_amfes[] = 0;

            } else {

                $checked_amfes[] = 1;
            }
        }

        return $response = new JsonResponse(['data' => $amfes, 'data2' => $checked_amfes]);

    }

    public function setAmfesAction(Request $request)
    {
        $id_tarea_asignada=$_POST['idTareaAsignada'];
        

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        if (isset($_POST['bbb'])) {

            $id_amfes=$_POST['bbb'];

            $query = "DELETE FROM tarea_amfe WHERE id_tareaAsignada = $id_tarea_asignada";
            $statement=$db->prepare($query);
            $statement->execute();

            $cantidadamfes=count($id_amfes);

            for ( $i=0; $i<$cantidadamfes ; $i++) {

                $id_amfe = $id_amfes[$i];

                $tarea_asignada = $em->getRepository('GestionBundle:tareaAsignada')->find($id_tarea_asignada);
                $amfe = $em->getRepository('GestionBundle:amfe')->find($id_amfe);

                $tarea_amfe = new tarea_amfe;

                $tarea_amfe->setIdTareaAsignada($tarea_asignada);
                $tarea_amfe->setIdAmfe($amfe);

                $em->persist($tarea_amfe);
                $em->flush();

            }
        } else {

            $query2 = "DELETE FROM tarea_amfe WHERE id_tareaAsignada = $id_tarea_asignada";
            $statement2=$db->prepare($query2);
            $statement2->execute();
        }
        

        $response = $this->forward('GestionBundle:TareaAsignada:drag', [
            'planta' =>$id_planta,
            'submodelo' =>$id_submodelo,
            'linea' =>$id_linea,
            'proceso' =>$id_proceso,
            'idAsignarprocesoversion' => $id_asignarprocesoversion
        ]);

        return $response;

    }


    public function newCopyLtAction(Request $request) {

        $id_planta=$_POST['planta'];
        $id_submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $nombre=$_POST['nombre_copyLT'];
        $lt_select=$_POST['lt_copyLT'];
        $estado=$_POST['estado3'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $id_submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

            $id_asignarproceso = $asignarproceso->getId();

            $query = "SELECT id AS id_lt_select FROM asignarprocesoversion WHERE nombre_lt = '$lt_select' AND estado = '$estado'";
            $statement=$db->prepare($query);
            $statement->execute();
            $id=$statement->fetchAll();

            $asignarprocesoversion_select = $this->getDoctrine()
                    ->getRepository('GestionBundle:asignarprocesoversion')
                    ->find($id[0]['id_lt_select']);

            $tipo = $asignarprocesoversion_select->getTipo();
            $id_asignarprocesoversionselect = $asignarprocesoversion_select->getId();

            // NUEVA LT /////////////////////////////////
            $new_version = 1;
            $version = $em->getRepository('GestionBundle:version')->find($new_version);

            $asignarprocesoversion = new asignarprocesoversion();

            $version = $em->getRepository('GestionBundle:version')->find($new_version);

            $asignarprocesoversion->setVersion($version);
            $asignarprocesoversion->setEstado("DESARROLLO");
            $asignarprocesoversion->setNombreLt($nombre);
            $asignarprocesoversion->setTipo($tipo);

            $em->persist($asignarprocesoversion);
            $em->flush();

            $id_asignarprocesoversion=$asignarprocesoversion->getId();

            $query = "SELECT COUNT(*) as numerolistados FROM asignarprocesore WHERE id_asignarproceso = $id_asignarproceso";
            $statement=$db->prepare($query);
            $statement->execute();
            $totalistados=$statement->fetchAll();

            $entityManager = $this->getDoctrine()->getManager();

            $asignarprocesore = new asignarprocesore();

            $asignarprocesoset = $entityManager->getRepository('GestionBundle:asignarproceso')->find($id_asignarproceso);
            $asignarprocesore->setIdAsignarproceso($asignarprocesoset);

            $asignarprocesoversionset = $entityManager->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);
            $asignarprocesore->setIdAsignarprocesoversion($asignarprocesoversionset);

            $asignarprocesore->setPosition(($totalistados[0]['numerolistados']+1));
                        
            $em->persist($asignarprocesore);
            $em->flush();


            $query2 = "SELECT * FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversionselect";
            $statement2=$db->prepare($query2);
            $statement2->execute();
            $datos=$statement2->fetchAll();

            if ($datos == null) {

                } else {

                    //COPIAMOS TODAS LAS TAREAS ASIGNADAS A LA VERSION ACTUAL DE LA LT
                        
                    $query3 = "INSERT INTO tarea_asignada (id_asignarprocesoversion, id_tarea, tiempo, position) VALUES ";
                        foreach ($datos as $key=>$value) {
                            $query3 = $query3 . "(".$id_asignarprocesoversion .",". $value['id_tarea'] . ",". $value['tiempo']. ",".$value['position']."),";
                        }
                    $query3 = trim($query3, ','); 

                    $statement3=$db->prepare($query3);
                    $statement3->execute();


                    //COPIAMOS TODAS LAS OPERACIONES BASICAS ASIGNADAS A CADA TAREA LA VERSION ACTUAL DE LA LT

                    //seleccionamos los id's de las tareas asignadas a la version actual
                    $query4 = "SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversionselect";
                    $statement4=$db->prepare($query4);
                    $statement4->execute();
                    $id_tarea_asignada_select=$statement4->fetchAll();

                    $tarea_asignada_select_id = '';
                        foreach ($id_tarea_asignada_select as $value) {
                            $tarea_asignada_select_id .= $value['id'] . ',';
                        }

                    $tarea_asignada_select_id = trim($tarea_asignada_select_id, ',');

                    //selecionamos los id's de las tareas asignadas a la nueva version
                    $query7 = "SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion";
                    $statement7=$db->prepare($query7);
                    $statement7->execute();
                    $id_tarea_asignada_nueva=$statement7->fetchAll();

                    $cantidad_tareas=count($id_tarea_asignada_nueva);

                    //insertamos las operaciones basicas con los ids de las tareas de la nueva version

                    for ($i=0; $i < $cantidad_tareas; $i++) { 

                        $tarea_asignada=$id_tarea_asignada_select[$i]['id'];

                        $query5 = "SELECT id_operacionbasica, repeticion, position, tiempo, comentario, active FROM operacion_asignada WHERE id_tareaAsignada = $tarea_asignada";
                        $statement5=$db->prepare($query5);
                        $statement5->execute();
                        $datos_opb=$statement5->fetchAll();

                        $cantidad_opb=count($datos_opb);

                        $query8 = "INSERT INTO operacion_asignada (id_operacionbasica, repeticion, position, tiempo, comentario, active, id_tareaAsignada) VALUES ";

                            for ($j=0; $j < $cantidad_opb; $j++) { 
                                
                                $query8 = $query8 . "(".$datos_opb[$j]['id_operacionbasica'] .",". $datos_opb[$j]['repeticion'] . ",". $datos_opb[$j]['position'] . ",". $datos_opb[$j]['tiempo']. ",'".$datos_opb[$j]['comentario']."',". $datos_opb[$j]['active'] . ",". $id_tarea_asignada_nueva[$i]['id'] . "),";

                            }

                        $query8 = trim($query8, ','); 

                        $statement8=$db->prepare($query8);
                        $statement8->execute();

                        //asignamos amfe's a los nuevos id's de tareas
                        $query50 = "SELECT id_amfe FROM tarea_amfe WHERE id_tareaAsignada = $tarea_asignada";
                        $statement50=$db->prepare($query50);
                        $statement50->execute();
                        $datos_amfe=$statement50->fetchAll();

                        $cantidad_amfe=count($datos_amfe);

                        $query80 = "INSERT INTO tarea_amfe (id_amfe, id_tareaAsignada) VALUES ";

                        for ($k=0; $k < $cantidad_amfe; $k++) { 
                            
                            $query80 = $query80 . "(".$datos_amfe[$k]['id_amfe'] .",". $id_tarea_asignada_nueva[$i]['id'] . "),";

                        }

                        $query80 = trim($query80, ','); 

                        $statement80=$db->prepare($query80);
                        $statement80->execute();

                    } // endfor  

                } //endelse datos!=null

        } //endif permiso


    $response = $this->forward('GestionBundle:TareaAsignada:index', [
            'planta' =>$id_planta,
            'submodelo' =>$id_submodelo,
            'linea' =>$id_linea,
            'proceso' =>$id_proceso
        ]);

    return $response;

    }

}
