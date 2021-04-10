<?php

namespace App\Controller;

use App\Entity\Url;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrlRedirectController extends AbstractController
{
    #[Route('/', name: 'url_index')]
    public function index(): Response
    {
        return $this->render('url_redirect/index.html.twig', [
            'controller_name' => 'UrlRedirectController',
        ]);
    }

    #[NoReturn] #[Route('/{short_uri}', name: 'url_redirect', requirements: ['short_uri'=>'^((?!api).)*$'])]
    public function redirect_url(string $short_uri, EntityManagerInterface $em): NoReturn
    {
        /*$url = $this->getDoctrine()
            ->getRepository(Url::class)
            ->findOneBy(['short_uri' => $short_uri]);*/

        $url = $em->getRepository(Url::class)->findOneBy(['short_uri' => $short_uri]);

        if (!$url) {
            throw $this->createNotFoundException(
                'No url found for ' . $short_uri
            );
        }

        try {
            // assert version
            $em->lock($url, LockMode::OPTIMISTIC, $url->getVersion());

            $url->setVisits($url->getVisits() + 1);
            $em->persist($url);
            $em->flush();

        } catch(OptimisticLockException $e) {
            echo "Sorry, but someone else has already changed this url. Please apply the changes again!";
        }

        header(sprintf("Location: %s", $url->getOrigUrl()));
        exit;
    }

    #[Route(
        path: '/api/urls/{id}/visits',
        name: 'url_get_visits',
        defaults: [
        '_api_resource_class' => Url::class,
        '_api_item_operation_name' => 'get_url_visits',
    ],
        methods: ['GET'],
    )]
    public function url_visits(Url $data): Url
    {
        return $data;
    }
}
