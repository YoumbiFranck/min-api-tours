<?php

namespace App\Repository;

use App\Entity\Tour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tour>
 */
class TourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tour::class);
    }

    /**
     * Recherche de tours avec filtres optionnels
     *
     * @param string|null $country Filtrer par pays
     * @param float|null $minPrice Prix minimum
     * @param float|null $maxPrice Prix maximum
     * @return Tour[]
     */
    public function findWithFilters(?string $country = null, ?float $minPrice = null, ?float $maxPrice = null): array
    {
        $qb = $this->createQueryBuilder('t');

        // Filtre par pays
        if ($country) {
            $qb->andWhere('t.country = :country')
                ->setParameter('country', $country);
        }

        // Filtre par prix minimum
        if ($minPrice !== null) {
            $qb->andWhere('t.price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }

        // Filtre par prix maximum
        if ($maxPrice !== null) {
            $qb->andWhere('t.price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }

        // Trier par date de début (les plus proches en premier)
        $qb->orderBy('t.startDate', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte le nombre de jours d'un tour
     */
    public function save(Tour $tour, bool $flush = false): void
    {
        $this->getEntityManager()->persist($tour);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime un tour
     */
    public function remove(Tour $tour, bool $flush = false): void
    {
        $this->getEntityManager()->remove($tour);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Tour[] Returns an array of Tour objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tour
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
