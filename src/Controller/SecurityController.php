<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function login(IriConverterInterface $iriConverter)
    {

        if(!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->json([
                'error' => "Nieprawidłowe dane do logowania!"
            ], 400);
        }

        return new Response(null, 204, [
            'Location' => $iriConverter->getIriFromItem($this->getUser())
        ]);

//        return $this->json([
//                'user' => $this->getUser() ? $this->getUser()->getId() : null]
//        );
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception("nie powinien zostać osiągnięty");
    }
}