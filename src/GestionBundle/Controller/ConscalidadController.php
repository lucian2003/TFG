<?php

namespace GestionBundle\Controller;

use GestionBundle\Entity\conscalidad;
use GestionBundle\Entity\planta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EntityType;

use Doctrine\ORM\EntityRepository;


class ConscalidadController extends Controller
{
   
    public function indexAction($page) {


        $plantas = $this->getDoctrine()
            ->getRepository('GestionBundle:planta')
            ->findByactive('1');

        $conscalidads = $this->getDoctrine()
            ->getRepository('GestionBundle:conscalidad')
            ->findByactive('1');

        return $this->render('GestionBundle:gestion/conscalidad:index_conscalidad.html.twig', array(
            'conscalidads' => $conscalidads,
            'plantas' => $plantas,
            'navconscalidad' => 1
        ));
    }

    public function langAction (Request $request) {
    	return $this->render('GestionBundle:gestion/conscalidad:index_conscalidad.html.twig');
    }

    public function createAction(Request $request) {

        $conscalidad = new conscalidad;

        $form = $this->createFormBuilder($conscalidad)


            ->add('id_planta', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:planta',
                'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.active = 1');
            },
                'choice_label' => 'nombre', 'label' => 'planta', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('descripcion_es', TextareaType::class, array('label' => 'descripcionES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('descripcion_en', TextareaType::class, array(
                'label' => 'descripcionEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $descripcion_es = $form['descripcion_es']->getData();
            $descripcion_en = $form['descripcion_en']->getData();

            $conscalidad->setIdPlanta($id_planta);
            $conscalidad->setDescripcionES($descripcion_es);
            $conscalidad->setDescripcionEN($descripcion_en);
            
            $em = $this->getDoctrine()->getManager();

            $em->persist($conscalidad);
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_conscalidad');
        }

        return $this->render('GestionBundle:gestion/conscalidad:create_conscalidad.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function editAction(Request $request) {

        $id=$_POST['idConscalidad'];
        $conscalidad = $this->getDoctrine()
            ->getRepository('GestionBundle:conscalidad')
            ->find($id);

        $form = $this->createFormBuilder($conscalidad)
            ->add('id_planta', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class, [
                'class' => 'GestionBundle:planta',
                'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.active = 1');
            },
                'choice_label' => 'nombre', 'label' => 'planta', 'attr' => array('class' => 'form-control'
                , 'style' => 'margin-bottom:15px')
            ])
            ->add('descripcion_es', TextareaType::class, array('label' => 'descripcionES', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('descripcion_en', TextareaType::class, array(
                'label' => 'descripcionEN',
                'required' => false,
                'attr' => array(
                    'class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('SAVE', SubmitType::class, array('label' => 'crear', 'attr' => array('class' => 'btn btn-success waves-effect waves-light', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $id_planta = $form['id_planta']->getData();
            $descripcion_es = $form['descripcion_es']->getData();
            $descripcion_en = $form['descripcion_en']->getData();

            $em = $this->getDoctrine()->getManager();
            $conscalidad = $em->getRepository('GestionBundle:conscalidad')->find($id);

            $conscalidad->setIdPlanta($id_planta);
            $conscalidad->setDescripcionES($descripcion_es);
            $conscalidad->setDescripcionEN($descripcion_en);
            
            $em->flush();

            $this->addFlash(
                'notice',
                'exito'
            );

            return $this->redirectToRoute('app_index_conscalidad');
        }

        return $this->render('GestionBundle:gestion/conscalidad:edit_conscalidad.html.twig', array(
            'form' => $form->createView(), 'id'=> $id
        ));
    }

    public function detailsAction(Request $request) {

        $id=$_POST['idConscalidad'];
        $conscalidad = $this->getDoctrine()
            ->getRepository('GestionBundle:conscalidad')
            ->find($id);
    

            return $this->render('GestionBundle:gestion/conscalidad:details_conscalidad.html.twig', array(
                'conscalidad' => $conscalidad
            ));
    }

    public function searchByPlanta() {


        $planta=$_POST['planta'];    
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();

        $query = "SELECT * FROM conscalidad WHERE id_planta = ? AND active = 1 ORDER BY id DESC";
        $statement=$db->prepare($query);
        $params=array($planta);
        $statement->execute($params);
        $conscalidads=$statement->fetchAll();


        return $this->render('GestionBundle:gestion/conscalidad:filter_conscalidad.html.twig', array(
                'conscalidads' => $conscalidads,
            ));

    }

    public function deleteAction(Request $request) {

        $id=$_POST['idConscalidad'];
        $em = $this->getDoctrine()->getManager();
        $conscalidad = $em->getRepository('GestionBundle:conscalidad')->find($id);

        $conscalidad->setActive('0');

        $em->flush();

        $this->addFlash(
                'notice',
                'exito'
            );

        return $this->redirectToRoute('app_index_conscalidad');
    }

}