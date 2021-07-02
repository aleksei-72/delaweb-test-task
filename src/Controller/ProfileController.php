<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    /**
     * @Route("/profile", methods={"PATCH"})
     */
    public function patchUser(Request $request) {

        $content = json_decode($request->getContent(), true);

        //remove private data
        $content['password'] = null;

        $token = $request->cookies->get('token');

        if (!$token) {
            return $this->json(['error' => ErrorList::E_UNAUTHORIZE], 401);
        }

        $auth = new Authentication($this->getDoctrine());

        try {
            $token = $auth->getTokenByKey($token);
        } catch (\Exception $e) {
            return $this->json(['error' => ErrorList::E_UNAUTHORIZE], 401);
        }

        if ($token->getExp() < time()) {
            //token expired
            return $this->json(['error' => ErrorList::E_UNAUTHORIZE], 401);
        }

        $manager = $this->getDoctrine()->getManager();


        try {
            $token->getUser()->patch($this->getDoctrine(), $content);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }


        $manager->flush();

        return $this->json('',204);

    }

    private function clearCookieAndRedirectToLogin(): RedirectResponse {
        $response = new RedirectResponse('Location: /login');

        $tokenCookie = new Cookie('token', '', time() - 60, '/', null, false, false);
        $response->headers->setCookie($tokenCookie);
        return $response;
    }


}