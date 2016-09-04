<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Extra;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    const QUESTION_INTRO = 'introduction';
    const QUESTION_TEXT = 'text';
    const QUESTION_SUM = 'sum';
    const QUESTION_LANGS = 'langs';
    const QUESTION_DATE = 'date';
    const QUESTION_VIDEO = 'video';
    const FINISH = 'finish';

    /**
     * @Extra\Route("/test/start/", name="TestStart")
     */
    public function startAction(Request $request)
    {
        $request->getSession()->clear();
        $request->getSession()->set('time_start', new \DateTime());

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
        } elseif ($request->get(self::QUESTION_SUM)) {
            $response = $request->get(self::QUESTION_SUM);
            if ($response['sum'] == $response['number1'] + $response['number2']) {
                $this->addFlash('success', "What a calculation!");
                $request->getSession()->set(self::QUESTION_SUM, 1);
            } else {
                $this->addFlash('warning', "Well, not exactly");
                $request->getSession()->set(self::QUESTION_SUM, 0);
            }
        } elseif ($request->get(self::QUESTION_LANGS)) {
            $response = $request->get(self::QUESTION_LANGS);
            if (count($response) > 1 && false === isset($response['VB'])) {
                $this->addFlash('success', "Good skills!");
                $request->getSession()->set(self::QUESTION_LANGS, 1);
            } elseif (1 >= count($response)) {
                $this->addFlash('warning', "It's a pity, we need you to have a bit of programming background");
                $request->getSession()->set(self::QUESTION_LANGS, 0);
            } else {
                $this->addFlash('warning', "Basic?! Are you kidding me?!");
                $request->getSession()->set(self::QUESTION_LANGS, 0);
            }
        } elseif ($request->get(self::QUESTION_DATE)) {
            if ($request->get(self::QUESTION_DATE) === date('l')) {
                $this->addFlash('success', "You did it!");
                $request->getSession()->set(self::QUESTION_DATE, 1);
            } else {
                $this->addFlash('warning', "Too bad, you are lost");
                $request->getSession()->set(self::QUESTION_DATE, 0);
            }
        } elseif ($request->get(self::QUESTION_VIDEO)) {
            if ($request->get(self::QUESTION_VIDEO) === 'umbanda') {
                $this->addFlash('success', "Thank you for your patience!");
                $request->getSession()->set(self::QUESTION_VIDEO, 1);
            } else {
                $this->addFlash('warning', "Too much temper for us");
                $request->getSession()->set(self::QUESTION_VIDEO, 0);
            }
        }
    }

    /*** @param Request $request
     * @return string
     */
    protected function getNextQuestion(Request $request)
    {
        $questionsAvailable = [];
        $score = 0;

        foreach ([self::QUESTION_TEXT, self::QUESTION_SUM, self::QUESTION_LANGS, self::QUESTION_DATE, self::QUESTION_VIDEO] as $question) {
            if (null === $request->getSession()->get($question)) {
                $questionsAvailable[] = $question;
            } else {
                $score += $request->getSession()->get($question);
            }            
        }
        $request->getSession()->set('score', $score);
        $request->getSession()->set(
            'time_spent',
            date_diff(new \DateTime(), $request->getSession()->get('time_start'))->format('%I:%S')
        );

        if (count($questionsAvailable) === 0) {
            return self::FINISH;
        }

        return $questionsAvailable[array_rand($questionsAvailable)];
    }
}
