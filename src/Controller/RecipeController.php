<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Entity\Ingredient;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * This controller display all recipes
     * @param RecipeRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */

    #[Route('/recette', name: 'app_recipe', methods: ['get'])]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * This controller allows us to create a new recipe
     */

    #[Route('/recette/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été crée avec succès !'
            );

            return $this->redirectToRoute('app_recipe');
        }

        
        return $this->render('recipe/_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allows us to create a new recipe
     */

    #[Route('/recette/edit/{id}', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifiée avec succès !'
            );

            return $this->redirectToRoute('app_recipe');
        }
        $form = $this->createForm(recipeType::class, $recipe);

        return $this->render('recipe/_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allow us to DELETE a recipe
     */
    #[Route('/ingredient/recipe/{id}', name: 'app_recipe_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Recipe $recipe): Response
    {
        if(!$recipe) {
            $this->addFlash(
                'warning',
                'La recette n\'a pas été trouvée !'
            );
        }
        $manager->remove($recipe);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès !'
        );


        return $this->redirectToRoute('app_ingredient');
    }

}
