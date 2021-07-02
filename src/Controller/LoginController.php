<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Authentication;
use App\ErrorList;


class LoginController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     * @Route ("/registration", methods={"GET"})
     */
    public function showRegistrationPage(Request $request): Response {
        $usersList = $this->getDoctrine()->getRepository(User::class)->findAll();

        $usersJson = array();

        foreach ($usersList as $user) {
            array_push($usersJson, $user->toArray());
        }

        return $this->render('index.html.twig', [
            'bodyTemplateName' => 'registration.html.twig',
            'users' => $usersJson
        ]);
    }


    /**
     * @param Request $request
     * @return Response
     * @Route ("/login", methods={"GET"})
     */
    public function showLoginPage(Request $request): Response {

        return $this->render('index.html.twig', [
            'bodyTemplateName' => 'login.html.twig'
        ]);
    }



    /**
     * @param Request $request
     * @return JsonResponse
     * @Route ("/registration", methods={"POST"})
     */
    public function registrationUser(Request $request): JsonResponse {

        $content = json_decode($request->getContent(), true);

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();


        $newUser = new User();

        try {
            $newUser->patch($doctrine, $content);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode());
        }

        $manager->persist($newUser);

        try {
            $manager->flush();
        } catch (\Exception $e) {

            return $this->json(['error' => ErrorList::E_INCOMPLETE_DATA], 400);
        }

        return $this->json(['id' => $newUser->getId()]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/login", methods={"POST"})
     */
    public function login(Request $request): JsonResponse {

        $content = json_decode($request->getContent(), true);

        try {
            $phone = $content['phone'];
            $password = $content['password'];
        } catch (\Exception $e) {
            return $this->json(['error' => ErrorList::E_INCOMPLETE_DATA], 400);
        }

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();

        $targetUser = $doctrine->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (!$targetUser) {
            return $this->json(['error' => ErrorList::E_USER_NOT_FOUND], 400);
        }

        if (!$targetUser->verifyPassword($password)) {
            return $this->json(['error' => ErrorList::E_INVALID_PASSWORD], 400);
        }

        $auth = new Authentication($doctrine);

        $auth->removeExpiredSessions($targetUser);
        $token = $auth->generateToken($targetUser);

        $response = new JsonResponse();

        $tokenCookie = new Cookie('token', $token->getKey(), $token->getExp(), '/', null, false, false);
        $response->headers->setCookie($tokenCookie);

        $manager->persist($token);
        $manager->flush();

        return $response;
    }
}