<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\ProductType;
use AppBundle\Entity\Product;

use AppBundle\Form\Type\CategoryType;
use AppBundle\Entity\Category;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{

    /**
     * @Route("/insert/category", name="insert_category")
     */
    public function insertCategoryAction(Request $request)
    {
        $category = new Category();
        $category->setAdded(new \DateTime('now'));
        $category->setAdminId($this->getUser()->getId());

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('insert_category');
        }

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Category');
        $categories = $repository->findByParent(NULL);

        return $this->render('category/insert.html.twig', array(
            'form' => $form->createView(), 'categories' => $categories
        ));
    }

    /**
     * @Route("/edit/category/{id}", name="edit_category", requirements={
     *     "id": "\d+"
     * })
     *
     */
    public function editCategoryAction($id)
    {
        $category = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No category found for id '.$id
            );
        }

        $form = $this->createFormBuilder($category)
            ->add('id', HiddenType::class)
            ->add('name', TextType::class)
            ->getForm();


        return $this->render('category/edit_dialog.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/edit/category/save", name="edit_category_save")
     *
     */
    public function editCategorySaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')->find($request->request->get('id'));

        if (!$category) {
            throw $this->createNotFoundException(
                'No product found for id '.$request->request->get('id')
            );
        }

        $category->setName($request->request->get('name'));
        $em->flush();

        return new Response('1');
    }

    /**
     *
     * @Route("/delete/category/{id}", name="delete_category", requirements={
     *     "id": "\d+"
     * })
     *
     */
    public function deleteCategoryAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $em->remove($category);
        $em->flush();

        //return $this->redirectToRoute('insert_category');
        return new Response('1');
    }

    /**
     *
     * @Route("/update/category/parent/{id}/{parent_id}", name="update_category_parent", requirements={
     *     "id": "\d+", "parent_id": "\d+"
     * })
     *
     */
    public function updateCategoryParentAction($id, $parent_id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('AppBundle:Category')->find($id);
        $parent = $em->getRepository('AppBundle:Category')->find($parent_id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $category->setParent($parent);
        $em->flush();

        //return $this->redirectToRoute('insert_category');
        return new Response('1');
    }

}
