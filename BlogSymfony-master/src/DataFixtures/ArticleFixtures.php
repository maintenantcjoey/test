<?php

namespace App\DataFixtures;


use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Comment;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR'); //instancier le faker

        //créer avec faker 3 catégories
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            //remplir les catégories
            $category->setTitle($faker->sentence()); //on demande a faker de générer lui mm le titre avec la méthode sentence
            $category->setDescription($faker->paragraph());

            $manager->persist($category);

            //créer entre 4 eet 6articles ds les caté
            for ($j = 1; $j <= mt_rand(4,6); $j++) {

                $article = new Article();

                // $content = '<p>' . join($faker->paragraphs(10),'</p><p>') . '</p>';     //TROUVER PK LE MSG DERREUR

                $article->setTitle($faker->sentence())
                    ->setContent($faker->paragraph())
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6months'))
                    ->setCategory($category); //associer l'article à une catégory

                $manager->persist($article);

                //on ajoute des commentaires à l'article
                for($k = 1;$k <= mt_rand(4,10);$k ++){
                    $comment = new Comment();

                   // $content = '<p>' . join($faker->paragraphs(2),'</p><p>') . '</p>';

                   $now = new \DateTime();
                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;
                    $minimum = '-' . $days . 'days';

                   $days = (new \DateTime())->diff($article->getCreatedAt())->days;
                    //var_dump($faker->paragraph());
                    $comment->setAuthor($faker->name)
                            ->setContent($faker->paragraph())
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
