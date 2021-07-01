<?php

namespace App\Controller;

use App\Entity\Organization;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'users' => $usersJson,
            'authorization2' => '5'
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

        try {

            $firstName = $content['first_name'];
            $lastName = $content['last_name'];
            $phone = $content['phone'];
            $organizationTitle = $content['organization'];
            $password = $content['password'];
            $invitatoryId = (int)$content['invitatory_id'];

        } catch (\Exception $e) {
            return $this->json(['error' => ErrorList::E_INCOMPLETE_DATA], 400);
        }

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();

        if (!preg_match('/^[А-Я][а-я]{1,25}$/u', $firstName)) {
            return $this->json(['error' => ErrorList::E_INVALID_FIRST_NAME], 400);
        }

        if (!preg_match('/^[А-Я][а-я]{1,25}$/u', $lastName)) {
            return $this->json(['error' => ErrorList::E_INVALID_LAST_NAME], 400);
        }

        if (!preg_match('/^[0-9]{6,15}$/', $phone)) {
            return $this->json(['error' => ErrorList::E_INVALID_PHONE], 400);
        }
        if ($doctrine->getRepository(User::class)->findOneBy(['phone' => $phone])) {
            return $this->json(['error' => ErrorList::E_NOT_UNIQUE_PHONE], 400);
        }

        if (mb_strlen($organizationTitle) < 5 || mb_strlen($organizationTitle) > 50) {
            return $this->json(['error' => ErrorList::E_INVALID_ORGANIZATION], 400);
        }

        if (!preg_match('/(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*]{6,}/', $password)) {
            return $this->json(['error' => ErrorList::E_INVALID_ORGANIZATION], 400);
        }




        $invitatoryUser = $doctrine->getRepository(User::class)->find($invitatoryId);

        if (!$invitatoryUser) {
            return $this->json(['error' => ErrorList::E_INVALID_INVITATORY_ID], 400);
        }

        $organization = $doctrine->getRepository(Organization::class)
            ->findOneBy(['title' => $organizationTitle]);

        if (!$organization) {
            //create new Organization
            $organization = new Organization();
            $organization->setTitle($organizationTitle);

            $manager->persist($organization);
        }

        $newUser = new User();
        $newUser->setFirstName($firstName);
        $newUser->setLastName($lastName);
        $newUser->setPhone($phone);
        $newUser->setOrganization($organization);
        $newUser->setPassword($password);
        $newUser->setInvitatory($invitatoryUser);

        $manager->persist($newUser);


        $manager->flush();

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

        $targetUser = $doctrine->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (!$targetUser) {
            return $this->json(['error' => ErrorList::E_USER_NOT_FOUND], 400);
        }

        if (!$targetUser->verifyPassword($password)) {
            return $this->json(['error' => ErrorList::E_INVALID_PASSWORD], 400);
        }

        return $this->json('ok');
    }
}