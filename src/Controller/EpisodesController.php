<?php

namespace App\Controller;

use App\Entity\Season;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EpisodesController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    )
    {
        
    }
    #[Route('/season/{season}/episodes',
        name: 'app_episodes',
        methods: ['GET']
    )]
    public function index(Season $season): Response
    {
        $episodes = $season->getEpisodes();
        return $this->render('episodes/index.html.twig', [
            'season' => $season,
            'series' => $season->getSeries(),
            'episodes' => $season->getEpisodes()
        ]);
    }
    #[Route('/season/{season}/episodes',
        name: 'app_watch_episodes',
        methods: ['POST']
    )]
    public function watch(Season $season, Request $request): Response
    {
        $watchedEpisodes = array_keys(
            $request
                ->request
                ->all(
                    'episodes'
                ));
        if(count($watchedEpisodes) > 2){
            $this->logger->info('More than two episodes marked as watched');
        }
        $episodes = $season->getEpisodes();
        foreach($episodes as $episode){
            $episode->setWatched(
                in_array($episode->getId(),
                    $watchedEpisodes
            ));
        }
        $this->entityManager
            ->flush();
        return new RedirectResponse(
            "/season/{$season->getId()}/episodes"
        );
    }
}
