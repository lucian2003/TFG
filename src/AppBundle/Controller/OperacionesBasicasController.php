<?php

namespace AppBundle\Controller;


use GestionBundle\Entity\planta;
use GestionBundle\Entity\modelo;
use GestionBundle\Entity\submodelo;
use GestionBundle\Entity\linea;
use GestionBundle\Entity\proceso;
use GestionBundle\Entity\tarea;
use GestionBundle\Entity\opbasica;
use GestionBundle\Entity\tareaAsignada;
use GestionBundle\Entity\asignarproceso;
use GestionBundle\Entity\asignarprocesoversion;
use GestionBundle\Entity\operacionAsignada;
use GestionBundle\Entity\configuracionlinea;
use GestionBundle\Entity\detalleconfiguracion;


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

use PHPExcel;
use PHPExcel_IOFactory;

use Doctrine\ORM\EntityRepository;

class OperacionesBasicasController extends Controller
{
   
    public function indexAction() {


        return $this->render('AppBundle:Consultas/operacionesbasicas:index_operacionesbasicas.html.twig', array(
                'navopbasicas' => 1
                //'modelos' => $modelos,
                //'opbasicas' => $opbasicas
        ));

    }


    public function areaAction(Request $request) {

        $area = $request->get('area');

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT DISTINCT id_gama FROM modelo WHERE area = '$area' AND active = 1";
        $statement=$db->prepare($query);
        $statement->execute();
        $id_gamas=$statement->fetchAll();

        $gamas_id = '';
        foreach ($id_gamas as $value) {
            $gamas_id .= $value['id_gama'] . ',';
        }

        $gamas_id = trim($gamas_id, ',');

        $query2 = "SELECT * FROM gama WHERE id in ($gamas_id) ORDER BY nombre ASC";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $gamas=$statement2->fetchAll();

            return $this->render('AppBundle:Consultas/operacionesbasicas:index_gama_operacionesbasicas.html.twig', array(
                'area' => $area,
                'gamas' => $gamas
            ));

    }


    public function gamaAction(Request $request) {

            $id_gama = $request->get('gama');
            $area = $request->get('area');

            $submodelo = 0;
            $linea = 0;
            $proceso = 0;

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $query = "SELECT id FROM modelo WHERE id_gama = $id_gama AND area = '$area' AND active = 1";
            $statement=$db->prepare($query);
            $statement->execute();
            $id_modelos=$statement->fetchAll();

            $modelos_id = '';
            foreach ($id_modelos as $value) {
                $modelos_id .= $value['id'] . ',';
            }

            $modelos_id = trim($modelos_id, ',');

            $query2 = "SELECT id_submodelo FROM modelosubmodelo WHERE id_modelo in ($modelos_id)";
            $statement2=$db->prepare($query2);
            $statement2->execute();
            $id_submodelos=$statement2->fetchAll();

            $submodelos_id = '';
            foreach ($id_submodelos as $value) {
                $submodelos_id .= $value['id_submodelo'] . ',';
            }

            $submodelos_id = trim($submodelos_id, ',');

            $query3 = "SELECT * FROM submodelo WHERE id in ($submodelos_id) AND active = 1 ORDER BY nombre ASC";
            $statement3=$db->prepare($query3);
            $statement3->execute();
            $submodelos=$statement3->fetchAll();

            $gama = $this->getDoctrine()
            ->getRepository('GestionBundle:gama')
            ->find($id_gama);

            $repository4 = $this->getDoctrine()
            ->getRepository(opbasica::class);

            $query4 = $repository4->createQueryBuilder('p')
                ->where('p.tipo = :tipo')
                ->setParameter('tipo', 'OPB')
                ->getQuery();

            $opbasicas = $query4->getResult();


            return $this->render('AppBundle:Consultas/operacionesbasicas:index_all_operacionesbasicas.html.twig', array(
                'submodelos' => $submodelos,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'gama' => $gama,
                'area' => $area,
                'opbasicas' => $opbasicas
            ));

    }


