<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Authentication;
use App\ErrorList;


class ProfileController extends AbstractController
{

    /**
     * @param Request $request
     * @return Response
     * @Route ("/profile", methods={"GET"})
     */
    public function showProfilePage(Request $request): Response {

        $token = $request->cookies->get('token');

        if (!$token) {
            return $this->redirect('/login');
        }

        $auth = new Authentication($this->getDoctrine());

        try {
            $token = $auth->getTokenByKey($token);
        } catch (\Exception $e) {
            //invalid token
            return $this->clearCookieAndRedirectToLogin();
        }

        if ($token->getExp() < time()) {
            //token expired
            $auth->removeExpiredSessions($token->getUser());
            return $this->clearCookieAndRedirectToLogin();
        }

        $userRepos = $this->getDoctrine()->getRepository(User::class);

        $usersList = $userRepos->findAll();

        $usersJson = array();

        foreach ($usersList as $user) {
            array_push($usersJson, $user->toArray());
        }

        return $this->render('index.html.twig', [
            'bodyTemplateName' => 'profile.html.twig',
            'currentUser' => $token->getUser()->toArray(),
            'invitatoryUser' => $token->getUser()->getInvitatory()->toArray(),
            'users' => $usersJson
        ]);
    }

    private function clearCookieAndRedirectToLogin(): Response {
        $response = new Response();

        $tokenCookie = new Cookie('token', '', time() - 60, '/', null, false, false);
        $response->headers->setCookie($tokenCookie);
        $response->headers->set('Location: /login');
        $response->setStatusCode(302);
        return $response;
    }

}