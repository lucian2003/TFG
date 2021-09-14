<?php

namespace GestionBundle\Controller;


use GestionBundle\Entity\opbasica;
use GestionBundle\Entity\conscalidad;
use GestionBundle\Entity\asignarconsideracion;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityRepository;

class AsignarConsideracionController extends Controller
{
   
    public function indexAction() {


        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $repository = $this->getDoctrine()
            ->getRepository(opbasica::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.tipo = :tipo')
            ->setParameter('tipo', 'OPB')
            ->getQuery();

        $opbasicas = $query->getResult();

        $consasignadas = $this->getDoctrine()
            ->getRepository('GestionBundle:asignarconsideracion')
            ->findAll();

        return $this->render('GestionBundle:gestion/asignarconsideracion:index_asignarconsideracion.html.twig', array(
            'opbasicas' => $opbasicas,
            /*'submodelos' => $submodelos,*/
            'consasignadas' => $consasignadas,
            'navasignarconsideracion' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/asignarconsideracion:index_asignarconsideracion.html.twig');
    }


    public function getConsCalidad(Request $request)
    {
        $opbasica = $request->get('id');
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM conscalidad WHERE id NOT IN (SELECT id_consideracioncalidad FROM asignarconsideracion WHERE id_operacionbasica = $opbasica)";
        $statement=$db->prepare($query);
        $statement->execute();
        $consideracionescalidad=$statement->fetchAll();


        return $response = new JsonResponse(['data' => $consideracionescalidad]);

    }

    public function createAction(Request $request) {

            $id_opbasica = $_POST['opbasica'];
            
            $id_consideracion = $_POST['consideracion'];

            $entityManager = $this->getDoctrine()->getManager();

            $asignarconsideracion = new asignarconsideracion();

            $opbasica = $entityManager->getRepository('GestionBundle:opbasica')->find($id_opbasica);
            $asignarconsideracion->setIdOperacionbasica($opbasica);

            $consideracion = $entityManager->getRepository('GestionBundle:conscalidad')->find($id_consideracion);
            $asignarconsideracion->setIdConsideracioncalidad($consideracion);

            $entityManager->persist($asignarconsideracion);

            $entityManager->flush();

            $opbasicas = $this->getDoctrine()
            ->getRepository('GestionBundle:opbasica')
            ->findByactive('1');

            $consasignadas = $this->getDoctrine()
                ->getRepository('GestionBundle:asignarconsideracion')
                ->findAll();

            return $this->render('GestionBundle:gestion/asignarconsideracion:index_asignarconsideracion.html.twig', array(
                'opbasicas' => $opbasicas,
                'consasignadas' => $consasignadas
            ));

    }

    public function ajaxAction(Request $request) {

            $id_opbasica = $_POST['opbasica'];

            $consasignadas = $this->getDoctrine()
            ->getRepository('GestionBundle:asignarconsideracion')
            ->findByidOperacionbasica($id_opbasica);
            
            return $this->render('GestionBundle:gestion/asignarconsideracion:ajax_asignarconsideracion.html.twig', array(
                'consasignadas' => $consasignadas
            ));

    }

}