    public function getOpbsSubmodelos(Request $request)
    {
        $area = $request->get('area');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT id FROM modelo WHERE area = '$area' AND active = 1";
        $statement=$db->prepare($query);
        $statement->execute();
        $id_modelos=$statement->fetchAll();

        $modelos_id = '';
        foreach ($id_modelos as $value) {
            $modelos_id .= $value['id'] . ',';
        }

        $modelos_id = trim($modelos_id, ',');

        $query2 = "SELECT id_submodelo FROM modelosubmodelo WHERE id_modelo in ($modelos_id)";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $id_submodelos=$statement2->fetchAll();

        $submodelos_id = '';
        foreach ($id_submodelos as $value) {
            $submodelos_id .= $value['id_submodelo'] . ',';
        }

        $submodelos_id = trim($submodelos_id, ',');

        $query3 = "SELECT * FROM submodelo WHERE id in ($submodelos_id) AND active = 1 ORDER BY nombre ASC";
        $statement3=$db->prepare($query3);
        $statement3->execute();
        $submodelos=$statement3->fetchAll();


        return $response = new JsonResponse(['data' => $submodelos]);

    }

    public function getOpbsLineas(Request $request)
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
        //$params2=array($lineas_id);
        $statement2->execute();
        $lineas=$statement2->fetchAll();


