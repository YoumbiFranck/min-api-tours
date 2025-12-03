<?php

namespace App\Controller;

use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Inscription d'un nouvel utilisateur
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            // Récupérer les données JSON
            $data = json_decode($request->getContent(), true);

            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->json([
                    'error' => 'Email et mot de passe requis'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Appeler le service d'inscription
            $result = $this->authService->register(
                $data['email'],
                $data['password']
            );

            return $this->json($result, JsonResponse::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Connexion d'un utilisateur
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        try {
            // Récupérer les données JSON
            $data = json_decode($request->getContent(), true);

            if (!isset($data['email']) || !isset($data['password'])) {
                return $this->json([
                    'error' => 'Email et mot de passe requis'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Appeler le service de connexion
            $result = $this->authService->login(
                $data['email'],
                $data['password']
            );

            return $this->json($result, JsonResponse::HTTP_OK);

        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Une erreur est survenue'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}