<?php

namespace GestionBundle\Controller;


use GestionBundle\Entity\planta;
use GestionBundle\Entity\submodelo;
use GestionBundle\Entity\linea;
use GestionBundle\Entity\proceso;
use GestionBundle\Entity\asignarproceso;

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

class AsignarprocesoController extends Controller
{
   
    public function indexAction() {


        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');

        $lineas = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->findBy(
             array('active'=> 1), 
             array('nombre' => 'ASC')
           );

        $procesos = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findBy(
             array('active'=> 1), 
             array('nombre' => 'ASC')
           );

        return $this->render('GestionBundle:gestion/asignarproceso:index_asignarproceso.html.twig', array(
            'plantas' => $plantas,
            'lineas' => $lineas,
            'procesos' =>$procesos,
            'navasignarproceso' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/asignarproceso:index_asignarproceso.html.twig');
    }


    public function getSubmodelos(Request $request)
    {
        $planta = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();


        $query = "SELECT * FROM submodelo WHERE id_planta = $planta AND active = 1 ORDER BY nombre ASC";
        $statement=$db->prepare($query);
        $statement->execute();
        $submodelos=$statement->fetchAll();

        return $response = new JsonResponse(['data' => $submodelos]);
    }


    public function asignadosAction() {

        $id=$_POST['planta_id'];
        $planta = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->find($id);

        $id2=$_POST['submodelo_id'];
        $submodelo = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->find($id2);

        $lineas = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->findByactive('1');

        $procesos = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findByactive('1');

        $repository = $this->getDoctrine()->getRepository(asignarproceso::class);

        $asignarprocesos = $repository->findBy(
            ['idSubmodelo' => $id2, 'active' => '1'],
            ['id' => 'DESC']
        );

        return $this->render('GestionBundle:gestion/asignarproceso:filterplanta_asignarproceso.html.twig', array(
                'planta' => $planta,
                'submodelo' => $submodelo,
                'lineas' => $lineas,
                'procesos' => $procesos,
                'asignarprocesos' => $asignarprocesos
            ));

        }

    public function createAction(Request $request) {

            $id = $_POST['planta'];
            $planta = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->find($id);

            $id_submodelo = $_POST['submodelo'];

            $submodelo2 = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->find($id_submodelo);

            $id_linea = $_POST['linea'];
            $id_proceso = $_POST['proceso'];

            $entityManager = $this->getDoctrine()->getManager();

            $asignarproceso = new asignarproceso();

            $submodelo = $entityManager->getRepository('GestionBundle:submodelo')->find($id_submodelo);
            $asignarproceso->setIdSubmodelo($submodelo);

            $linea = $entityManager->getRepository('GestionBundle:linea')->find($id_linea);
            $asignarproceso->setIdLinea($linea);

            $proceso = $entityManager->getRepository('GestionBundle:proceso')->find($id_proceso);
            $asignarproceso->setIdProceso($proceso);
            
            $entityManager->persist($asignarproceso);

            $entityManager->flush();

            $lineas = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->findByactive('1');

            $procesos = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findByactive('1');

            $repository = $this->getDoctrine()->getRepository(asignarproceso::class);

            $asignarprocesos = $repository->findBy(
            ['idSubmodelo' => $id_submodelo, 'active' => '1'],
            ['id' => 'DESC']
            );

            return $this->render('GestionBundle:gestion/asignarproceso:filterplanta_asignarproceso.html.twig', array(
                'planta' => $planta,
                'submodelo' => $submodelo2,
                'lineas' => $lineas,
                'procesos' => $procesos,
                'asignarprocesos' => $asignarprocesos
            ));

    }

    public function deleteAction(Request $request) {

        $id=$_POST['idAsignarproceso'];

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $asignarproceso = $em->getRepository('GestionBundle:asignarproceso')->find($id);

        $asignarproceso->setActive('0');
        $em->flush();


        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');

        $lineas = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->findByactive('1');

        $procesos = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findByactive('1');

        return $this->render('GestionBundle:gestion/asignarproceso:index_asignarproceso.html.twig', array(
            'plantas' => $plantas,
            'lineas' => $lineas,
            'procesos' =>$procesos
        ));
    }


}