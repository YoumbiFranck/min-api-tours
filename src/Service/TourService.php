<?php

namespace App\Service;

use App\Entity\Tour;
use App\Repository\TourRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TourService
{
    public function __construct(
        private TourRepository $tourRepository,
        private ValidatorInterface $validator
    ) {}

    /**
     * Créer un nouveau tour avec application de la réduction automatique
     */
    public function createTour(Tour $tour): array
    {
        // Validation de l'entité
        $errors = $this->validator->validate($tour);
        if (count($errors) > 0) {
            return $this->formatValidationErrors($errors);
        }

        // Appliquer la réduction si le voyage dure plus de 10 jours
        $this->applyDiscount($tour);

        // Sauvegarder
        $this->tourRepository->save($tour, true);

        return [
            'success' => true,
            'tour' => $tour
        ];
    }

    /**
     * Mettre à jour un tour existant
     */
    public function updateTour(Tour $tour): array
    {
        // Validation de l'entité
        $errors = $this->validator->validate($tour);
        if (count($errors) > 0) {
            return $this->formatValidationErrors($errors);
        }

        // Recalculer la réduction si nécessaire
        $this->applyDiscount($tour);

        // Sauvegarder
        $this->tourRepository->save($tour, true);

        return [
            'success' => true,
            'tour' => $tour
        ];
    }

    /**
     * Supprimer un tour
     */
    public function deleteTour(Tour $tour): void
    {
        $this->tourRepository->remove($tour, true);
    }

    /**
     * Rechercher des tours avec filtres
     */
    public function searchTours(?string $country = null, ?float $minPrice = null, ?float $maxPrice = null): array
    {
        return $this->tourRepository->findWithFilters($country, $minPrice, $maxPrice);
    }

    /**
     * Appliquer une réduction de 10% si le voyage dure plus de 10 jours
     */
    private function applyDiscount(Tour $tour): void
    {
        $duration = $tour->getDuration();

        if ($duration > 10) {
            $originalPrice = $tour->getPrice();
            $discountedPrice = $originalPrice * 0.90;
            $tour->setPrice($discountedPrice);
        }
    }

    /**
     * Formatter les erreurs de validation en tableau lisible
     */
    private function formatValidationErrors($errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }

        return [
            'success' => false,
            'errors' => $errorMessages
        ];
    }
}