<?php

namespace App\Repository;

use App\DTO\SeriesCreationInputDTO;
use App\Entity\Series;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Series>
 *
 * @method Series|null find($id, $lockMode = null, $lockVersion = null)
 * @method Series|null findOneBy(array $criteria, array $orderBy = null)
 * @method Series[]    findAll()
 * @method Series[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private LoggerInterface $logger,
        private SeasonRepository $seasonRepository,
        private EpisodeRepository $episodeRepository)
    {
        parent::__construct($registry, Series::class);
    }

    public function save(SeriesCreationInputDTO $DTO, bool $flush = false): Series
    {
        $series = new Series($DTO->seriesName, $DTO->coverImage);
        $this->getEntityManager()->persist($series);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
        try{
            $this->seasonRepository->addSeasonsQuantity($DTO->seasonsQuantity, $series->getId());
            $seasons = $this->seasonRepository->findBy(['series' => $series]);
            $this->episodeRepository->addEpisodesPerSeason($DTO->episodesPerSeason, $seasons);
        }catch(\Exception $e){
            $this->logger->error($e->getMessage());
            $this->remove($series, true);
            dd($e);
        }
        return $series;
    }

    public function remove(Series $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function removeById(int $id): void
    {
        $series = $this->getEntityManager()
            ->getPartialReference(
                Series::class, $id
            );
        $this->remove($series, true);
    }

//    /**
//     * @return Series[] Returns an array of Series objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Series
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
