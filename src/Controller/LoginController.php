<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class LoginController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route ("/registration", methods={"GET"})
     */
    public function showRegistrationPage(Request $request): Response {
        return $this->render('registration.html.twig', [
            'users' => [
                ['id' => 1, 'firstName' => 'Дмитрий', 'lastName' => 'Иванов'],
                ['id' => 2, 'firstName' => 'Перт', 'lastName' => 'Дмитриев'],
                ['id' => 3, 'firstName' => 'Михаил', 'lastName' => 'Данилов']
            ]
        ]);
    }
}