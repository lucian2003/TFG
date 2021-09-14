<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\linea;
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


class LineaController extends Controller
{
   
    public function indexAction() {

        

        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');

        $lineas = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->findByactive('1');

        return $this->render('GestionBundle:gestion/linea:index_linea.html.twig', array(
            'lineas' => $lineas,
            'plantas' => $plantas,
            'navlinea' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/planta:index_modelo.html.twig');
    }

    public function createAction(Request $request) {

        $linea = new linea;

        $form = $this->createFormBuilder($linea)


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
            ->add('productividad', TextType::class, array('label' => 'productividad', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('estaciones', IntegerType::class, array('label' => 'estaciones', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre = $form['nombre']->getData();
            $productividad = $form['productividad']->getData();
            $estaciones = $form['estaciones']->getData();

            $linea->setIdPlanta($id_planta);
            $linea->setNombre($nombre);
            $linea->setProductividad($productividad);
            $linea->setEstaciones($estaciones);
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($linea);
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_linea');
        }

        return $this->render('GestionBundle:gestion/linea:create_linea.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function editAction(Request $request) {

        $id=$_POST['idLinea'];
        $linea = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->find($id);

        $form = $this->createFormBuilder($linea)
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
            ->add('productividad', TextType::class, array('label' => 'productividad', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('estaciones', IntegerType::class, array('label' => 'estaciones', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre = $form['nombre']->getData();
            $productividad = $form['productividad']->getData();
            $estaciones = $form['estaciones']->getData();

            $em = $this->getDoctrine()->getManager();
            $linea = $em->getRepository('GestionBundle:linea')->find($id);

            $linea->setIdPlanta($id_planta);
            $linea->setNombre($nombre);
            $linea->setProductividad($productividad);
            $linea->setEstaciones($estaciones);
            
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_linea');
        }

        return $this->render('GestionBundle:gestion/linea:edit_linea.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }

    public function detailsAction(Request $request) {

        $id=$_POST['idLinea'];
        $linea = $this->getDoctrine()
            ->getRepository('GestionBundle:linea')
            ->find($id);

            return $this->render('GestionBundle:gestion/linea:details_linea.html.twig', array(
                'linea' => $linea
            ));
    }

    public function searchByPlanta() {

        
        $planta=$_POST['planta'];    
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM linea WHERE id_planta = ? AND active = 1 ORDER BY id DESC";
        $statement=$db->prepare($query);
        $params=array($planta);
        $statement->execute($params);
        $lineas=$statement->fetchAll();


        return $this->render('GestionBundle:gestion/linea:filter_linea.html.twig', array(
                'lineas' => $lineas
            ));


        }


    public function deleteAction(Request $request) {

        $id=$_POST['idLinea'];
        $em = $this->getDoctrine()->getManager();
        $linea = $em->getRepository('GestionBundle:linea')->find($id);

        $linea->setActive('0');

        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        return $this->redirectToRoute('app_index_linea');
    }

}