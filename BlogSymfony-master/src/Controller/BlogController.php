<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo)
    {
        /*$repo = $this->getDoctrine()->getRepository(Article::class); //plus besoin de cette ligne ou l'on demande
        a doctrine de chercher les repos article si l'on met en param de la fonction le repository d'article) car
        cest une injection de dépendance*/

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('blog/home.html.twig');

    }

    /**
     * @Route ("/blog/new", name="blog_create")
     * @Route ("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {
        if(!$article) {
            $article = new Article();
        }

        $form = $this->createFormBuilder($article)
                        ->add('title')
                        ->add('content')
                        ->add('image')
                        ->add('category', EntityType::class, [
                            'class' => Category::class,
                            'choice_label' => 'title'
                        ])
                        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article, Request $request, ObjectManager $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment); //lier le form à l'objet comment

        $form->handleRequest($request); //pr que S analyse la requete

        if($form->isSubmitted() && $form->isValid()){ //si le form et soumis et valide
            $comment->setCreatedAt(new \DateTime()) //alors S le lie le comm a une date de création
                    ->setArticle($article); //et S lie le comm à l'article

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]); //on reste sur la mm page

        }
       /* $repo = $this->getDoctrine()->getRepository(Article::class);

        $article = $repo->find($id); // on retire tout car avec les param de la fonction et la route = param converters
       cad que sym va chercher l'article qui a l'id qu'il lit ds la route*/

        return $this->render('blog/show.html.twig',[
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
