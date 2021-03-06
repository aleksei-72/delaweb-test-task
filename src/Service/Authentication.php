<?php


namespace App\Service;


use App\Entity\Token;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Authentication {

    private $doctrine;
    private $tokenExp = 60*60; //1 hour
    private $tokenLength = 25;


    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }


    /**
     * @param string $key
     * @return Token
     * @throws \Exception
     */
    public function getTokenByKey(string $key): Token {
        $token = $this->doctrine->getRepository(Token::class)->findOneBy(['key' => $key]);

        if (!$token) {
            throw new \Exception("invalid_key");
        }

        return $token;
    }

    /**
     * @param User $user
     * @return Token
     */
    public function generateToken(User $user): Token {

        $token = new Token();
        $token->setUser($user);
        $token->setExp(time() + $this->tokenExp);

        while (1) {
            $key = $this->generateKey();

            //check for unique
            if (!$this->doctrine->getRepository(Token::class)->findOneBy(['key' => $key])) {
                $token->setKey($key);

                return $token;
            }
        }
    }


    /**
     * @param User $user
     */
    public function removeExpiredSessions(User $user) {
        $sessions = $this->doctrine->getRepository(Token::class)->findBy(['targetUser' => $user]);

        if (!$sessions) {
            return;
        }

        $manager = $this->doctrine->getManager();

        foreach ($sessions as $session) {
            if ($session->getExp() < time()) {
                $manager->remove($session);
            }
        }

        $manager->flush();
    }

    private function generateKey(): string {
        $symbols = "qwertyuiopasdfghjklzxcvbnm1234567890_";

        $key = "";

        for ($i = 0; $i < $this->tokenLength; $i ++) {
            $key .= $symbols[random_int(0, strlen($symbols) - 1)];
        }

        return  $key;
    }
}