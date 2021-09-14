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

class OperacionesBasicasConfController extends Controller
{
   
    public function indexAction() {

        return $this->render('AppBundle:Consultas/operacionesbasicasconf:index_operacionesbasicasconf.html.twig', array(
                'navopbasicasconf' => 1
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

            return $this->render('AppBundle:Consultas/operacionesbasicasconf:index_gama_operacionesbasicasconf.html.twig', array(
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

            return $this->render('AppBundle:Consultas/operacionesbasicasconf:index_all_operacionesbasicasconf.html.twig', array(
                'submodelos' => $submodelos,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'gama' => $gama,
                'area' => $area,
                'opbasicas' => $opbasicas
            ));

    }


    public function getOpbsConfSubmodelos(Request $request)
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

    public function getOpbsConfLineas(Request $request)
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

    public function getOpbsConfProcesos(Request $request)
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

    public function getOpbsConfiguraciones(Request $request)
    {
        $linea = $request->get('linea');
        $submodelo = $request->get('submodelo');
        $proceso = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $submodelo, 'idLinea'=> $linea, 'idProceso'=> $proceso]);

        $id_asignarproceso = $asignarproceso->getId();

        $query = "SELECT id, nombre FROM configuracionlinea WHERE id_asignarproceso = $id_asignarproceso AND estado = 'PRODUCCION' GROUP BY nombre, id";
        $statement=$db->prepare($query);
        $statement->execute();
        $configuraciones=$statement->fetchAll();

        return $response = new JsonResponse(['data' => $configuraciones]);

    }


    public function filterAction(Request $request) {

        $id_configuracion=$_POST['configuracion'];
        $id_opbasica=$_POST['opbasica'];
        $id_submodelo=$_POST['submodelo'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $repeticiones=[];
        $estaciones=[];
        $operarios=[];

        //seleccionamos las estaciones de detalleconfiguracion con el id de la configuracion
        $query = "SELECT estacion FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracion";
        $statement=$db->prepare($query);
        $statement->execute();
        $estaciones=$statement->fetchAll();

        //seleccionamos los operarios de detalleconfiguracion con el id de la configuracion
        $query5 = "SELECT operario FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracion";
        $statement5=$db->prepare($query5);
        $statement5->execute();
        $operarios=$statement5->fetchAll();

        //seleccionamos los id's de las tareas asignadas
        $query6 = "SELECT id_tareaAsignada FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracion ORDER BY position";
        $statement6=$db->prepare($query6);
        $statement6->execute();
        $id_tareas_asignadas=$statement6->fetchAll();

        $tareas = [];

        for ($i=0; $i < count($id_tareas_asignadas); $i++) { 

            $id_tareaAsignada = $id_tareas_asignadas[$i]['id_tareaAsignada'];
            
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

        return $this->render('AppBundle:Consultas/operacionesbasicasconf:filter_operacionesbasicasconf.html.twig', array(
                'tareas' => $tareas,
                'repeticiones' => $repeticiones,
                'suma' => $suma,
                'estaciones' => $estaciones,
                'operarios' => $operarios,
                'id_configuracion' => $id_configuracion,
                'id_opbasica' => $id_opbasica,
                'submodelo' => $id_submodelo
        ));

    }


    public function exportAction(Request $request)
    {

        $id_configuracion=$_POST['id_configuracion'];
        $id_opbasica=$_POST['id_opbasica'];
        $id_submodelo=$_POST['submodelo'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $submodelo = $em->getRepository('GestionBundle:submodelo')->findOneBy(['id' => $id_submodelo]);
        $nombre_submodelo = $submodelo->getNombre();
        $configuracion = $em->getRepository('GestionBundle:configuracionlinea')->findOneBy(['id' => $id_configuracion]);
        $nombre_configuracion = $configuracion->getNombre();
        $opbasica = $em->getRepository('GestionBundle:opbasica')->findOneBy(['id' => $id_opbasica]);
        $nombre_opbasica = $opbasica->getNombreEs();

        $repeticiones=[];
        $estaciones=[];
        $operarios=[];

        //seleccionamos las estaciones de detalleconfiguracion con el id de la configuracion
        $query = "SELECT estacion FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracion";
        $statement=$db->prepare($query);
        $statement->execute();
        $estaciones=$statement->fetchAll();

        //seleccionamos los operarios de detalleconfiguracion con el id de la configuracion
        $query5 = "SELECT operario FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracion";
        $statement5=$db->prepare($query5);
        $statement5->execute();
        $operarios=$statement5->fetchAll();

        //seleccionamos los id's de las tareas asignadas
        $query6 = "SELECT id_tareaAsignada FROM detalleconfiguracion WHERE id_configuracionlinea = $id_configuracion ORDER BY position";
        $statement6=$db->prepare($query6);
        $statement6->execute();
        $id_tareas_asignadas=$statement6->fetchAll();

        $tareas = [];

        for ($i=0; $i < count($id_tareas_asignadas); $i++) { 

            $id_tareaAsignada = $id_tareas_asignadas[$i]['id_tareaAsignada'];
            
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
                    ->setCellValue('A2', "LISTADO DE TAREAS: " . $nombre_configuracion)
                    ->setCellValue('A3', "OPERACION BASICA: " . $nombre_opbasica)
                    ->setCellValue('A4', '')
                    ->setCellValue('A5', 'TAREAS')
                    ->setCellValue('B5', 'Nº EST')
                    ->setCellValue('C5', 'Nº OPS')
                    ->setCellValue('D5', 'REP');


        for ($j=0; $j < count($tareas); $j++) {

            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . (string)($j + 7), $tareas[$j][0]['nombre_es'])
                    ->setCellValue('B' . (string)($j + 7), (string)$estaciones[$j]['estacion'])
                    ->setCellValue('C' . (string)($j + 7), (string)$operarios[$j]['operario'])
                    ->setCellValue('D' . (string)($j + 7), (string)$repeticiones[$j]);

        }

        $last_row = (count($tareas) + 8);

    
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . (string)($last_row), "TOTAL OPERACIONES BASICAS")
                    ->setCellValue('D' . (string)($last_row), (string)$suma);


        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(65);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
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