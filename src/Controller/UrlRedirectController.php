<?php

namespace App\Controller;

use App\Entity\Url;
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
    public function redirect_url(string $short_uri): NoReturn
    {
        $url = $this->getDoctrine()
            ->getRepository(Url::class)
            ->findOneBy(['short_uri' => $short_uri]);

        if (!$url) {
            throw $this->createNotFoundException(
                'No url found for ' . $short_uri
            );
        }

        header(sprintf("Location: %s", $url->getOrigUrl()));
        exit;
    }
}
