<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleController extends Controller
{
    /**
     * @Route("/show-articles")
     */
    public function showAction()
    {
        $articles = $this->getDoctrine()
        ->getRepository('AppBundle:Article')
        ->findByIdDesc();

        return $this->render('Article/show.html.twig', array(
            'articles' => $articles
        ));
    }

    /**
     * @Route("/create-article")
     */
    public function createAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirect("/show-articles");
        }

        return $this->render('Article/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/view-article/{id}")
     */
    public function viewAction($id)
    {
        $article= $this->getDoctrine()
        ->getRepository('AppBundle:Article')
        ->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
            'Il n\'y a pas d\'article correspondant'
            );
        }
        
        return $this->render('Article/view.html.twig', array(
            'article' => $article
        ));
    }

    /**
     * @Route("/update-article/{id}")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->find($id);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if (!$article) {
            throw $this->createNotFoundException(
                'Aucun article ne correspond à cet ID'
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirect("/show-articles");
        }

        return $this->render('Article/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/delete-article/{id}")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->find($id);

        if (!$article) {
            throw $this->createNotFoundException(
                'Article inconnu'
            );
        }

        $em->remove($article);
        $em->flush();

        return $this->redirect("/show-articles");
    }

}
