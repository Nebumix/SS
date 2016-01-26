<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\ProductType;
use AppBundle\Entity\Product;

use AppBundle\Form\Type\CategoryType;
use AppBundle\Entity\Category;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/insert/product", name="insert_product")
     */
    public function insertProductAction(Request $request)
    {
        $product = new Product();
        $product->setAdded(new \DateTime('now'));
        $product->setState('Italy');

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('insert_product');
        }

        return $this->render('product/insert.html.twig', array(
            'form' => $form->createView(),
        ));
    }

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

}