        return $response = new JsonResponse(['data' => $lineas]);

    }

    public function getOpbsProcesos(Request $request)
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

    public function getOpbsListados(Request $request)
    {
        $linea = $request->get('linea');
        $submodelo = $request->get('submodelo');
        $proceso = $request->get('id');

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

        $asignarprocesores = $em->getRepository('GestionBundle:asignarprocesore')->findBy(
             array('idAsignarproceso'=> $id_asignarproceso), 
             array('position' => 'ASC')
           );

        if (!empty($array_ids)) {

        $query = "SELECT id, nombre_lt FROM asignarprocesoversion WHERE id IN ($array_ids) AND active = 1 AND estado = 'PRODUCCION' GROUP BY nombre_lt, id";
        $statement=$db->prepare($query);
        $statement->execute();
        $lts=$statement->fetchAll();

        } else {

            $query = "SELECT id, nombre_lt FROM asignarprocesoversion WHERE active = 1 AND estado = 'PRODUCCION' GROUP BY nombre_lt, id";
            $statement=$db->prepare($query);
            $statement->execute();
            $lts=$statement->fetchAll();

        }

        return $response = new JsonResponse(['data' => $lts]);

    }


    public function filterAction(Request $request) {

        $id_asignarprocesoversion=$_POST['listado'];
        $id_opbasica=$_POST['opbasica'];
        $id_submodelo=$_POST['submodelo'];

        $repeticiones=[];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        //seleccionamos los id's de las tareas asignadas 
        $query = "SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion ORDER BY position";
        $statement=$db->prepare($query);
        $statement->execute();
        $id_tareas_asignadas=$statement->fetchAll();

        $tareas = [];

        for ($i=0; $i < count($id_tareas_asignadas); $i++) { 

            $id_tareaAsignada = $id_tareas_asignadas[$i]['id'];
            
            $query2 = "SELECT id_operacionbasica FROM operacion_asignada WHERE id_tareaAsignada = $id_tareaAsignada";
            $statement2=$db->prepare($query2);
            $statement2->execute();
            $opbasicas=$statement2->fetchAll();

            $query4 = "SELECT id, nombre_es FROM tarea WHERE id = (SELECT id_tarea FROM tarea_asignada WHERE id = $id_tareaAsignada)";
            $statement4=$db->prepare($query4);
            $statement4->execute();
            $tarea=$statement4->fetchAll();

            $tareas [] = $tarea;

            if (in_array($id_opbasica, array_column($opbasicas, 'id_operacionbasica'))) {

                $query3 = "SELECT repeticion FROM operacion_asignada WHERE id_tareaAsignada = $id_tareaAsignada AND id_operacionbasica = $id_opbasica";
                $statement3=$db->prepare($query3);
                $statement3->execute();
                $repeticiones_op=$statement3->fetchAll();

                $repeticiones[] = $repeticiones_op[0]['repeticion'];

                
            } else {

                $repeticiones[] = 0;

            }

        }

        $suma = array_sum($repeticiones);

        return $this->render('AppBundle:Consultas/operacionesbasicas:filter_operacionesbasicas.html.twig', array(
                'tareas' => $tareas,
                'repeticiones' => $repeticiones,
                'suma' => $suma,
                'id_asignarprocesoversion' => $id_asignarprocesoversion,
                'id_opbasica' => $id_opbasica,
                'submodelo' => $id_submodelo
        ));

    }

    public function exportAction(Request $request)
    {

        $id_asignarprocesoversion=$_POST['id_asignarprocesoversion'];
        $id_opbasica=$_POST['id_opbasica'];
        $id_submodelo=$_POST['submodelo'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $submodelo = $em->getRepository('GestionBundle:submodelo')->findOneBy(['id' => $id_submodelo]);
        $nombre_submodelo = $submodelo->getNombre();
        $lt = $em->getRepository('GestionBundle:asignarprocesoversion')->findOneBy(['id' => $id_asignarprocesoversion]);
        $nombre_lt = $lt->getNombreLt();
        $opbasica = $em->getRepository('GestionBundle:opbasica')->findOneBy(['id' => $id_opbasica]);
        $nombre_opbasica = $opbasica->getNombreEs();

        $repeticiones=[];

        //seleccionamos los id's de las tareas asignadas 
        $query = "SELECT id FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion ORDER BY position";
        $statement=$db->prepare($query);
        $statement->execute();
        $id_tareas_asignadas=$statement->fetchAll();

        $tareas = [];

        for ($i=0; $i < count($id_tareas_asignadas); $i++) { 

            $id_tareaAsignada = $id_tareas_asignadas[$i]['id'];
            
            $query2 = "SELECT id_operacionbasica FROM operacion_asignada WHERE id_tareaAsignada = $id_tareaAsignada";
            $statement2=$db->prepare($query2);
            $statement2->execute();
            $opbasicas=$statement2->fetchAll();

            $query4 = "SELECT id, nombre_es FROM tarea WHERE id = (SELECT id_tarea FROM tarea_asignada WHERE id = $id_tareaAsignada)";
            $statement4=$db->prepare($query4);
            $statement4->execute();
            $tarea=$statement4->fetchAll();

            $tareas [] = $tarea;

            if (in_array($id_opbasica, array_column($opbasicas, 'id_operacionbasica'))) {

                $query3 = "SELECT repeticion FROM operacion_asignada WHERE id_tareaAsignada = $id_tareaAsignada AND id_operacionbasica = $id_opbasica";
                $statement3=$db->prepare($query3);
                $statement3->execute();
                $repeticiones_op=$statement3->fetchAll();

                $repeticiones[] = $repeticiones_op[0]['repeticion'];

                
            } else {

                $repeticiones[] = 0;

            }

        }

        $suma = array_sum($repeticiones);

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', "SUBMODELO: " . $nombre_submodelo)
                    ->setCellValue('A2', "LISTADO DE TAREAS: " . $nombre_lt)
                    ->setCellValue('A3', "OPERACION BASICA: " . $nombre_opbasica)
                    ->setCellValue('A4', '')
                    ->setCellValue('A5', 'TAREAS')
                    ->setCellValue('B5', 'REP');


        for ($j=0; $j < count($id_tareas_asignadas); $j++) {

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . (string)($j + 7), $tareas[$j][0]['nombre_es'])
                    ->setCellValue('B' . (string)($j + 7), (string)$repeticiones[$j]);

        }

        $last_row = (count($id_tareas_asignadas) + 8);

    
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . (string)($last_row), "TOTAL OPERACIONES BASICAS")
                    ->setCellValue('B' . (string)($last_row), (string)$suma);


        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(85);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="01simple.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

        exit;

    }

}