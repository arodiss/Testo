<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    const QUESTION_INTRO = 'introduction';
    const QUESTION_TEXT = 'text';
    const FINISH = 'finish';

    /**
     * @Extra\Route("/test/start/", name="TestStart")
     */
    public function startAction(Request $request)
    {
        $request->getSession()->clear();

        return $this->render('test/start.html.twig');
    }

    /**
     * @Extra\Route("/test/next/", name="TestNext")
     */
    public function nextAction(Request $request)
    {
        $this->processRequest($request);

        return $this->render(sprintf(
            'test/%s.html.twig',
            $this->getNextQuestion($request))
        );
    }

    /** @param Request $request */
    protected function processRequest(Request $request)
    {
        if ($request->get(self::QUESTION_INTRO)) {
            $this->addFlash('success', "Let's start, " . $request->get(self::QUESTION_INTRO));
            $request->getSession()->set(self::QUESTION_INTRO, $request->get(self::QUESTION_INTRO));
        } elseif ($request->get(self::QUESTION_TEXT)) {
            $this->addFlash('success', "You are able to read, it's pretty awesome");
            $request->getSession()->set(self::QUESTION_TEXT, 1);
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function getNextQuestion(Request $request)
    {
        $questionsAvailable = [];
        $score = 0;

        if (null === $request->getSession()->get(self::QUESTION_TEXT)) {
            $questionsAvailable[] = self::QUESTION_TEXT;
        } else {
            $score += $request->getSession()->get(self::QUESTION_TEXT);
        }
        $request->getSession()->set('score', $score);

        if (count($questionsAvailable) === 0) {
            return self::FINISH;
        }

        return $questionsAvailable[array_rand($questionsAvailable)];
    }
}
