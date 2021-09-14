<?php

namespace GestionBundle\Controller;


use GestionBundle\Entity\planta;
use GestionBundle\Entity\submodelo;
use GestionBundle\Entity\linea;
use GestionBundle\Entity\proceso;
use GestionBundle\Entity\tarea;
use GestionBundle\Entity\tareaAsignada;
use GestionBundle\Entity\asignarproceso;
use GestionBundle\Entity\opbasica;
use GestionBundle\Entity\operacionAsignada;

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
use Symfony\Component\Form\Extension\Core\Type\TimeType;

use Doctrine\ORM\EntityRepository;

class OperacionAsignadaController extends Controller
{
   
    public function indexAction(Request $request) {

    	$id=$_POST['idTareaAsignada'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];
        $id_asignarproceso=$_POST['asignarproceso'];
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

    	$opbasignadas = $this->getDoctrine()
            ->getRepository('GestionBundle:operacionAsignada')
            ->findByIdTareaAsignada($id, array('position' => 'ASC'));


        $repository = $this->getDoctrine()
            ->getRepository(opbasica::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.tipo = :tipo')
            ->andWhere('p.idProceso = :idProceso')
            ->setParameter('tipo', 'OPB')
            ->setParameter('idProceso', $id_proceso)
            ->getQuery();

        $opbasicas = $query->getResult();

        $asignarproceso = $this->getDoctrine()
            ->getRepository('GestionBundle:asignarproceso')
            ->findOneById($id_asignarproceso);

        $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

        $tareaasignada = $this->getDoctrine()
            ->getRepository('GestionBundle:tareaAsignada')
            ->findOneById($id);

        $id_tarea = $tareaasignada->getIdTarea();

        $tarea = $this->getDoctrine()
            ->getRepository('GestionBundle:tarea')
            ->findOneById($id_tarea);


        return $this->render('GestionBundle:gestion/operacionasignada:index_operacionasignada.html.twig', array(
            'idTareaAsignada' =>$id,
            'opbasignadas' =>$opbasignadas,
            'opbasicas' =>$opbasicas,
            'tarea' => $tarea,
            'planta' => $id_planta,
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso,
            'asignarproceso' =>$asignarproceso,
            'asignarprocesoversion' =>$asignarprocesoversion,
            'idAsignarprocesoversion' => $id_asignarprocesoversion,
            //'desp_necesarios' => $desp_necesarios,
            'permiso' => $permiso
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/operacionasignada:index_operacionasignada.html.twig');
    }

    public function updateAction(Request $request) {

        $id=$_POST['idTareaAsignada'];
    	$proceso=$_POST['proceso'];
        $planta=$_POST['planta'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $id_asignarproceso=$_POST['asignarproceso'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $permiso = 0;

        if (in_array('ROLE_INGENIERIA', $this->getUser()->getRoles(), true)) {
            
            $permiso = 1;

            if (isset($_POST['update'])) {
            foreach($_POST['positions'] as $position) {
               $index = $position[0];
               $newPosition = $position[1];

               $em = $this->getDoctrine()->getManager();
               $db = $em->getConnection();

               $query = "UPDATE operacion_asignada SET position = '$newPosition' WHERE id='$index'";
               $statement=$db->prepare($query);
               $statement->execute();
            }
        }

            $em = $this->getDoctrine()->getManager();
            $db = $em->getConnection();

            $opbasignadas = $this->getDoctrine()
                ->getRepository('GestionBundle:operacionAsignada')
                ->findByIdTareaAsignada($id, array('position' => 'ASC'));

            $opbasicas = $this->getDoctrine()
                ->getRepository('GestionBundle:opbasica')
                ->findAll();

            $asignarproceso = $this->getDoctrine()
                ->getRepository('GestionBundle:asignarproceso')
                ->findOneById($id_asignarproceso);

            $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

            $tareaasignada = $this->getDoctrine()
                ->getRepository('GestionBundle:tareaAsignada')
                ->findOneById($id);

            $id_tarea = $tareaasignada->getIdTarea();

            $tarea = $this->getDoctrine()
                ->getRepository('GestionBundle:tarea')
                ->findOneById($id_tarea);

        } //endif permiso


        return $this->render('GestionBundle:gestion/operacionasignada:ajax_operacionasignada.html.twig', array(
                'idTareaAsignada' =>$id,
                'opbasignadas' =>$opbasignadas,
                'opbasicas' =>$opbasicas,
                'tarea' => $tarea,
                'planta' => $planta,
                'submodelo' => $submodelo,
                'linea' => $linea,
                'proceso' => $proceso,
                'asignarproceso' =>$asignarproceso,
                'asignarprocesoversion' =>$asignarprocesoversion,
                'idAsignarprocesoversion' => $id_asignarprocesoversion,
                'permiso' => $permiso
            ));

    }

    public function newOPBAsignadaAction(Request $request)
    {

        $id=$_POST['idTareaAsignada'];
    	$proceso=$_POST['proceso'];
    	$opbasica=$_POST['opbasica'];
    	$reps=$_POST['reps_opb'];
    	$comentario=$_POST['comentario_oa'];
        $asignarproceso=$_POST['asignarproceso'];
        $planta=$_POST['planta'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $tareaasignada = $this->getDoctrine()
            ->getRepository('GestionBundle:tareaAsignada')
            ->find($id);

        $lote = $tareaasignada->getLote();

        if(is_null($lote)) {
            $lote = 1;
        }

        $operacionbasica = $this->getDoctrine()
            ->getRepository('GestionBundle:opbasica')
            ->find($opbasica);

        $tiempo = $operacionbasica->getTiempo();
        $tiempototal = (($tiempo * $reps) / $lote);

        $query = "SELECT COUNT(*) as numeroopbs FROM operacion_asignada WHERE id_tareaAsignada = $id";
        $statement=$db->prepare($query);
        $statement->execute();
        $totalopbs=$statement->fetchAll();    

        $operacionasignada = new operacionAsignada();

        $operacionasignada->setIdTareaAsignada($tareaasignada);   
        $operacionasignada->setIdOperacionbasica($operacionbasica);
        $operacionasignada->setRepeticion($reps);
        $operacionasignada->setComentario($comentario);
        $operacionasignada->setTiempo($tiempototal);
        $operacionasignada->setPosition(($totalopbs[0]['numeroopbs']+1));

        $em->persist($operacionasignada);
        $em->flush();

        $response = $this->forward('GestionBundle:OperacionAsignada:index', [
            'idTareaAsignada' =>$id,
            'proceso' =>$proceso,
            'asignarproceso' =>$asignarproceso,
            'planta' =>$planta,
            'submodelo' =>$submodelo,
            'linea' =>$linea,
            'idAsignarprocesoversion' => $id_asignarprocesoversion
        ]);

        return $response;

    }

    public function newDESAction(Request $request)
    {

        date_default_timezone_set('Europe/Madrid');

        $id=$_POST['idTareaAsignada'];
        $proceso=$_POST['proceso'];

        $nombreES=$_POST['nombreES'];
        $nombreEN=$_POST['nombreEN'];
        $min=$_POST['min'];
        $sec=$_POST['sec'];

        $asignarproceso=$_POST['asignarproceso'];
        $planta=$_POST['planta'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $em = $this->getDoctrine()->getManager();

        $proceso = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findById($proceso);

        $opbasica = new opbasica;

        $opbasica->setIdProceso($proceso[0]);
        $opbasica->setNombreES($nombreES);
        $opbasica->setNombreEN($nombreEN);
        $opbasica->setDescripcionES($nombreES);
            
        $tiempo = ($min * 60) + $sec;
        $opbasica->setTiempo($tiempo);
        $opbasica->setTipo('DES');

        $em->persist($opbasica);    
        $em->flush();

        $id_opbasica = $opbasica->getId();

        $response = $this->forward('GestionBundle:OperacionAsignada:newDESAsignada', [
            'id_opbasica'  => $id_opbasica,
            'idTareaAsignada' =>$id,
            'proceso' =>$proceso,
            'asignarproceso' =>$asignarproceso,
            'planta' =>$planta,
            'submodelo' =>$submodelo,
            'linea' =>$linea,
            'comentario' =>$nombreES,
            'idAsignarprocesoversion' => $id_asignarprocesoversion

        ]);

        return $response;

    }  

    public function newDESAsignadaAction(Request $request, $id_opbasica, $comentario)
    {

        $id=$_POST['idTareaAsignada'];
        $proceso=$_POST['proceso'];
        $asignarproceso=$_POST['asignarproceso'];
        $planta=$_POST['planta'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $tareaasignada = $this->getDoctrine()
            ->getRepository('GestionBundle:tareaAsignada')
            ->find($id);

        $operacionbasica = $this->getDoctrine()
            ->getRepository('GestionBundle:opbasica')
            ->find($id_opbasica);

        $tiempo = $operacionbasica->getTiempo();

        $query = "SELECT COUNT(*) as numerodeps FROM operacion_asignada WHERE id_tareaAsignada = $id";
        $statement=$db->prepare($query);
        $statement->execute();
        $totaldesps=$statement->fetchAll();

        $operacionasignada = new operacionAsignada();

        $operacionasignada->setIdTareaAsignada($tareaasignada);
        $operacionasignada->setIdOperacionbasica($operacionbasica);
        $operacionasignada->setRepeticion(1);
        $operacionasignada->setComentario($comentario);
        $operacionasignada->setTiempo($tiempo);
        $operacionasignada->setPosition(($totaldesps[0]['numerodeps']+1));

        $em->persist($operacionasignada);
        $em->flush();

        

        $response = $this->forward('GestionBundle:OperacionAsignada:index', [
            'idTareaAsignada' =>$id,
            'proceso' =>$proceso,
            'asignarproceso' =>$asignarproceso,
            'planta' =>$planta,
            'submodelo' =>$submodelo,
            'linea' =>$linea,
            'idAsignarprocesoversion' => $id_asignarprocesoversion
        ]);

        return $response;

    }

    public function editAction(Request $request) {

        $id=$_POST['idTareaAsignada'];
        $proceso=$_POST['proceso'];
        $asignarproceso=$_POST['asignarproceso'];
        $planta=$_POST['planta'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $id_opb=$_POST['idOPBAsignada'];

        $operacionasignada = $this->getDoctrine()
            ->getRepository('GestionBundle:operacionAsignada')
            ->find($id_opb);

        $id_opbasica = $operacionasignada->getIdOperacionbasica();

        $opbasica = $this->getDoctrine()
            ->getRepository('GestionBundle:opbasica')
            ->find($id_opbasica);

        $tipo = $opbasica->getTipo();

        $tareaasignada = $this->getDoctrine()
            ->getRepository('GestionBundle:tareaAsignada')
            ->find($id);

        $lote = $tareaasignada->getLote();

        if(is_null($lote)) {
            $lote = 1;
        }

        if ($tipo == "OPB" || $tipo == "DNEC") {
            

            $form = $this->createFormBuilder($operacionasignada)
                ->add('id_operacionbasica', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                    'class' => 'GestionBundle:opbasica',
                    'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.active = 1');
                },
                    'choice_label' => 'nombreES', 'label' => 'opbasica', 'attr' => array('class' => 'form-control'
                    , 'style' => 'margin-bottom:15px')
                ])
                ->add('repeticion', ChoiceType::class, array('label' => 'REPS', 'choices' => range(0,999,1), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('comentario', TextType::class, array('label' => 'comentario', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
                ->getForm();

            $form->handleRequest($request);


            if($form->isSubmitted() && $form->isValid()) {

                $id_operacionbasica = $form['id_operacionbasica']->getData();
                $repeticion = $form['repeticion']->getData();
                $comentario = $form['comentario']->getData();
            

                $em = $this->getDoctrine()->getManager();

                $operacionasignada = $em->getRepository('GestionBundle:operacionAsignada')->find($id_opb);

                $operacionbasica = $this->getDoctrine()
                ->getRepository('GestionBundle:opbasica')
                ->find($id_operacionbasica);

                $tiempo = $operacionbasica->getTiempo();
                $tiempototal = (($tiempo * $repeticion) / $lote);

                $operacionasignada->setIdOperacionbasica($id_operacionbasica);
                $operacionasignada->setRepeticion($repeticion);
                $operacionasignada->setComentario($comentario);
                $operacionasignada->setTiempo($tiempototal);

                $em->persist($operacionasignada);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'exito'
                );

                $response = $this->forward('GestionBundle:OperacionAsignada:index', [
                'idTareaAsignada' =>$id,
                'proceso' =>$proceso,
                'asignarproceso' =>$asignarproceso,
                'planta' =>$planta,
                'submodelo' =>$submodelo,
                'linea' =>$linea,
                'idAsignarprocesoversion' => $id_asignarprocesoversion
                ]);

                return $response;
            }

        } else {

            $a=$operacionasignada->getTiempo()-3600;
            $operacionasignada->setTiempo($a);

            $form = $this->createFormBuilder($operacionasignada)
                ->add('id_operacionbasica', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                    'class' => 'GestionBundle:opbasica',
                    'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->where('u.active = 1');
                },
                    'choice_label' => 'nombreES', 'label' => 'DES', 'attr' => array('class' => 'form-control'
                    , 'style' => 'margin-bottom:15px')
                ])
                ->add('tiempo', TimeType::class, [
                    'label' => 'tiempo',
                    'required' => false,
                    'widget' => 'choice',
                    'html5' => true,
                    'input' => 'timestamp',
                    'with_seconds' => true,
                    'placeholder' => ['hour' => 'hh', 'minute' => 'mm', 'second' => 'ss',]])
                ->add('comentario', TextType::class, array('label' => 'comentario', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
                ->getForm();

            $form->handleRequest($request);


            if($form->isSubmitted() && $form->isValid()) {

                $id_operacionbasica = $form['id_operacionbasica']->getData();
                $tiempo = $form['tiempo']->getData();
                $comentario = $form['comentario']->getData();

                $tiempo=$tiempo+3600;

                $em = $this->getDoctrine()->getManager();

                $operacionasignada = $em->getRepository('GestionBundle:operacionAsignada')->find($id_opb);

                $operacionbasica = $this->getDoctrine()
                ->getRepository('GestionBundle:opbasica')
                ->find($id_operacionbasica);

                $operacionasignada->setIdOperacionbasica($id_operacionbasica);
                $operacionasignada->setComentario($comentario);
                $operacionasignada->setTiempo($tiempo);

                $em->persist($operacionasignada);
                $em->flush();

                $operacionbasica->setTiempo($tiempo);
                $em->persist($operacionbasica);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'exito'
                );

                $response = $this->forward('GestionBundle:OperacionAsignada:index', [
                'idTareaAsignada' =>$id,
                'proceso' =>$proceso,
                'asignarproceso' =>$asignarproceso,
                'planta' =>$planta,
                'submodelo' =>$submodelo,
                'linea' =>$linea,
                'idAsignarprocesoversion' => $id_asignarprocesoversion
                ]);

                return $response;
            }
        }

        return $this->render('GestionBundle:gestion/operacionasignada:edit_operacionasignada.html.twig', array(
            'form' => $form->createView(), 'id'=> $id_opb, 'idTareaAsignada' =>$id, 'proceso' =>$proceso, 'asignarproceso' =>$asignarproceso, 'planta' =>$planta, 'submodelo' =>$submodelo,'linea' =>$linea, 'idAsignarprocesoversion' =>$id_asignarprocesoversion
        ));
    }



    public function deleteAction(Request $request) {

        $id=$_POST['idTareaAsignada'];
        $proceso=$_POST['proceso'];
        $asignarproceso=$_POST['asignarproceso'];
        $planta=$_POST['planta'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];
        $opbasignada=$_POST['idOPBAsignada'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $operacionasignada = $em->getRepository('GestionBundle:operacionAsignada')->find($opbasignada);
        $posicion=$operacionasignada->getPosition();


        $query = "SELECT COUNT(*) as totaloperaciones FROM operacion_asignada WHERE id_tareaAsignada = $id";
        $statement=$db->prepare($query);
        $statement->execute();
        $total_operaciones=$statement->fetchAll();

        for ($i=($posicion+1); $i <= ($total_operaciones[0]['totaloperaciones']); $i++) { 

            $query2 = "UPDATE operacion_asignada SET position = ($i-1) WHERE id_tareaAsignada = $id AND position = $i";
            $statement2=$db->prepare($query2);
            $statement2->execute();
        }

        $em->remove($operacionasignada);
        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        
        $response = $this->forward('GestionBundle:OperacionAsignada:index', [
            'idTareaAsignada' =>$id,
            'proceso' =>$proceso,
            'asignarproceso' =>$asignarproceso,
            'planta' =>$planta,
            'submodelo' =>$submodelo,
            'linea' =>$linea
        ]);

        return $response;

    }

    public function ajaxAction(Request $request) {

        $id=$_POST['idTareaAsignada'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];
        $id_asignarproceso=$_POST['asignarproceso'];
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

        $opbasignadas = $this->getDoctrine()
            ->getRepository('GestionBundle:operacionAsignada')
            ->findByIdTareaAsignada($id, array('position' => 'ASC'));

        $opbasicas = $this->getDoctrine()
            ->getRepository('GestionBundle:opbasica')
            ->findAll();

        $asignarproceso = $this->getDoctrine()
            ->getRepository('GestionBundle:asignarproceso')
            ->findOneById($id_asignarproceso);

        $asignarprocesoversion = $em->getRepository('GestionBundle:asignarprocesoversion')->find($id_asignarprocesoversion);

        $tareaasignada = $this->getDoctrine()
            ->getRepository('GestionBundle:tareaAsignada')
            ->findOneById($id);

        $id_tarea = $tareaasignada->getIdTarea();

        $tarea = $this->getDoctrine()
            ->getRepository('GestionBundle:tarea')
            ->findOneById($id_tarea);

        //ACTUALIZAR TIEMPO EN TAREA_ASIGNADA
        $query = "SELECT SUM(tiempo) AS SUMA FROM operacion_asignada WHERE id_tareaAsignada = $id";
        $statement=$db->prepare($query);
        $statement->execute();
        $suma_tiempo=$statement->fetchAll();
        
        $suma_tiempos=($suma_tiempo[0]['SUMA']);
        $tareaasignada->setTiempo($suma_tiempos);

        $em->persist($tareaasignada);
        $em->flush();
        //FIN ACTUALIZAR TIEMPO

        return $this->render('GestionBundle:gestion/operacionasignada:ajax_operacionasignada.html.twig', array(
            'idTareaAsignada' =>$id,
            'opbasignadas' =>$opbasignadas,
            'opbasicas' =>$opbasicas,
            'tarea' => $tarea,
            'planta' => $id_planta,
            'submodelo' => $id_submodelo,
            'linea' => $id_linea,
            'proceso' => $id_proceso,
            'asignarproceso' =>$asignarproceso,
            'asignarprocesoversion' =>$asignarprocesoversion,
            'idAsignarprocesoversion' => $id_asignarprocesoversion,
            'permiso' => $permiso
        ));
    }

    public function newDNECAction(Request $request)
    {

        $id=$_POST['idTareaAsignada'];
        $proceso=$_POST['proceso'];
        $id_opbasica=$_POST['opbasica'];
        $reps=$_POST['reps_opb'];
        $comentario=$_POST['comentario_oa'];
        $asignarproceso=$_POST['asignarproceso'];
        $planta=$_POST['planta'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $operacionbasica = $this->getDoctrine()
            ->getRepository('GestionBundle:opbasica')
            ->find($id_opbasica);

        $id_proceso = $operacionbasica->getIdProceso();
        $nombreES = $operacionbasica->getNombreES();
        $nombreEN = $operacionbasica->getNombreEN();
        $descripcionES = $operacionbasica->getDescripcionES();
        $descripcionEN = $operacionbasica->getDescripcionEN();
        $tiempo = $operacionbasica->getTiempo();
        $comentarioES = $operacionbasica->getComentarioES();
        $comentarioEN = $operacionbasica->getComentarioEN();

        $opbasica = new opbasica;

        $opbasica->setIdProceso($id_proceso);
        $opbasica->setNombreES($nombreES);
        $opbasica->setNombreEN($nombreEN);
        $opbasica->setDescripcionES($descripcionES);
        $opbasica->setDescripcionEN($descripcionEN);
        $opbasica->setTiempo($tiempo);
        $opbasica->setComentarioES($comentarioES);
        $opbasica->setComentarioEN($comentarioEN);
        $opbasica->setTipo('DNEC');

        $em->persist($opbasica);    
        $em->flush();


        $tareaasignada = $this->getDoctrine()
            ->getRepository('GestionBundle:tareaAsignada')
            ->find($id);

        $lote = $tareaasignada->getLote();

        if(is_null($lote)) {
            $lote = 1;
        }

        $tiempototal = (($tiempo * $reps) / $lote);

        $query = "SELECT COUNT(*) as numeroopbs FROM operacion_asignada WHERE id_tareaAsignada = $id";
        $statement=$db->prepare($query);
        $statement->execute();
        $totalopbs=$statement->fetchAll();    

        $operacionasignada = new operacionAsignada();

        $operacionasignada->setIdTareaAsignada($tareaasignada);   
        $operacionasignada->setIdOperacionbasica($opbasica);
        $operacionasignada->setRepeticion($reps);
        $operacionasignada->setComentario($comentario);
        $operacionasignada->setTiempo($tiempototal);
        $operacionasignada->setPosition(($totalopbs[0]['numeroopbs']+1));

        $em->persist($operacionasignada);
        $em->flush();

        $response = $this->forward('GestionBundle:OperacionAsignada:index', [
            'idTareaAsignada' =>$id,
            'proceso' =>$proceso,
            'asignarproceso' =>$asignarproceso,
            'planta' =>$planta,
            'submodelo' =>$submodelo,
            'linea' =>$linea,
            'idAsignarprocesoversion' => $id_asignarprocesoversion
        ]);

        return $response;

    }

    public function newTPROGAction(Request $request)
    {

        $id=$_POST['idTareaAsignada'];
        $proceso=$_POST['proceso'];

        $nombreES=$_POST['nombreES'];
        $nombreEN=$_POST['nombreEN'];
        $min=$_POST['min'];
        $sec=$_POST['sec'];

        $asignarproceso=$_POST['asignarproceso'];
        $planta=$_POST['planta'];
        $submodelo=$_POST['submodelo'];
        $linea=$_POST['linea'];
        $id_asignarprocesoversion=$_POST['idAsignarprocesoversion'];

        $em = $this->getDoctrine()->getManager();

        $proceso = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findById($proceso);

        $opbasica = new opbasica;


        $opbasica->setIdProceso($proceso[0]);
        $opbasica->setNombreES($nombreES);
        $opbasica->setNombreEN($nombreEN);
        $opbasica->setDescripcionES($nombreES);
            
        $tiempo = ($min * 60) + $sec;
        $opbasica->setTiempo($tiempo);
        $opbasica->setTipo('TPROG');

        $em->persist($opbasica);    
        $em->flush();

        $id_opbasica = $opbasica->getId();

        $response = $this->forward('GestionBundle:OperacionAsignada:newDESAsignada', [
            'id_opbasica'  => $id_opbasica,
            'idTareaAsignada' =>$id,
            'proceso' =>$proceso,
            'asignarproceso' =>$asignarproceso,
            'planta' =>$planta,
            'submodelo' =>$submodelo,
            'linea' =>$linea,
            'comentario' =>$nombreES,
            'idAsignarprocesoversion' => $id_asignarprocesoversion

        ]);

        return $response;

    }  

}