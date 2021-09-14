<?php

namespace AppBundle\Controller;


use GestionBundle\Entity\planta;
use GestionBundle\Entity\submodelo;
use GestionBundle\Entity\linea;
use GestionBundle\Entity\proceso;
use GestionBundle\Entity\tarea;
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

use Doctrine\ORM\EntityRepository;

class ConfiguracionesLineasController extends Controller
{
   
    public function indexAction(Request $request) {

        return $this->render('AppBundle:Consultas/configuracioneslineas:index_configuracioneslineas.html.twig', array(
                'navconfiguraciones' => 1
        ));

    }

    public function langAction (Request $request) {
        return $this->render('AppBundle:Consultas/configuracioneslineas:index_configuracioneslineas.html.twig');
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

            return $this->render('AppBundle:Consultas/configuracioneslineas:index_gama_configuracioneslineas.html.twig', array(
                'area' => $area,
                'gamas' => $gamas
            ));

    }

    public function gamaAction(Request $request) {

        if (isset($_POST['submodelo']) && isset($_POST['linea']) && isset($_POST['proceso'])) {

            $submodelo = $_POST['submodelo'];
            $linea = $_POST['linea'];
            $proceso = $_POST['proceso'];

            $id_gama = $request->get('gama');
            $area = $request->get('area');

            $back = 1;

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

            $query4 = "SELECT id_linea FROM asignarproceso WHERE id_submodelo in ($submodelos_id) AND active = 1";
            $statement4=$db->prepare($query4);
            $statement4->execute();
            $id_lineas=$statement4->fetchAll();

            $lineas_id = '';
            foreach ($id_lineas as $value) {
                $lineas_id .= $value['id_linea'] . ',';
            }

            $lineas_id = trim($lineas_id, ',');

            $query5 = "SELECT * FROM linea WHERE id in ($lineas_id) AND active = 1 ORDER BY nombre ASC";
            $statement5=$db->prepare($query5);
            $statement5->execute();
            $lineas=$statement5->fetchAll();

            $gama = $this->getDoctrine()
            ->getRepository('GestionBundle:gama')
            ->find($id_gama);


            $response = $this->render('AppBundle:Consultas/configuracioneslineas:index_all_configuracioneslineas.html.twig', array(
                'submodelos' => $submodelos,
                'back' => $back,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'gama' => $gama,
                'area' => $area,
                'lineas' => $lineas
            ));

        } else {

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

            $query4 = "SELECT id_linea FROM asignarproceso WHERE id_submodelo in ($submodelos_id) AND active = 1";
            $statement4=$db->prepare($query4);
            $statement4->execute();
            $id_lineas=$statement4->fetchAll();

            $lineas_id = '';
            foreach ($id_lineas as $value) {
                $lineas_id .= $value['id_linea'] . ',';
            }

            $lineas_id = trim($lineas_id, ',');

            $query5 = "SELECT * FROM linea WHERE id in ($lineas_id) AND active = 1 ORDER BY nombre ASC";
            $statement5=$db->prepare($query5);
            $statement5->execute();
            $lineas=$statement5->fetchAll();

            $gama = $this->getDoctrine()
            ->getRepository('GestionBundle:gama')
            ->find($id_gama);


            $response = $this->render('AppBundle:Consultas/configuracioneslineas:index_all_configuracioneslineas.html.twig', array(
                'submodelos' => $submodelos,
                'back' => $back,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'gama' => $gama,
                'area' => $area,
                'lineas' => $lineas
            ));
        }

        return $response;

    }


    public function getConfAreaModelos(Request $request)
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

    public function getConfSubmodelos(Request $request)
    {
        $linea = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT id_submodelo FROM asignarproceso WHERE id_linea = $linea AND id_proceso = 1 AND active = 1";
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
        //$params2=array($lineas_id);
        $statement2->execute();
        $submodelos=$statement2->fetchAll();


        return $response = new JsonResponse(['data' => $submodelos]);

    }

    public function getConfLineas(Request $request)
    {
        $submodelo = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();


        $query = "SELECT id_linea FROM asignarproceso WHERE id_submodelo = $submodelo AND id_proceso = 1 AND active = 1";
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

    public function getConfProcesos(Request $request)
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

    public function getConfAreaLineas(Request $request)
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

        $query3 = "SELECT id_linea FROM asignarproceso WHERE id_submodelo in ($submodelos_id) AND active = 1";
        $statement3=$db->prepare($query3);
        $statement3->execute();
        $id_lineas=$statement3->fetchAll();

        $lineas_id = '';
        foreach ($id_lineas as $value) {
            $lineas_id .= $value['id_linea'] . ',';
        }

        $lineas_id = trim($lineas_id, ',');

        $query4 = "SELECT * FROM linea WHERE id in ($lineas_id) AND active = 1 ORDER BY nombre ASC";
        $statement4=$db->prepare($query4);
        $statement4->execute();
        $lineas=$statement4->fetchAll();

        return $response = new JsonResponse(['data' => $lineas]);

    }

