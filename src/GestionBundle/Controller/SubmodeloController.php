<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\submodelo;
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


class SubmodeloController extends Controller
{
   
    public function indexAction() {

        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');

        $submodelos = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->findByactive('1');

        return $this->render('GestionBundle:gestion/submodelo:index_submodelo.html.twig', array(
            'submodelos' => $submodelos,
            'plantas' => $plantas,
            'navsubmodelo' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/submodelo:index_submodelo.html.twig');
    }

    public function createAction(Request $request) {

        $submodelo = new submodelo;

        $form = $this->createFormBuilder($submodelo)


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
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre = $form['nombre']->getData();

            $submodelo->setIdPlanta($id_planta);
            $submodelo->setNombre($nombre);
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($submodelo);
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_submodelo');
        }

        return $this->render('GestionBundle:gestion/submodelo:create_submodelo.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function editAction(Request $request) {

        $id=$_POST['idSubmodelo'];
        $submodelo = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->find($id);

        $form = $this->createFormBuilder($submodelo)
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
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre = $form['nombre']->getData();

            $em = $this->getDoctrine()->getManager();
            $submodelo = $em->getRepository('GestionBundle:submodelo')->find($id);

            $submodelo->setIdPlanta($id_planta);
            $submodelo->setNombre($nombre);
            
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_submodelo');
        }

        return $this->render('GestionBundle:gestion/submodelo:edit_submodelo.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }

    public function detailsAction(Request $request) {

        $id=$_POST['idSubmodelo'];
        $submodelo = $this->getDoctrine()
            ->getRepository('GestionBundle:submodelo')
            ->find($id);

            return $this->render('GestionBundle:gestion/submodelo:details_submodelo.html.twig', array(
                'submodelo' => $submodelo
            ));
    }

    public function searchByPlanta() {


        $planta=$_POST['planta'];    
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM submodelo WHERE id_planta = ? AND active = 1 ORDER BY id DESC";
        $statement=$db->prepare($query);
        $params=array($planta);
        $statement->execute($params);
        $submodelos=$statement->fetchAll();


        return $this->render('GestionBundle:gestion/submodelo:filter_submodelo.html.twig', array(
                'submodelos' => $submodelos
            ));


        }

    public function deleteAction(Request $request) {

        $id=$_POST['idSubmodelo'];
        $em = $this->getDoctrine()->getManager();
        $submodelo = $em->getRepository('GestionBundle:submodelo')->find($id);

        $submodelo->setActive('0');

        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        return $this->redirectToRoute('app_index_submodelo');
    }

}