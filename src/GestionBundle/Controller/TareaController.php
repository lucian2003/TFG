<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\tarea;
use GestionBundle\Entity\planta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;

use Doctrine\ORM\EntityRepository;


class TareaController extends Controller
{
   
    public function indexAction($page) {

        $pageSize = 10;

        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');

        $tareas = $this->getDoctrine()
            ->getRepository('GestionBundle:tarea')
            ->getPaginateTareas($pageSize, $page);

        $totalItems = count($tareas);
        $pagesCount = ceil($totalItems/$pageSize);

        return $this->render('GestionBundle:gestion/tarea:index_tarea.html.twig', array(
            'tareas' => $tareas,
            'plantas' => $plantas,
            'totalItems' => $totalItems,
            'pagesCount' => $pagesCount,
            'page' => $page,
            'page_m' => $page
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/planta:index_modelo.html.twig');
    }

    public function createAction(Request $request) {

        $tarea = new tarea;

        $form = $this->createFormBuilder($tarea)


            ->add('id_planta', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:planta',
                'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.active = 1');
            },
                'choice_label' => 'nombre', 'label' => 'planta', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('nombre_es', TextType::class, array('label' => 'nombreES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('nombre_en', TextType::class, array(
                'label' => 'nombreEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre_es = $form['nombre_es']->getData();
            $nombre_en = $form['nombre_en']->getData();

            $tarea->setIdPlanta($id_planta);
            $tarea->setNombreES($nombre_es);
            $tarea->setNombreEN($nombre_en);
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($tarea);
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_tarea');
        }

        return $this->render('GestionBundle:gestion/tarea:create_tarea.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function editAction(Request $request) {

        $id=$_POST['idTarea'];
        $tarea = $this->getDoctrine()
            ->getRepository('GestionBundle:tarea')
            ->find($id);

        $form = $this->createFormBuilder($tarea)
            ->add('id_planta', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:planta',
                'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.active = 1');
            },
                'choice_label' => 'nombre', 'label' => 'planta', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('nombre_es', TextType::class, array('label' => 'nombreES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('nombre_en', TextType::class, array(
                'label' => 'nombreEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre_es = $form['nombre_es']->getData();
            $nombre_en = $form['nombre_en']->getData();

            $em = $this->getDoctrine()->getManager();
            $linea = $em->getRepository('GestionBundle:linea')->find($id);

            $tarea->setIdPlanta($id_planta);
            $tarea->setNombreES($nombre_es);
            $tarea->setNombreEN($nombre_en);
            
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_tarea');
        }

        return $this->render('GestionBundle:gestion/tarea:edit_tarea.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }

    public function detailsAction(Request $request) {

        $id=$_POST['idTarea'];
        $tarea = $this->getDoctrine()
            ->getRepository('GestionBundle:tarea')
            ->find($id);

            return $this->render('GestionBundle:gestion/tarea:details_tarea.html.twig', array(
                'tarea' => $tarea
            ));
    }

    public function searchByPlanta() {

        
        $planta=$_POST['planta'];    
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM tarea WHERE id_planta = ? AND active = 1 ORDER BY id DESC";
        $statement=$db->prepare($query);
        $params=array($planta);
        $statement->execute($params);
        $tareas=$statement->fetchAll();


        return $this->render('GestionBundle:gestion/tarea:filter_tarea.html.twig', array(
                'tareas' => $tareas
            ));


        }


    public function deleteAction(Request $request) {

        $id=$_POST['idTarea'];
        $em = $this->getDoctrine()->getManager();
        $tarea = $em->getRepository('GestionBundle:tarea')->find($id);

        $tarea->setActive('0');

        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        return $this->redirectToRoute('app_index_tarea');
    }

}