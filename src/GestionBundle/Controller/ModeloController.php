<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\modelo;
use GestionBundle\Entity\planta;
use GestionBundle\Entity\gama;
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


class ModeloController extends Controller
{
   
    public function indexAction() {

        
        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');

        $modelos = $this->getDoctrine()
            ->getRepository('GestionBundle:modelo')
            ->findByactive('1');


        return $this->render('GestionBundle:gestion/modelo:index_modelo.html.twig', array(
            'modelos' => $modelos,
            'plantas' => $plantas,
            'navmodelo' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/modelo:index_modelo.html.twig');
    }

    public function createAction(Request $request) {

        $modelo = new modelo;

        $form = $this->createFormBuilder($modelo)


            ->add('id_planta', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:planta',
                'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.active = 1');
            },
                'choice_label' => 'nombre', 'label' => 'planta', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('nombre', TextType::class, array('label' => 'nombre', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('area', ChoiceType::class, array('label' => 'area', 'choices' => array('EO' => 'EO', 'FV' => 'FV', 'ME' => 'ME', 'PVT' => 'PVT'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('id_gama', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:gama', 'choice_label' => 'nombre', 'label' => 'gama', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre = $form['nombre']->getData();
            $area = $form['area']->getData();
            $id_gama = $form['id_gama']->getData();

            $modelo->setIdPlanta($id_planta);
            $modelo->setNombre($nombre);
            $modelo->setArea($area);
            $modelo->setIdGama($id_gama);
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($modelo);
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_modelo');
        }

        return $this->render('GestionBundle:gestion/modelo:create_modelo.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function editAction(Request $request) {

        $id=$_POST['idModelo'];
        $modelo = $this->getDoctrine()
            ->getRepository('GestionBundle:modelo')
            ->find($id);

        $form = $this->createFormBuilder($modelo)
            ->add('id_planta', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:planta',
                'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.active = 1');
            },
                'choice_label' => 'nombre', 'label' => 'planta', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('nombre', TextType::class, array('label' => 'nombre', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('area', ChoiceType::class, array('label' => 'area', 'choices' => array('EO' => 'EO', 'FV' => 'FV', 'ME' => 'ME', 'PVT' => 'PVT'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('id_gama', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:gama', 'choice_label' => 'nombre', 'label' => 'gama', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre = $form['nombre']->getData();
            $area = $form['area']->getData();
            $id_gama = $form['id_gama']->getData();

            $em = $this->getDoctrine()->getManager();
            $modelo = $em->getRepository('GestionBundle:modelo')->find($id);

            $modelo->setIdPlanta($id_planta);
            $modelo->setNombre($nombre);
            $modelo->setArea($area);
            $modelo->setIdGama($id_gama);
            
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_modelo');
        }

        return $this->render('GestionBundle:gestion/modelo:edit_modelo.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }

    public function detailsAction(Request $request) {

        $id=$_POST['idModelo'];
        $modelo = $this->getDoctrine()
            ->getRepository('GestionBundle:modelo')
            ->find($id);

            return $this->render('GestionBundle:gestion/modelo:details_modelo.html.twig', array(
                'modelo' => $modelo
            ));
    }

    public function searchByPlanta() {

        
        $planta=$_POST['planta'];    
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM modelo WHERE id_planta = ? AND active = 1 ORDER BY id DESC";
        $statement=$db->prepare($query);
        $params=array($planta);
        $statement->execute($params);
        $modelos=$statement->fetchAll();


        return $this->render('GestionBundle:gestion/modelo:filter_modelo.html.twig', array(
                'modelos' => $modelos
            ));

        }

    public function deleteAction(Request $request) {

        $id=$_POST['idModelo'];
        $em = $this->getDoctrine()->getManager();
        $modelo = $em->getRepository('GestionBundle:modelo')->find($id);

        $modelo->setActive('0');

        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        return $this->redirectToRoute('app_index_modelo');
    }

}