    public function searchAction(Request $request) {

        $submodelo=$_POST['submodelo'];
        $id_linea=$_POST['linea'];
        $id_proceso=$_POST['proceso'];
        $area=$_POST['area'];
        $id_gama=$_POST['gama'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->findOneBy(['idSubmodelo' => $submodelo, 'idLinea'=> $id_linea, 'idProceso'=> $id_proceso]);

        $id_asignarproceso = $asignarproceso->getId();

        $linea = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->find($id_linea);

        $productividad = floatval(str_replace(',', '.', $linea->getProductividad()));

        if (empty($productividad)) {
            
            $productividad = 1;
        }

        $repository = $this->getDoctrine()
            ->getRepository(configuracionlinea::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.idAsignarproceso = :id_asignarproceso')
            ->andWhere('p.estado = :estado')
            ->setParameter('id_asignarproceso', $id_asignarproceso)
            ->setParameter('estado', 'PRODUCCION')
            ->orderBy('p.version', 'DESC')
            ->getQuery();

        $configuracionlineas = $query->getResult();

        $gama = $this->getDoctrine()
            ->getRepository('GestionBundle:gama')
            ->find($id_gama);


        return $this->render('AppBundle:Consultas/configuracioneslineas:filter_configuracioneslineas.html.twig', array(
                'configuracionlineas' => $configuracionlineas,
                'productividad' => $productividad,
                'idAsignarproceso' => $id_asignarproceso,
                'submodelo' => $submodelo,
                'linea' => $id_linea,
                'proceso' => $id_proceso,
                'area' => $area,
                'gama' => $gama
            ));

    }

    public function detailsAction(Request $request) {

        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $proceso=$_POST['proceso'];
        $area=$_POST['area'];
        $id_gama=$_POST['gama'];

        $submodelo_entity = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->find($submodelo);

        $linea_entity = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->find($linea);

        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $configuracionlinea = $this->getDoctrine()
            ->getRepository('GestionBundle:configuracionlinea')
            ->findOneById($id_configuracionLinea);

        $operarios=$configuracionlinea->getOperarios();

        $gama = $this->getDoctrine()
            ->getRepository('GestionBundle:gama')
            ->find($id_gama);

        return $this->render('AppBundle:Consultas/configuracioneslineas:details_configuracioneslineas.html.twig', array(
                'idConfiguracionLinea' => $id_configuracionLinea,
                'operarios' => $operarios,
                'submodelo' => $submodelo,
                'submodelo_entity' => $submodelo_entity,
                'linea_entity' => $linea_entity,
                'linea' => $linea,
                'proceso' => $proceso,
                'area' => $area,
                'gama' => $gama
            ));

    }

    public function ajaxAction(Request $request) {


        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $repository = $this->getDoctrine()
                ->getRepository(detalleconfiguracion::class);

        if (!empty($_POST['operarios'])) {

            $query = $repository->createQueryBuilder('p')
                ->where('p.idConfiguracionlinea = :idConfiguracionlinea')
                ->andWhere('p.operario = :operario')
                ->setParameter('idConfiguracionlinea', $id_configuracionLinea)
                ->setParameter('operario', $_POST['operarios'])
                ->orderBy('p.position', 'ASC')
                ->getQuery();

            $detalleconfiguracions = $query->getResult();

        } else {
            
            // no muestro nada
        }

        return $this->render('AppBundle:Consultas/configuracioneslineas:ajax_configuracioneslineas.html.twig', array(
                'idConfiguracionLinea' => $id_configuracionLinea,
                'detalleconfiguracions' => $detalleconfiguracions

            ));

    }

        public function ajaxOpsAction(Request $request) {

        $id_configuracionLinea=$_POST['idConfiguracionLinea'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $repository = $this->getDoctrine()
                ->getRepository(detalleconfiguracion::class);

        if (!empty($_POST['operarios'])) {

            $query = $repository->createQueryBuilder('p')
                ->where('p.idConfiguracionlinea = :idConfiguracionlinea')
                ->andWhere('p.operario = :operario')
                ->setParameter('idConfiguracionlinea', $id_configuracionLinea)
                ->setParameter('operario', $_POST['operarios'])
                ->orderBy('p.position', 'ASC')
                ->getQuery();

            $detalleconfiguracions = $query->getResult();

        } else {

            
            }

        return $this->render('AppBundle:Consultas/configuracioneslineas:ajax_ops_configuracioneslineas.html.twig', array(
                'idConfiguracionLinea' => $id_configuracionLinea,
                'detalleconfiguracions' => $detalleconfiguracions,
                'operario' => $_POST['operarios']
            ));

    }

    public function getOps(Request $request)
    {
        
        $id_detalleconfiguracion = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT id_tareaAsignada FROM detalleconfiguracion WHERE id = $id_detalleconfiguracion";
        $statement=$db->prepare($query);
        $statement->execute();
        $tareaAsignada=$statement->fetchAll();

        $id = $tareaAsignada[0]['id_tareaAsignada'];

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

}