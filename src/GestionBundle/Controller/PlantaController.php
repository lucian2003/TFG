<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\planta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class PlantaController extends Controller
{
   
    public function indexAction() {

        
        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');
   

        return $this->render('GestionBundle:gestion/planta:index_planta.html.twig', array(
            'plantas' => $plantas,
            'navplanta' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/planta:index_planta.html.twig');
    }

    public function createAction(Request $request) {

        $planta = new planta;

        $form = $this->createFormBuilder($planta)
            ->add('nombre', TextType::class, array('label' => 'nombre', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $nombre = $form['nombre']->getData();

            $planta->setNombre($nombre);
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($planta);
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_planta');
        }

        return $this->render('GestionBundle:gestion/planta:create_planta.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function editAction(Request $request) {

        $id=$_POST['idPlanta'];
        $planta = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->find($id);

        $form = $this->createFormBuilder($planta)
            ->add('nombre', TextType::class, array('label' => 'nombre', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $nombre = $form['nombre']->getData();

            $em = $this->getDoctrine()->getManager();
            $planta = $em->getRepository('GestionBundle:planta')->find($id);

            $planta->setNombre($nombre);
            
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_planta');
        }

        return $this->render('GestionBundle:gestion/planta:edit_planta.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }

    public function detailsAction(Request $request) {

        $id=$_POST['idPlanta'];
        $planta = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->find($id);

            return $this->render('GestionBundle:gestion/planta:details_planta.html.twig', array(
                'planta' => $planta
            ));
    }

    public function deleteAction(Request $request) {

        $id=$_POST['idPlanta'];
        $em = $this->getDoctrine()->getManager();
        $planta = $em->getRepository('GestionBundle:planta')->find($id);

        $planta->setActive('0');
            
        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        return $this->redirectToRoute('app_index_planta');
    }

}
