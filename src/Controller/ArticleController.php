<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Service\SlackClient;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Exception\NoConfigurationException;

class ArticleController extends AbstractController
{
    /**
     * Currently unused: just showing a controller with a constructor!
     */
    private $isDebug;

    public function __construct(
        bool $isDebug,
        private readonly LoggerInterface $logger
    ) {
        $this->isDebug = $isDebug;
        $this->logger->info('Controller instantiated!');
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(ArticleRepository $repository, LoggerInterface $logger, $isMac, HttpKernelInterface $httpKernel)
    {
        $articles = $repository->findAllPublishedOrderedByNewest();

        $logger->info('Inside the controller');

        /*//manual sub-request
        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->attributes->set('_controller', 'App\\Controller\\PartialController::trendingQuotes');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');

        $responce = $httpKernel->handle(
            $request,
            HttpKernelInterface::SUB_REQUEST
        );
        dump($responce);*/

        return $this->render('article/homepage.html.twig', [
            'articles' => $articles,
            'isMac' => $isMac
        ]);
    }

    /**
     * @Route("/news/{slug}", name="article_show")
     */
    public function show(Article $article, SlackClient $slack, ArticleRepository $articleRepository, $isMac)
    {
        dump($isMac);
//        return;
//        $article = $articleRepository->findOneBy(['slug' => $slug]);

//        if (!$article) {
//            throw $this->createNotFoundException();
//        }

        if ($article->getSlug() === 'khaaaaaan') {
            $slack->sendMessage('Kahn', 'Ah, Kirk, my old friend...');
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/news/{slug}/heart", name="article_toggle_heart", methods={"POST"})
     */
    public function toggleArticleHeart(Article $article, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $article->incrementHeartCount();
        $em->flush();

        $logger->info('Article is being hearted!');

        return new JsonResponse(['hearts' => $article->getHeartCount()]);
    }
}
