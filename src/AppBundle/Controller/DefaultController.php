<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Extra\Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('landing.html.twig');
    }
}
