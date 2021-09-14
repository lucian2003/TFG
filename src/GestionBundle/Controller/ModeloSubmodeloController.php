<?php

namespace GestionBundle\Controller;


use GestionBundle\Entity\modelo;
use GestionBundle\Entity\submodelo;
use GestionBundle\Entity\modelosubmodelo;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityRepository;

class ModeloSubmodeloController extends Controller
{
   
    public function indexAction() {

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $modelos = $this->getDoctrine()
            ->getRepository('GestionBundle:modelo')
            ->findBy(
             array('active'=> 1), 
             array('nombre' => 'ASC')
           );

        $modelosubmodelos = $this->getDoctrine()
            ->getRepository('GestionBundle:modelosubmodelo')
            ->findAll();

        return $this->render('GestionBundle:gestion/modelosubmodelo:index_modelosubmodelo.html.twig', array(
            'modelos' => $modelos,
            'modelosubmodelos' => $modelosubmodelos,
            'navmodelosubmodelo' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/modelosubmodelo:index_modelosubmodelo.html.twig');
    }


    public function getModSubmodelos(Request $request)
    {
        $modelo = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM submodelo WHERE id NOT IN (SELECT id_submodelo FROM modelosubmodelo WHERE id_modelo = $modelo) ORDER BY nombre ASC";
        $statement=$db->prepare($query);
        $statement->execute();
        $submodelos=$statement->fetchAll();


        return $response = new JsonResponse(['data' => $submodelos]);

    }

    public function createAction(Request $request) {

            $id_modelo = $_POST['modelo'];
            $modelo = $this->getDoctrine()
            ->getRepository('GestionBundle:modelo')
            ->find($id_modelo);

            $id_submodelo = $_POST['submodelo'];
            $submodelo = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->find($id_submodelo);

            $cantidad = $_POST['cantidad'];

            $entityManager = $this->getDoctrine()->getManager();

            $modelosubmodelo = new modelosubmodelo();

            $modelo = $entityManager->getRepository('GestionBundle:modelo')->find($id_modelo);
            $modelosubmodelo->setIdModelo($modelo);

            $submodelo = $entityManager->getRepository('GestionBundle:submodelo')->find($id_submodelo);
            $modelosubmodelo->setIdSubmodelo($submodelo);

            $modelosubmodelo->setCantidad($cantidad);

            $entityManager->persist($modelosubmodelo);

            $entityManager->flush();

            $modelos = $this->getDoctrine()
            ->getRepository('GestionBundle:modelo')
            ->findByactive('1');

	        $submodelos = $this->getDoctrine()
	            ->getRepository('GestionBundle:submodelo')
	            ->findByactive('1');

	        $modelosubmodelos = $this->getDoctrine()
	            ->getRepository('GestionBundle:modelosubmodelo')
	            ->findAll();

	        return $this->render('GestionBundle:gestion/modelosubmodelo:index_modelosubmodelo.html.twig', array(
	            'modelos' => $modelos,
	            'submodelos' => $submodelos,
	            'modelosubmodelos' => $modelosubmodelos
	        ));

    }

    public function ajaxAction(Request $request) {

            $id_modelo = $_POST['modelo'];

            $modelosubmodelos = $this->getDoctrine()
            ->getRepository('GestionBundle:modelosubmodelo')
            ->findByidModelo($id_modelo);
            
            return $this->render('GestionBundle:gestion/modelosubmodelo:ajax_modelosubmodelo.html.twig', array(
                'modelosubmodelos' => $modelosubmodelos
            ));

    }

}