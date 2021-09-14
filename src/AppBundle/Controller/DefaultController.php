<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{

    public function gestionAction()
    {
        return $this->render('AppBundle:Consultas:index.html.twig');
    }

    public function langAction (Request $request) {
        return $this->render('AppBundle:Consultas:index.html.twig');
    }
}
