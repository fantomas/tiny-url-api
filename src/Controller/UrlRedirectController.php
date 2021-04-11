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

    #[NoReturn] #[Route(path: '/{shortUri}', name: 'url_redirect', requirements: ['shortUri'=>'^((?!api).)*$'])]
    public function redirect_url(string $shortUri, EntityManagerInterface $em): NoReturn
    {
        $url = $em->getRepository(Url::class)->findOneBy(['shortUri' => $shortUri]);

        if (!$url) {
            throw $this->createNotFoundException(
                'No url found for ' . $shortUri
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
}
