<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// Needed packages for Lock
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\Lock;
// Allow to store lock state on MySQL using Doctrine
use Symfony\Component\Lock\Store\DoctrineDbalStore;

// Instanciate elements
function get_lock(string $name)
{
    // Store used to store the lock state
    $store = new DoctrineDbalStore($_ENV['DATABASE_URL']);
    // Create table if not exists
    try {
        $store->createTable();
    } catch (\Exception $e) {
        // Table already exists
    }
    $key = new Key($name); // key used to identify the lock
    $lock = new Lock(
        $key,
        $store, // store used to store the lock state
        20,  // time before lock is released
        false // autoRelease if instance is destroyed
    );
    return $lock;
}

#[Route('/admin/article')]
class CRUDArticleController extends AbstractController
{
    #[Route('/', name: 'app_admin_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin_article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Add current logged user as author
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' =>
            $this->getUser()->getUserIdentifier()]);
            $user->addArticle($article);
            $entityManager->persist($user);
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('admin_article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get lock for this article editing
            $edit_lock = get_lock("article_edit_".$article->getId());

            // Case lock is acquired from another user, request will wait
            // Case not locked, lock it to avoid concurrent editing
            if(!$edit_lock->isAcquired()) {
                $edit_lock->acquire(true);
                $entityManager->flush();
                // Case no release, wait for ttl
                $edit_lock->release();

            }
            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
