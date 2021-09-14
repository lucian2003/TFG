<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\opbasica;
use GestionBundle\Entity\proceso;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Doctrine\ORM\EntityRepository;


class OpbasicaController extends Controller
{
   
    public function indexAction() {


        $repository = $this->getDoctrine()
            ->getRepository(opbasica::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.tipo = :tipo')
            ->andWhere('p.active = :active')
            ->setParameter('tipo', 'OPB')
            ->setParameter('active', 1)
            ->getQuery();

        $opbasicas = $query->getResult();

        $procesos = $this->getDoctrine()
            ->getRepository('GestionBundle:proceso')
            ->findByactive('1');

        return $this->render('GestionBundle:gestion/opbasica:index_opbasica.html.twig', array(
            'opbasicas' => $opbasicas,
            'procesos' => $procesos,
            'navopbasica' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/opbasica:index_opbasica.html.twig');
    }

    public function createAction(Request $request) {

        date_default_timezone_set('Europe/Madrid');

        $opbasica = new opbasica;

        $form = $this->createFormBuilder($opbasica)


            ->add('id_proceso', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:proceso',
                'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.active = 1');
            },
                'choice_label' => 'nombre', 'label' => 'proceso', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('nombre_es', TextType::class, array('label' => 'nombreES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('nombre_en', TextType::class, array(
                'label' => 'nombreEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('descripcion_es', TextareaType::class, array('label' => 'descripcionES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('descripcion_en', TextareaType::class, array(
                'label' => 'descripcionEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('tiempo', TimeType::class, [
                    'label' => 'tiempo',
                    'required' => true,
                    'widget' => 'choice',
                    'html5' => true,
                    'input' => 'timestamp',
                    'with_seconds' => true,
                    'placeholder' => ['hour' => 'hh', 'minute' => 'mm', 'second' => 'ss',],
                    'attr' => array('style' => 'margin-bottom:15px')])
            ->add('tipo', ChoiceType::class, array('label' => 'tipo', 'choices' => array('OPB' => 'OPB', 'DES' => 'DES'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('comentario_es', TextareaType::class, array(
                'label' => 'comentarioES',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('comentario_en', TextareaType::class, array(
                'label' => 'comentarioEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $id_proceso = $form['id_proceso']->getData();
            $nombre_es = $form['nombre_es']->getData();
            $nombre_en = $form['nombre_en']->getData();
            $descripcion_es = $form['descripcion_es']->getData();
            $descripcion_en = $form['descripcion_en']->getData();
            $tiempo = $form['tiempo']->getData()+3600; 
            $tipo = $form['tipo']->getData();
            $comentario_es = $form['comentario_es']->getData();
            $comentario_en = $form['comentario_en']->getData();

            $opbasica->setIdProceso($id_proceso);
            $opbasica->setNombreES($nombre_es);
            $opbasica->setNombreEN($nombre_en);
            $opbasica->setDescripcionES($descripcion_es);
            $opbasica->setDescripcionEN($descripcion_en);
            $opbasica->setTiempo($tiempo);
            $opbasica->setTipo($tipo);
            $opbasica->setComentarioES($comentario_es);
            $opbasica->setComentarioEN($comentario_en);

            
            $em = $this->getDoctrine()->getManager();

            $em->persist($opbasica);
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_opbasica');
        }

        return $this->render('GestionBundle:gestion/opbasica:create_opbasica.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function editAction(Request $request) {

        $id=$_POST['idOpbasica'];
        $opbasica = $this->getDoctrine()
            ->getRepository('GestionBundle:opbasica')
            ->find($id);

        $a=$opbasica->getTiempo()-3600;
        $opbasica->setTiempo($a);

        $form = $this->createFormBuilder($opbasica)

            ->add('id_proceso', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:proceso',
                'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.active = 1');
            },
                'choice_label' => 'nombre', 'label' => 'proceso', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('nombre_es', TextType::class, array('label' => 'nombreES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('nombre_en', TextType::class, array(
                'label' => 'nombreEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('descripcion_es', TextareaType::class, array('label' => 'descripcionES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('descripcion_en', TextareaType::class, array(
                'label' => 'descripcionEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('tiempo', TimeType::class, [
                    'label' => 'tiempo',
                    'required' => false,
                    'widget' => 'choice',
                    'html5' => true,
                    'input' => 'timestamp',
                    'with_seconds' => true,
                    'placeholder' => ['hour' => 'hh', 'minute' => 'mm', 'second' => 'ss',]])
            ->add('tipo', ChoiceType::class, array('label' => 'tipo', 'choices' => array('OPB' => 'OPB', 'DES' => 'DES'), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('comentario_es', TextareaType::class, array(
                'label' => 'comentarioES',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('comentario_en', TextareaType::class, array(
                'label' => 'comentarioEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $id_proceso = $form['id_proceso']->getData();
            $nombre_es = $form['nombre_es']->getData();
            $nombre_en = $form['nombre_en']->getData();
            $descripcion_es = $form['descripcion_es']->getData();
            $descripcion_en = $form['descripcion_en']->getData();
            $tiempo = $form['tiempo']->getData()+3600;
            $tipo = $form['tipo']->getData();
            $comentario_es = $form['comentario_es']->getData();
            $comentario_en = $form['comentario_en']->getData();

            $em = $this->getDoctrine()->getManager();
            $opbasica = $em->getRepository('GestionBundle:opbasica')->find($id);

            $opbasica->setIdProceso($id_proceso);
            $opbasica->setNombreES($nombre_es);
            $opbasica->setNombreEN($nombre_en);
            $opbasica->setDescripcionES($descripcion_es);
            $opbasica->setDescripcionEN($descripcion_en);
            $opbasica->setTiempo($tiempo);
            $opbasica->setTipo($tipo);
            $opbasica->setComentarioES($comentario_es);
            $opbasica->setComentarioEN($comentario_en);
            
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_opbasica');
        }

        return $this->render('GestionBundle:gestion/opbasica:edit_opbasica.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }

    public function detailsAction(Request $request) {

        $id=$_POST['idOpbasica'];
        $opbasica = $this->getDoctrine()
            ->getRepository('GestionBundle:opbasica')
            ->find($id);

            return $this->render('GestionBundle:gestion/opbasica:details_opbasica.html.twig', array(
                'opbasica' => $opbasica
            ));
    }

    public function searchByProceso() {


        $proceso=$_POST['proceso'];
          
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();
        if (empty($proceso)){
            $query = "SELECT * FROM opbasica WHERE tipo = 'OPB' AND active = 1 ORDER BY id DESC";
        
        }else {
            $query = "SELECT * FROM opbasica WHERE id_proceso = ? AND tipo = 'OPB' AND active = 1 ORDER BY id DESC";
            $params=array($proceso);
        
        }

        $statement=$db->prepare($query);
        $statement->execute($params);
        $opbasicas=$statement->fetchAll();

    
        return $this->render('GestionBundle:gestion/opbasica:filter_opbasica.html.twig', array(
                'opbasicas' => $opbasicas,
            ));

    
    }

    public function deleteAction(Request $request) {

        $id=$_POST['idOpbasica'];
        $em = $this->getDoctrine()->getManager();
        $opbasica = $em->getRepository('GestionBundle:opbasica')->find($id);

        $opbasica->setActive('0');

        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        return $this->redirectToRoute('app_index_opbasica');
    }

}