<?php

namespace App\Controller;

use App\DTO\SeriesCreateFormInput;
use App\Entity\{
    Episode,Season,Series
};
use App\Form\SeriesType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{
    RedirectResponse, Response, Request
};
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\{
    SubmitType,TextType
};

class SeriesController extends AbstractController
{
    public function __construct(
        private SeriesRepository $seriesRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }
    #[Route('/series', name: 'app_series')]
    public function index(Request $request): Response
    {
        $seriesList = $this->seriesRepository
            ->findAll();
        return $this->render('series/index.html.twig', [
            'controller_name' => 'SeriesController',
            'seriesList' => $seriesList
        ]);
    }
    #[Route('/series/create', name: 'app_series_create_form', methods: ['GET'])]
    public function addSeriesForm(): Response
    {
        $inputs = new SeriesCreateFormInput();
        $seriesForm = $this->createForm(
            SeriesType::class, $inputs
        );
        return $this->renderForm(
            'series/form.html.twig',
            compact('seriesForm')
        );
    }
    #[Route('/series/create',name: 'app_add_series', methods: ['POST'])]
    public function addSeries(Request $request): Response
    {
        $inputs = new SeriesCreateFormInput();

        $seriesForm = $this->createForm(
                SeriesType::class, $inputs
            )->handleRequest($request);
        if(!$seriesForm->isValid()){
            return $this->renderForm(
                'series/form.html.twig',
                compact('seriesForm')
            );
        }
        $series = new Series($inputs
            ->seriesName
        );
        for($i = 1; $i <= $inputs->seasonsQuantity; $i++){
            $season = new Season($i);
            for($j =1; $j <= $inputs->episodesPerSeason; $j++){
                $season->addEpisode(new Episode($j));
            }
            $series->addSeason($season);
        }
        $this->seriesRepository
            ->save(
                $series, true
        );
        $this->addFlash(
            'success',
            "Series \"{$series->getName()}\" created successfully!"
        );
        return new RedirectResponse('/series');
    }
    #[Route(
        '/series/delete/{id}',
        name: 'app_remove_series',
        methods: ['DELETE'],
        requirements: ['id' => '[0-9]+']
    )]
    public function removeSeries(int $id, Request $request)
    {
        $this->seriesRepository
            ->removeById($id
        );
        $this->addFlash(
            'success',
            'Series removed successfully!'
        );
        return new RedirectResponse('/series');
    }
    #[Route('/series/edit/{series}', name: 'app_edit_series_form', methods: ['GET'])]
    public function editSeriesForm(Series $series): Response
    {
        //$seriesDTO = new SeriesCreateFormInput($series->getName(), $series->getSeasons(), $series->getSeasons()->getEpisodes());
        $seriesForm = $this->createForm(SeriesType::class, $series, ['is_edit' => true]);
        return $this->renderForm(
            'series/form.html.twig',
            compact('seriesForm')
        );
    }
    #[Route('/series/edit/{series}', name: 'app_store_series_changes', methods: ['PATCH'])]
    public function storeSeriesChanges(
        Series $series, Request $request
            ): Response
    {
        $seriesForm = $this->createForm(
            SeriesType::class, $series, [
                'is_edit' => true
        ]);
        $seriesForm->handleRequest($request);
        if(!$seriesForm->isValid()){
            return $this->renderForm(
                'series/form.html.twig',
                compact('seriesForm',
                    'series'
                )
            );
        }

        /*$series->setName(
            $request->request->get('name')
        );
        */
        $this->addFlash('success',
                " \"{$series->getName()}\" series Successfully Edited!"
            );
        $this->entityManager->flush();
        return new RedirectResponse(
            '/series'
        );
    }
}
