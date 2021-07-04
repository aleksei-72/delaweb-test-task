<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * @Route("/", methods={"GET"})
     * @return RedirectResponse
     */
    public function showIndexPage(): RedirectResponse {
        return $this->redirect('/profile');
    }
}