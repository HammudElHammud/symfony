<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\Admin\ImageRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="category_new", methods={"GET","POST"})
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function new(Request $request, CategoryRepository $categoryRepository, ImageRepository $imageRepository): Response
    {
        $category = new Category();
        $categories = $categoryRepository->findBy(['parentid' => null]);
        $images = $imageRepository->findAll();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_index');
        }
        return $this->render('admin/admin/category/new.html.twig', [
            'category' => $category,
            'categories' => $categories,
            'images' => $images,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('admin/admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Category $category
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository, ImageRepository $imageRepository): Response
    {
        $categories = $categoryRepository->findBy(['parentid' => null]);
        $images = $imageRepository->findAll();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $category->setParentid($categoryRepository->findOneBy(['id' => $request->request->get('category')['parentid']]));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_index');
        }
        return $this->render('admin/admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'categories' => $categories,
            'images' => $categories,
        ]);
    }

    /**
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index');
    }
}
