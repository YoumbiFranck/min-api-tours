<?php

namespace App\Controller;

use App\Entity\Tour;
use App\Repository\TourRepository;
use App\Service\TourService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/tours', name: 'api_tours_')]
class TourController extends AbstractController
{
    public function __construct(
        private TourService $tourService,
        private TourRepository $tourRepository,
        private SerializerInterface $serializer
    ) {}

    /**
     * GET /api/tours - Liste tous les tours avec filtres optionnels
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        // Récupérer les paramètres de query
        $country = $request->query->get('country');
        $minPrice = $request->query->get('minPrice') ? (float) $request->query->get('minPrice') : null;
        $maxPrice = $request->query->get('maxPrice') ? (float) $request->query->get('maxPrice') : null;

        // Rechercher avec filtres
        $tours = $this->tourService->searchTours($country, $minPrice, $maxPrice);

        return $this->json($tours, JsonResponse::HTTP_OK, [], [
            'groups' => ['tour:read']
        ]);
    }

    /**
     * GET /api/tours/{id} - Récupère un tour spécifique
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $tour = $this->tourRepository->find($id);

        if (!$tour) {
            return $this->json([
                'error' => 'Tour non trouvé'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json($tour, JsonResponse::HTTP_OK, [], [
            'groups' => ['tour:read']
        ]);
    }

    /**
     * POST /api/tours - Créer un nouveau tour
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            // Désérialiser le JSON en objet Tour
            $tour = $this->serializer->deserialize(
                $request->getContent(),
                Tour::class,
                'json'
            );

            // Créer le tour via le service (avec logique métier)
            $result = $this->tourService->createTour($tour);

            // Si erreurs de validation
            if (!$result['success']) {
                return $this->json([
                    'error' => 'Erreurs de validation',
                    'details' => $result['errors']
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Retourner le tour créé
            return $this->json($result['tour'], JsonResponse::HTTP_CREATED, [], [
                'groups' => ['tour:read']
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur lors de la création',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * PUT /api/tours/{id} - Mettre à jour un tour
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            // Récupérer le tour existant
            $tour = $this->tourRepository->find($id);

            if (!$tour) {
                return $this->json([
                    'error' => 'Tour non trouvé'
                ], JsonResponse::HTTP_NOT_FOUND);
            }

            // Désérialiser et mettre à jour l'objet existant
            $this->serializer->deserialize(
                $request->getContent(),
                Tour::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $tour]
            );

            // Mettre à jour via le service
            $result = $this->tourService->updateTour($tour);

            // Si erreurs de validation
            if (!$result['success']) {
                return $this->json([
                    'error' => 'Erreurs de validation',
                    'details' => $result['errors']
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Retourner le tour mis à jour
            return $this->json($result['tour'], JsonResponse::HTTP_OK, [], [
                'groups' => ['tour:read']
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Erreur lors de la mise à jour',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * DELETE /api/tours/{id} - Supprimer un tour
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $tour = $this->tourRepository->find($id);

        if (!$tour) {
            return $this->json([
                'error' => 'Tour non trouvé'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // Supprimer via le service
        $this->tourService->deleteTour($tour);

        return $this->json([
            'message' => 'Tour supprimé avec succès'
        ], JsonResponse::HTTP_OK);
    }
}