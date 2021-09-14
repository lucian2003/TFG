<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\proceso;
use GestionBundle\Entity\planta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;

use Doctrine\ORM\EntityRepository;


class ProcesoController extends Controller
{
   
    public function indexAction() {

        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');

        $procesos = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findByactive('1');

        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT proceso.tipo FROM proceso group by proceso.tipo";
        $statement=$db->prepare($query);
        $statement->execute();
        $tipos=$statement->fetchAll();

        return $this->render('GestionBundle:gestion/proceso:index_proceso.html.twig', array(
            'procesos' => $procesos,
            'plantas' => $plantas,
            'tipos' => $tipos,
            'navproceso' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/proceso:index_proceso.html.twig');
    }

    public function createAction(Request $request) {

        $proceso = new proceso;

        $form = $this->createFormBuilder($proceso)


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
            ->add('tipo', ChoiceType::class, array('label' => 'tipo', 'choices' => array('INTERNO' => 'INT', 'EXTERNO' => 'EXT'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre = $form['nombre']->getData();
            $tipo = $form['tipo']->getData();

            $proceso->setIdPlanta($id_planta);
            $proceso->setNombre($nombre);
            $proceso->setTipo($tipo);
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($proceso);
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_proceso');
        }

        return $this->render('GestionBundle:gestion/proceso:create_proceso.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function editAction(Request $request) {

        $id=$_POST['idProceso'];
        $proceso = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->find($id);

        $form = $this->createFormBuilder($proceso)
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
            ->add('tipo', ChoiceType::class, array('label' => 'tipo', 'choices' => array('INTERNO' => 'INT', 'EXTERNO' => 'EXT'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $nombre = $form['nombre']->getData();
            $tipo = $form['tipo']->getData();

            $em = $this->getDoctrine()->getManager();
            $proceso = $em->getRepository('GestionBundle:proceso')->find($id);

            $proceso->setIdPlanta($id_planta);
            $proceso->setNombre($nombre);
            $proceso->setTipo($tipo);
            
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_proceso');
        }

        return $this->render('GestionBundle:gestion/proceso:edit_proceso.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }

    public function detailsAction(Request $request) {

        $id=$_POST['idProceso'];
        $proceso = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->find($id);

            return $this->render('GestionBundle:gestion/proceso:details_proceso.html.twig', array(
                'proceso' => $proceso
            ));
    }

    public function searchByPlanta() {


        $planta=$_POST['planta'];
        $tipo=$_POST['tipo'];   
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();
        if (empty($planta)){
            $query = "SELECT * FROM proceso WHERE tipo = ? AND active = 1 ORDER BY id DESC";
            $params=array($tipo); 
               
        }else if(empty($tipo)){
            $query = "SELECT * FROM proceso WHERE id_planta = ? AND active = 1 ORDER BY id DESC";
            $params=array($planta);
        
        }else {
            $query = "SELECT * FROM proceso WHERE id_planta = ? AND tipo = ? AND active = 1 ORDER BY id DESC";
            $params=array($planta, $tipo);
        
        }
        $statement=$db->prepare($query);
        $statement->execute($params);
        $procesos=$statement->fetchAll();

    
        return $this->render('GestionBundle:gestion/proceso:filter_proceso.html.twig', array(
                'procesos' => $procesos
            ));

    }



    public function deleteAction(Request $request) {

        $id=$_POST['idProceso'];
        $em = $this->getDoctrine()->getManager();
        $proceso = $em->getRepository('GestionBundle:proceso')->find($id);

        $em->remove($proceso);
        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        return $this->redirectToRoute('app_index_proceso');
    }
}