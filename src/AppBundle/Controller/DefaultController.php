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
        return $this->render(
            'landing.html.twig',
            [
                'results' => array_reverse($this->getResults()),
            ]
        );
    }

    /** @return array */
    protected function getResults()
    {
        return json_decode(file_get_contents(__DIR__ . '/results.json'));
    }
}
