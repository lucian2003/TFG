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
use PHPExcel_Style_NumberFormat;


use Doctrine\ORM\EntityRepository;

class ListadoTareasController extends Controller
{
   
    public function indexAction(Request $request) {

        return $this->render('AppBundle:Consultas/listadotareas:index_listadotareas.html.twig', array(
                'navlistadotareas' => 1
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

            return $this->render('AppBundle:Consultas/listadotareas:index_gama_listadotareas.html.twig', array(
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

            $back = 0;

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

            return $this->render('AppBundle:Consultas/listadotareas:index_all_listadotareas.html.twig', array(
                'submodelos' => $submodelos,
                'back' => $back,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'gama' => $gama,
                'area' => $area
            ));

    }

    public function getListSubmodelos(Request $request)
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

    public function getListLineas(Request $request)
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

    public function getListProcesos(Request $request)
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


    public function getListListados(Request $request)
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
        $id_submodelo=$_POST['submodelo'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $tareas = $this->getDoctrine()
            ->getRepository('GestionBundle:tareaAsignada')
            ->findByIdAsignarprocesoversion($id_asignarprocesoversion, array('position' => 'ASC'));

        return $this->render('AppBundle:Consultas/listadotareas:filter_listadotareas.html.twig', array(
                'tareas' => $tareas,
                'listado' => $id_asignarprocesoversion,
                'submodelo' => $id_submodelo
        ));

    }

    public function getOpsList(Request $request)
    {
        
        $id_tarea = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT id FROM tarea_asignada WHERE id = $id_tarea";
        $statement=$db->prepare($query);
        $statement->execute();
        $tareaAsignada=$statement->fetchAll();

        $id = $tareaAsignada[0]['id'];

        $query2 = "SELECT id_operacionbasica FROM operacion_asignada WHERE id_tareaAsignada = $id";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $id_ops=$statement2->fetchAll();


        $ops = [];

        for ($i=0; $i < count($id_ops); $i++) { 
            
            $id_op = $id_ops[$i]['id_operacionbasica'];

            $query3 = "SELECT * FROM opbasica WHERE id = $id_op AND active = 1";
            $statement3=$db->prepare($query3);
            $statement3->execute();
            $op=$statement3->fetchAll();

            $ops [] = $op;

        }

        $query4 = "SELECT * FROM operacion_asignada WHERE id_tareaAsignada = $id";
        $statement4=$db->prepare($query4);
        $statement4->execute();
        $comentarios=$statement4->fetchAll();

        
        return $response = new JsonResponse([
            'data' => $ops,
            'data2' => $comentarios

        ]);

    }


    public function exportAction(Request $request)
    {

        $id_asignarprocesoversion=$_POST['listado'];
        $id_submodelo=$_POST['submodelo'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM tarea_asignada WHERE id_asignarprocesoversion = $id_asignarprocesoversion ORDER BY position ASC";
        $statement=$db->prepare($query);
        $statement->execute();
        $tareas=$statement->fetchAll();

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'LISTADO DE TAREAS')
                    ->setCellValue('A3', 'TAREA')
                    ->setCellValue('B3', 'TIEMPO');


        for ($j=0; $j < count($tareas); $j++) {

            $id_tarea=$tareas[$j]['id_tarea'];

            $query2 = "SELECT nombre_es FROM tarea WHERE id = $id_tarea";
            $statement2=$db->prepare($query2);
            $statement2->execute();
            $nombre_tarea=$statement2->fetchAll();

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . (string)($j + 4), $nombre_tarea[0]['nombre_es'])
                    ->setCellValue('B' . (string)($j + 4), ($tareas[$j]['tiempo']/86400));

            $objPHPExcel->getActiveSheet()
                    ->getStyle('B' . (string)($j + 4))
                    ->getNumberFormat()
                    ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4);

        }

        $objPHPExcel->getActiveSheet()->setTitle('Simple');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(80);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);

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