<?php


namespace App\EventListener;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use  Symfony\Component\HttpKernel\Event\ExceptionEvent;
use \Symfony\Component\HttpFoundation\Response;


class ExceptionListener extends AbstractController
{
    public function onKernelException(ExceptionEvent $e)
    {
        $exeption = $e->getException();

        $code = $exeption->getCode();

        if (substr($exeption->getMessage(), 0, 8) == "No route") {
            $code = 404;
        }

        $e->setResponse($this->render('index.html.twig', [
            'bodyTemplateName' => 'error.html.twig',
            'code' => $code,
            'description' => $exeption->getMessage()
        ]));
    }
}