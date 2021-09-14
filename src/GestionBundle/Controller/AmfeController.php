<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\amfe;
use GestionBundle\Entity\detalleamfe;
use GestionBundle\Entity\tarea_amfe;
use GestionBundle\Entity\planta;
use GestionBundle\Entity\tareaAsignada;
use GestionBundle\Entity\asignarproceso;
use GestionBundle\Entity\asignarprocesore;
use GestionBundle\Entity\asignarprocesoversion;

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
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Doctrine\ORM\EntityRepository;


class AmfeController extends Controller
{
   
    public function indexAction() {

        $repository = $this->getDoctrine()
            ->getRepository(amfe::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.tipo = :tipo')
            ->setParameter('tipo', 'STD')
            ->getQuery();

        $amfes = $query->getResult();

        return $this->render('GestionBundle:gestion/amfe:index_amfe.html.twig', array(
            'navamfe' => 1,
            'amfes' => $amfes
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/amfe:index_amfe.html.twig');
    }

    public function createAction(Request $request) {

        $amfe = new amfe;

        $form = $this->createFormBuilder($amfe)

            ->add('nombre', TextType::class, array('label' => 'nombre', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $nombre = $form['nombre']->getData();

            $amfe->setNombre($nombre);
            $amfe->setTipo("STD");
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($amfe);
            $em->flush();

            return $this->redirectToRoute('app_index_amfe');
        }

        return $this->render('GestionBundle:gestion/amfe:create_amfe.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function editAction(Request $request) {

        if (isset($_POST['idAmfe'])) {
            $id=$_POST['idAmfe'];
        } else {
            $id = $request->get('id');
        }

        $amfe = $this->getDoctrine()
            ->getRepository('GestionBundle:amfe')
            ->find($id);

        $detalleamfes = $this->getDoctrine()
            ->getRepository('GestionBundle:detalleamfe')
            ->findByIdAmfe($id);

        return $this->render('GestionBundle:gestion/amfe:edit_amfe.html.twig', array(
            'amfe'=> $amfe,
            'detalleamfes' => $detalleamfes
        ));
    }

    public function createDetalleAction(Request $request) {

        $id_amfe=$_POST['amfe'];

        $detalleamfe = new detalleamfe;

        $form = $this->createFormBuilder($detalleamfe)

            ->add('modo_fallo', TextType::class, array('label' => 'MODO DE FALLO', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('gravedad', ChoiceType::class, array('label' => 'GRAVEDAD', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('frecuencia', ChoiceType::class, array('label' => 'FRECUENCIA', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('deteccion', ChoiceType::class, array('label' => 'DETECCION', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('accion', TextType::class, array('label' => 'ACCIONES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('gravedad_t', ChoiceType::class, array('label' => 'GRAVEDAD TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('frecuencia_t', ChoiceType::class, array('label' => 'FRECUENCIA TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('deteccion_t', ChoiceType::class, array('label' => 'DETECCION TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))

            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $modo_fallo = $form['modo_fallo']->getData();
            $gravedad = $form['gravedad']->getData();
            $frecuencia = $form['frecuencia']->getData();
            $deteccion = $form['deteccion']->getData();
            $accion = $form['accion']->getData();
            $gravedad_t = $form['gravedad_t']->getData();
            $frecuencia_t = $form['frecuencia_t']->getData();
            $deteccion_t = $form['deteccion_t']->getData();

            $amfe = $this->getDoctrine()
                ->getRepository('GestionBundle:amfe')
                ->find($id_amfe);

            $npr=$gravedad*$frecuencia*$deteccion;
            
            if ( ($gravedad_t == "") || ($frecuencia_t == "") || ($deteccion_t == "") ) {
                
                $detalleamfe->setModoFallo($modo_fallo);
                $detalleamfe->setGravedad($gravedad);
                $detalleamfe->setFrecuencia($frecuencia);
                $detalleamfe->setDeteccion($deteccion);
                $detalleamfe->setNpr($npr);
                $detalleamfe->setAccion($accion);
                $detalleamfe->setIdAmfe($amfe);
            
                $em = $this->getDoctrine()->getManager();

                $em->persist($detalleamfe);
                $em->flush();

            } else {

                $nuevo_npr=$gravedad_t*$frecuencia_t*$deteccion_t;

                $detalleamfe->setModoFallo($modo_fallo);
                $detalleamfe->setGravedad($gravedad);
                $detalleamfe->setFrecuencia($frecuencia);
                $detalleamfe->setDeteccion($deteccion);
                $detalleamfe->setNpr($npr);
                $detalleamfe->setAccion($accion);                
                $detalleamfe->setGravedadT($gravedad_t);
                $detalleamfe->setFrecuenciaT($frecuencia_t);
                $detalleamfe->setDeteccionT($deteccion_t);
                $detalleamfe->setNuevoNpr($nuevo_npr);     
                $detalleamfe->setIdAmfe($amfe);
                
                $em = $this->getDoctrine()->getManager();

                $em->persist($detalleamfe);
                $em->flush();
            }

            $response = $this->forward('GestionBundle:amfe:edit', [
                'id' => $id_amfe
            ]);

            return $response;
        }

        return $this->render('GestionBundle:gestion/amfe:create_detalle_amfe.html.twig', array(
            'form' => $form->createView(), 'amfe' => $id_amfe, 'idDetalleamfe' => 0
        ));
    }

    public function editDetalleAction(Request $request) {


        $id_detalleamfe=$_POST['idDetalleamfe'];

        $detalleamfe = $this->getDoctrine()
            ->getRepository('GestionBundle:detalleamfe')
            ->find($id_detalleamfe);

        $id_amfe=$detalleamfe->getIdAmfe();

        $amfe = $this->getDoctrine()
            ->getRepository('GestionBundle:amfe')
            ->find($id_amfe);

        $id=$amfe->getId();

        $form = $this->createFormBuilder($detalleamfe)

            ->add('modo_fallo', TextType::class, array('label' => 'MODO DE FALLO', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('gravedad', ChoiceType::class, array('label' => 'GRAVEDAD', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('frecuencia', ChoiceType::class, array('label' => 'FRECUENCIA', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('deteccion', ChoiceType::class, array('label' => 'DETECCION', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('accion', TextType::class, array('label' => 'ACCIONES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('gravedad_t', ChoiceType::class, array('label' => 'GRAVEDAD TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('frecuencia_t', ChoiceType::class, array('label' => 'FRECUENCIA TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('deteccion_t', ChoiceType::class, array('label' => 'DETECCION TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))

            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $modo_fallo = $form['modo_fallo']->getData();
            $gravedad = $form['gravedad']->getData();
            $frecuencia = $form['frecuencia']->getData();
            $deteccion = $form['deteccion']->getData();
            $accion = $form['accion']->getData();
            $gravedad_t = $form['gravedad_t']->getData();
            $frecuencia_t = $form['frecuencia_t']->getData();
            $deteccion_t = $form['deteccion_t']->getData();

            $amfe = $this->getDoctrine()
                ->getRepository('GestionBundle:amfe')
                ->find($id_amfe);

            $npr=$gravedad*$frecuencia*$deteccion;
            
            if ( ($gravedad_t == "") || ($frecuencia_t == "") || ($deteccion_t == "") ) {
                
                $detalleamfe->setModoFallo($modo_fallo);
                $detalleamfe->setGravedad($gravedad);
                $detalleamfe->setFrecuencia($frecuencia);
                $detalleamfe->setDeteccion($deteccion);
                $detalleamfe->setNpr($npr);
                $detalleamfe->setAccion($accion);
                $detalleamfe->setIdAmfe($amfe);
            
                $em = $this->getDoctrine()->getManager();

                $em->persist($detalleamfe);
                $em->flush();

            } else {

                $nuevo_npr=$gravedad_t*$frecuencia_t*$deteccion_t;

                $detalleamfe->setModoFallo($modo_fallo);
                $detalleamfe->setGravedad($gravedad);
                $detalleamfe->setFrecuencia($frecuencia);
                $detalleamfe->setDeteccion($deteccion);
                $detalleamfe->setNpr($npr);
                $detalleamfe->setAccion($accion);                
                $detalleamfe->setGravedadT($gravedad_t);
                $detalleamfe->setFrecuenciaT($frecuencia_t);
                $detalleamfe->setDeteccionT($deteccion_t);
                $detalleamfe->setNuevoNpr($nuevo_npr);     
                $detalleamfe->setIdAmfe($amfe);
                
                $em = $this->getDoctrine()->getManager();

                $em->persist($detalleamfe);
                $em->flush();
            }

            $response = $this->forward('GestionBundle:amfe:edit', [
                'id' => $id_amfe
            ]);

            return $response;
        }

        return $this->render('GestionBundle:gestion/amfe:create_detalle_amfe.html.twig', array(
            'form' => $form->createView(), 'amfe' => $id, 'idDetalleamfe' => $id_detalleamfe
        ));
    }

    public function deleteDetalleAction(Request $request) {

        $id_detalleamfe=$_POST['idDetalleamfe'];

        $em = $this->getDoctrine()->getManager();

        $detalleamfe = $em->getRepository('GestionBundle:detalleamfe')->find($id_detalleamfe);

        $em->remove($detalleamfe);
        $em->flush();

        $id_amfe = $detalleamfe->getIdAmfe();

        $response = $this->forward('GestionBundle:amfe:edit', [
                'id' => $id_amfe
            ]);

        return $response;
    }


    public function indexAmfepartAction() {

        if (isset($_POST['tarea']) && isset($_POST['area']) && isset($_POST['modelo']) && isset($_POST['submodelo']) && isset($_POST['linea']) && isset($_POST['proceso']) && isset($_POST['listado'])) {

            $id_tarea=$_POST['tarea'];
            $area = $_POST['area'];
            $modelo = $_POST['modelo'];
            $submodelo = $_POST['submodelo'];
            $linea = $_POST['linea'];
            $proceso = $_POST['proceso'];
            $listado = $_POST['listado'];

            $back = 1;

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $query2 = "SELECT nombre_es FROM tarea WHERE id = (SELECT id_tarea FROM tarea_asignada WHERE id = $id_tarea)";
            $statement2=$db->prepare($query2);
            $statement2->execute();
            $nombretarea=$statement2->fetchAll();

            $nombre_tarea = $nombretarea[0]['nombre_es'];
            
        } else {

            $id_tarea=0;
            $area = '';
            $modelo = 0;
            $submodelo = 0;
            $linea = 0;
            $proceso = 0;
            $listado = 0;
            $nombre_tarea='';

            $back = 0;
        }

       return $this->render('GestionBundle:gestion/amfe:index_amfepart.html.twig', array(
                'navamfepart' => 1,
                'back' =>$back,
                'tarea' => $id_tarea,
                'area' => $area,
                'modelo' => $modelo,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'listado' => $listado,
                'nombre_tarea' => $nombre_tarea
            ));
    }

    public function getAmfepartModelos(Request $request)
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

    public function getAmfepartSubmodelos(Request $request)
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

    public function getAmfepartLineas(Request $request)
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

    public function getAmfepartProcesos(Request $request)
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

    public function getAmfepartListados(Request $request)
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

    public function getAmfepartTareas(Request $request)
    {
        $listado = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT id_tarea FROM tarea_asignada WHERE id_asignarprocesoversion = $listado ORDER BY position ASC";
        $statement=$db->prepare($query);
        $statement->execute();
        $id_tareas=$statement->fetchAll();

        $tareas_id = '';
            foreach ($id_tareas as $value) {
                $tareas_id .= $value['id_tarea'] . ',';
            }

        $tareas_ids = trim($tareas_id, ',');

        $query2 = "SELECT id, nombre_es FROM tarea WHERE id IN ($tareas_ids) GROUP BY nombre_es, id";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $tareas=$statement2->fetchAll();

        return $response = new JsonResponse(['data' => $tareas]);

    }

    public function createAmfepartAction(Request $request) {

        $id_tarea=$_POST['tarea'];
        $area = $_POST['area'];
        $modelo = $_POST['modelo'];
        $submodelo = $_POST['submodelo'];
        $linea = $_POST['linea'];
        $proceso = $_POST['proceso'];
        $listado = $_POST['listado'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM amfe WHERE id IN (SELECT id_amfe FROM tarea_amfe WHERE id_tareaAsignada = $id_tarea) AND tipo = 'PART'";
        $statement=$db->prepare($query);
        $statement->execute();
        $nombres_amfes=$statement->fetchAll();

        
        $p_especifico = 0;
            foreach ($nombres_amfes as $item) {   
                if ($item['nombre'] == 'PROCESO CRÍTICO ESPECÍFICO') {
                    $p_especifico = 1;
                    break;
                } 
            }

        if ($p_especifico == 0) { //no existe el proceso especifico
            
            $tarea_asignada = $em->getRepository('GestionBundle:tareaAsignada')->find($id_tarea);

            $amfe = new amfe;

            $amfe->setNombre("PROCESO CRÍTICO ESPECÍFICO");
            $amfe->setTipo("PART");

            $em->persist($amfe);
            $em->flush();

            $tarea_amfe = new tarea_amfe;

            $tarea_amfe->setIdTareaAsignada($tarea_asignada);
            $tarea_amfe->setIdAmfe($amfe);

            $em->persist($tarea_amfe);
            $em->flush();

        }

        $query = "SELECT * FROM amfe WHERE id IN (SELECT id_amfe FROM tarea_amfe WHERE id_tareaAsignada = $id_tarea) AND tipo = 'PART'";
        $statement=$db->prepare($query);
        $statement->execute();
        $amfes=$statement->fetchAll();

        $query2 = "SELECT nombre_es FROM tarea WHERE id = $id_tarea";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $nombretarea=$statement2->fetchAll();

        $nombre_tarea = $nombretarea[0]['nombre_es'];

        return $this->render('GestionBundle:gestion/amfe:ajax_amfepart.html.twig', array(
            'amfes' => $amfes,
            'tarea' => $id_tarea,
            'area' => $area,
            'modelo' => $modelo,
            'submodelo' => $submodelo,
            'linea' => $linea,
            'proceso' => $proceso,
            'listado' => $listado,
            'nombre_tarea' => $nombre_tarea
        ));

    }

    public function editAmfepartAction(Request $request) {

        $id_tarea=$_POST['tarea'];
        $area = $_POST['area'];
        $modelo = $_POST['modelo'];
        $submodelo = $_POST['submodelo'];
        $linea = $_POST['linea'];
        $proceso = $_POST['proceso'];
        $listado = $_POST['listado'];


        if (isset($_POST['idAmfe'])) {
            $id=$_POST['idAmfe'];
        } else {
            $id = $request->get('id');
        }

        $amfe = $this->getDoctrine()
            ->getRepository('GestionBundle:amfe')
            ->find($id);

        $detalleamfes = $this->getDoctrine()
            ->getRepository('GestionBundle:detalleamfe')
            ->findByIdAmfe($id);

        return $this->render('GestionBundle:gestion/amfe:edit_amfepart.html.twig', array(
            'amfe'=> $amfe,
            'detalleamfes' => $detalleamfes,
            'tarea' => $id_tarea,
            'area' => $area,
            'modelo' => $modelo,
            'submodelo' => $submodelo,
            'linea' => $linea,
            'proceso' => $proceso,
            'listado' => $listado
        ));
    }

    public function createDetalleAmfepartAction(Request $request) {

        $id_amfe=$_POST['amfe'];

        $id_tarea=$_POST['tarea'];
        $area = $_POST['area'];
        $modelo = $_POST['modelo'];
        $submodelo = $_POST['submodelo'];
        $linea = $_POST['linea'];
        $proceso = $_POST['proceso'];
        $listado = $_POST['listado'];

        $detalleamfe = new detalleamfe;

        $form = $this->createFormBuilder($detalleamfe)

            ->add('modo_fallo', TextType::class, array('label' => 'MODO DE FALLO', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('gravedad', ChoiceType::class, array('label' => 'GRAVEDAD', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('frecuencia', ChoiceType::class, array('label' => 'FRECUENCIA', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('deteccion', ChoiceType::class, array('label' => 'DETECCION', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('accion', TextType::class, array('label' => 'ACCIONES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('gravedad_t', ChoiceType::class, array('label' => 'GRAVEDAD TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('frecuencia_t', ChoiceType::class, array('label' => 'FRECUENCIA TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('deteccion_t', ChoiceType::class, array('label' => 'DETECCION TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))

            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $modo_fallo = $form['modo_fallo']->getData();
            $gravedad = $form['gravedad']->getData();
            $frecuencia = $form['frecuencia']->getData();
            $deteccion = $form['deteccion']->getData();
            $accion = $form['accion']->getData();
            $gravedad_t = $form['gravedad_t']->getData();
            $frecuencia_t = $form['frecuencia_t']->getData();
            $deteccion_t = $form['deteccion_t']->getData();

            $amfe = $this->getDoctrine()
                ->getRepository('GestionBundle:amfe')
                ->find($id_amfe);

            $npr=$gravedad*$frecuencia*$deteccion;
            
            if ( ($gravedad_t == "") || ($frecuencia_t == "") || ($deteccion_t == "") ) {
                
                $detalleamfe->setModoFallo($modo_fallo);
                $detalleamfe->setGravedad($gravedad);
                $detalleamfe->setFrecuencia($frecuencia);
                $detalleamfe->setDeteccion($deteccion);
                $detalleamfe->setNpr($npr);
                $detalleamfe->setAccion($accion);
                $detalleamfe->setIdAmfe($amfe);
            
                $em = $this->getDoctrine()->getManager();

                $em->persist($detalleamfe);
                $em->flush();

            } else {

                $nuevo_npr=$gravedad_t*$frecuencia_t*$deteccion_t;

                $detalleamfe->setModoFallo($modo_fallo);
                $detalleamfe->setGravedad($gravedad);
                $detalleamfe->setFrecuencia($frecuencia);
                $detalleamfe->setDeteccion($deteccion);
                $detalleamfe->setNpr($npr);
                $detalleamfe->setAccion($accion);                
                $detalleamfe->setGravedadT($gravedad_t);
                $detalleamfe->setFrecuenciaT($frecuencia_t);
                $detalleamfe->setDeteccionT($deteccion_t);
                $detalleamfe->setNuevoNpr($nuevo_npr);     
                $detalleamfe->setIdAmfe($amfe);
                
                $em = $this->getDoctrine()->getManager();

                $em->persist($detalleamfe);
                $em->flush();
            }

            $response = $this->forward('GestionBundle:amfe:editAmfepart', [
                'id' => $id_amfe,
                'tarea' => $id_tarea,
                'area' => $area,
                'modelo' => $modelo,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'listado' => $listado
            ]);

            return $response;
        }

        return $this->render('GestionBundle:gestion/amfe:create_detalle_amfepart.html.twig', array(
            'form' => $form->createView(), 
            'amfe' => $id_amfe, 
            'idDetalleamfe' => 0,
            'tarea' => $id_tarea,
            'area' => $area,
            'modelo' => $modelo,
            'submodelo' => $submodelo,
            'linea' => $linea,
            'proceso' => $proceso,
            'listado' => $listado
        ));
    }

    public function editDetalleAmfepartAction(Request $request) {


        $id_detalleamfe=$_POST['idDetalleamfe'];

        $id_tarea=$_POST['tarea'];
        $area = $_POST['area'];
        $modelo = $_POST['modelo'];
        $submodelo = $_POST['submodelo'];
        $linea = $_POST['linea'];
        $proceso = $_POST['proceso'];
        $listado = $_POST['listado'];

        $detalleamfe = $this->getDoctrine()
            ->getRepository('GestionBundle:detalleamfe')
            ->find($id_detalleamfe);

        $id_amfe=$detalleamfe->getIdAmfe();

        $amfe = $this->getDoctrine()
            ->getRepository('GestionBundle:amfe')
            ->find($id_amfe);

        $id=$amfe->getId();

        $form = $this->createFormBuilder($detalleamfe)

            ->add('modo_fallo', TextType::class, array('label' => 'MODO DE FALLO', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('gravedad', ChoiceType::class, array('label' => 'GRAVEDAD', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('frecuencia', ChoiceType::class, array('label' => 'FRECUENCIA', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('deteccion', ChoiceType::class, array('label' => 'DETECCION', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('accion', TextType::class, array('label' => 'ACCIONES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('gravedad_t', ChoiceType::class, array('label' => 'GRAVEDAD TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('frecuencia_t', ChoiceType::class, array('label' => 'FRECUENCIA TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))
            ->add('deteccion_t', ChoiceType::class, array('label' => 'DETECCION TRAS ACCIONES', 'choices' => array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px'), 'required' => false))


            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $modo_fallo = $form['modo_fallo']->getData();
            $gravedad = $form['gravedad']->getData();
            $frecuencia = $form['frecuencia']->getData();
            $deteccion = $form['deteccion']->getData();
            $accion = $form['accion']->getData();
            $gravedad_t = $form['gravedad_t']->getData();
            $frecuencia_t = $form['frecuencia_t']->getData();
            $deteccion_t = $form['deteccion_t']->getData();

            $amfe = $this->getDoctrine()
                ->getRepository('GestionBundle:amfe')
                ->find($id_amfe);

            $npr=$gravedad*$frecuencia*$deteccion;
            
            if ( ($gravedad_t == "") || ($frecuencia_t == "") || ($deteccion_t == "") ) {
                
                $detalleamfe->setModoFallo($modo_fallo);
                $detalleamfe->setGravedad($gravedad);
                $detalleamfe->setFrecuencia($frecuencia);
                $detalleamfe->setDeteccion($deteccion);
                $detalleamfe->setNpr($npr);
                $detalleamfe->setAccion($accion);
                $detalleamfe->setIdAmfe($amfe);
            
                $em = $this->getDoctrine()->getManager();

                $em->persist($detalleamfe);
                $em->flush();

            } else {

                $nuevo_npr=$gravedad_t*$frecuencia_t*$deteccion_t;

                $detalleamfe->setModoFallo($modo_fallo);
                $detalleamfe->setGravedad($gravedad);
                $detalleamfe->setFrecuencia($frecuencia);
                $detalleamfe->setDeteccion($deteccion);
                $detalleamfe->setNpr($npr);
                $detalleamfe->setAccion($accion);                
                $detalleamfe->setGravedadT($gravedad_t);
                $detalleamfe->setFrecuenciaT($frecuencia_t);
                $detalleamfe->setDeteccionT($deteccion_t);
                $detalleamfe->setNuevoNpr($nuevo_npr);     
                $detalleamfe->setIdAmfe($amfe);
                
                $em = $this->getDoctrine()->getManager();

                $em->persist($detalleamfe);
                $em->flush();
            }

            $response = $this->forward('GestionBundle:amfe:editAmfepart', [
                'id' => $id_amfe,
                'tarea' => $id_tarea,
                'area' => $area,
                'modelo' => $modelo,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'listado' => $listado
            ]);

            return $response;
        }

        return $this->render('GestionBundle:gestion/amfe:create_detalle_amfepart.html.twig', array(
            'form' => $form->createView(), 
            'amfe' => $id, 
            'idDetalleamfe' => $id_detalleamfe,
            'tarea' => $id_tarea,
            'area' => $area,
            'modelo' => $modelo,
            'submodelo' => $submodelo,
            'linea' => $linea,
            'proceso' => $proceso,
            'listado' => $listado
        ));
    }

    public function deleteDetalleAmfepartAction(Request $request) {

        $id_detalleamfe=$_POST['idDetalleamfe'];

        $em = $this->getDoctrine()->getManager();

        $detalleamfe = $em->getRepository('GestionBundle:detalleamfe')->find($id_detalleamfe);

        $em->remove($detalleamfe);
        $em->flush();

        $id_amfe = $detalleamfe->getIdAmfe();

        $response = $this->forward('GestionBundle:amfe:editAmfepart', [
                'id' => $id_amfe
            ]);

        return $response;
    }


    public function editNombreAction(Request $request) {

        $id=$_POST['idAmfe'];

        $amfe = $this->getDoctrine()
            ->getRepository('GestionBundle:amfe')
            ->find($id);

        $form = $this->createFormBuilder($amfe)

            ->add('nombre', TextType::class, array('label' => 'nombre', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $nombre = $form['nombre']->getData();

            $em = $this->getDoctrine()->getManager();
            $amfe = $em->getRepository('GestionBundle:amfe')->find($id);

            $amfe->setNombre($nombre);
            
            $em->flush();

            return $this->redirectToRoute('app_index_amfe');
        }

        return $this->render('GestionBundle:gestion/amfe:edit_nombre_amfe.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }


    public function indexSegamfeAction() {

        return $this->render('GestionBundle:gestion/amfe:index_segamfe.html.twig', array(
                'navsegamfe' => 1
            ));
    }

    public function filterSegamfeAction(Request $request) {

        $id_submodelo=$_POST['submodelo'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $submodelo = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->find($id_submodelo);

        $query = "SELECT submodelo.nombre AS 'submodelo', tarea.nombre_es AS 'tarea', amfe.nombre AS 'amfe', detalleamfe.modo_fallo AS 'modo_fallo', detalleamfe.accion AS 'accion', detalleamfe.id AS 'id_detalleamfe', submodelo_detalleamfe.responsable AS responsable, submodelo_detalleamfe.fecha_estimada AS 'fecha_estimada', submodelo_detalleamfe.realizada AS 'realizada', submodelo_detalleamfe.fecha_realizado AS fecha_realizado
            FROM asignarproceso
            INNER JOIN submodelo ON submodelo.id = asignarproceso.id_submodelo
            INNER JOIN asignarprocesore ON asignarprocesore.id_asignarproceso = asignarproceso.id
            INNER JOIN asignarprocesoversion ON asignarprocesoversion.id = asignarprocesore.id_asignarprocesoversion
            INNER JOIN tarea_asignada ON tarea_asignada.id_asignarprocesoversion = asignarprocesoversion.id
            INNER JOIN tarea ON tarea.id = tarea_asignada.id_tarea
            INNER JOIN tarea_amfe ON tarea_amfe.id_tareaAsignada = tarea_asignada.id
            INNER JOIN amfe ON amfe.id = tarea_amfe.id_amfe
            INNER JOIN detalleamfe ON detalleamfe.id_amfe = amfe.id
            LEFT JOIN submodelo_detalleamfe ON submodelo_detalleamfe.id_submodelo = asignarproceso.id_submodelo AND submodelo_detalleamfe.id_detalleamfe = detalleamfe.id
            WHERE asignarproceso.id_submodelo = $id_submodelo AND detalleamfe.accion IS NOT NULL";
        
        $statement=$db->prepare($query);
        $statement->execute();
        $datos=$statement->fetchAll(); 

        return $this->render('GestionBundle:gestion/amfe:ajax_segamfe.html.twig', array(
            'submodelo' => $submodelo,
            'datos' => $datos
        ));
    }


    public function editSegamfeAction(Request $request) {

        $id_submodelo=$_POST['submodelo'];
        $value_responsable=$_POST['value_responsable'];
        $value_fecha_estimada=$_POST['value_fecha_estimada'];
        $value_fecha_realizada=$_POST['value_fecha_realizada'];
        $value_realizada=$_POST['value_realizada'];
        $value_detalleamfe=$_POST['detalle_amfe'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        //inserto en la bbdd el responsable y fecha
        $query = "SELECT * FROM submodelo_detalleamfe WHERE id_submodelo = $id_submodelo AND id_detalleamfe = $value_detalleamfe";
        $statement=$db->prepare($query);
        $statement->execute();
        $submodelo_detalleamfe_find=$statement->fetchAll();

        if (empty($submodelo_detalleamfe_find)) {

            $query2 = "INSERT INTO submodelo_detalleamfe (id_submodelo, id_detalleamfe, responsable, fecha_estimada, realizada, fecha_realizado) VALUES ($id_submodelo, $value_detalleamfe, '$value_responsable', '$value_fecha_estimada', '$value_realizada', '$value_fecha_realizada')";
            $statement2=$db->prepare($query2);
            $statement2->execute();
        
        } else {

            $query3 = "UPDATE submodelo_detalleamfe SET responsable = '$value_responsable', fecha_estimada = '$value_fecha_estimada', realizada = '$value_realizada', fecha_realizado = '$value_fecha_realizada' WHERE id_submodelo = $id_submodelo AND id_detalleamfe = $value_detalleamfe";
            $statement3=$db->prepare($query3);
            $statement3->execute();


        }

        $submodelo = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->find($id_submodelo);

        $query = "SELECT submodelo.nombre AS 'submodelo', tarea.nombre_es AS 'tarea', amfe.nombre AS 'amfe', detalleamfe.modo_fallo AS 'modo_fallo', detalleamfe.accion AS 'accion', detalleamfe.id AS 'id_detalleamfe', submodelo_detalleamfe.responsable AS responsable, submodelo_detalleamfe.fecha_estimada AS 'fecha_estimada', submodelo_detalleamfe.realizada AS 'realizada', submodelo_detalleamfe.fecha_realizado AS fecha_realizado
            FROM asignarproceso
            INNER JOIN submodelo ON submodelo.id = asignarproceso.id_submodelo
            INNER JOIN asignarprocesore ON asignarprocesore.id_asignarproceso = asignarproceso.id
            INNER JOIN asignarprocesoversion ON asignarprocesoversion.id = asignarprocesore.id_asignarprocesoversion
            INNER JOIN tarea_asignada ON tarea_asignada.id_asignarprocesoversion = asignarprocesoversion.id
            INNER JOIN tarea ON tarea.id = tarea_asignada.id_tarea
            INNER JOIN tarea_amfe ON tarea_amfe.id_tareaAsignada = tarea_asignada.id
            INNER JOIN amfe ON amfe.id = tarea_amfe.id_amfe
            INNER JOIN detalleamfe ON detalleamfe.id_amfe = amfe.id
            LEFT JOIN submodelo_detalleamfe ON submodelo_detalleamfe.id_submodelo = asignarproceso.id_submodelo AND submodelo_detalleamfe.id_detalleamfe = detalleamfe.id
            WHERE submodelo.id = $id_submodelo AND detalleamfe.accion IS NOT NULL";
        
        $statement=$db->prepare($query);
        $statement->execute();
        $datos=$statement->fetchAll();

        return $this->render('GestionBundle:gestion/amfe:ajax_segamfe.html.twig', array(
            'submodelo' => $submodelo,
            'datos' => $datos 
        ));
    }

}