<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\modelo;
use GestionBundle\Entity\submodelo;
use GestionBundle\Entity\asignarprocesoversion;
use GestionBundle\Entity\configuracionlinea;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;



class GestionController extends Controller
{
   
    public function gestionAction()
    {

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT COUNT(*) as modelos FROM modelo";
        $statement=$db->prepare($query);
        $statement->execute();
        $totalmodelos=$statement->fetchAll();

        $query2 = "SELECT COUNT(*) as submodelos FROM submodelo";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $totalsubmodelos=$statement2->fetchAll();

        $query3 = "SELECT COUNT(*) as listados FROM asignarprocesoversion";
        $statement3=$db->prepare($query3);
        $statement3->execute();
        $totallistados=$statement3->fetchAll();

        $query4 = "SELECT COUNT(*) as configuraciones FROM configuracionlinea";
        $statement4=$db->prepare($query4);
        $statement4->execute();
        $totalconfiguraciones=$statement4->fetchAll();


        return $this->render('GestionBundle:gestion:index.html.twig', array(
            'navgestion' => 1,
            'modelos' => $totalmodelos[0]['modelos'],
            'submodelos' => $totalsubmodelos[0]['submodelos'],
            'listados' => $totallistados[0]['listados'],
            'configuraciones' => $totalconfiguraciones[0]['configuraciones']
        ));
    }

    public function langAction (Request $request) {


        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT COUNT(*) as modelos FROM modelo";
        $statement=$db->prepare($query);
        $statement->execute();
        $totalmodelos=$statement->fetchAll();

        $query2 = "SELECT COUNT(*) as submodelos FROM submodelo";
        $statement2=$db->prepare($query2);
        $statement2->execute();
        $totalsubmodelos=$statement2->fetchAll();

        $query3 = "SELECT COUNT(*) as listados FROM asignarprocesoversion";
        $statement3=$db->prepare($query3);
        $statement3->execute();
        $totallistados=$statement3->fetchAll();

        $query4 = "SELECT COUNT(*) as configuraciones FROM configuracionlinea";
        $statement4=$db->prepare($query4);
        $statement4->execute();
        $totalconfiguraciones=$statement4->fetchAll();

    	return $this->render('GestionBundle:gestion:index.html.twig', array(
            'navgestion' => 1,
            'modelos' => $totalmodelos[0]['modelos'],
            'submodelos' => $totalsubmodelos[0]['submodelos'],
            'listados' => $totallistados[0]['listados'],
            'configuraciones' => $totalconfiguraciones[0]['configuraciones']
        ));
    }

}
