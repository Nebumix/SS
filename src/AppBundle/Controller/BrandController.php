<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\BrandType;
use AppBundle\Entity\Brand;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    /**
     * @Route("/insert/brand", name="insert_brand")
     */
    public function insertBrandAction(Request $request)
    {
        $brand = new Brand();
        $brand->setAdded(new \DateTime('now'));
        $brand->setAdminId($this->getUser()->getId());

        $form = $this->createForm(BrandType::class, $brand);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($brand);
            $em->flush();

            return $this->redirectToRoute('insert_brand');
        }

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Brand');
        $brands = $repository->findAll();

        return $this->render('brand/insert.html.twig', array(
            'form' => $form->createView(), 'brands' => $brands
        ));
    }

    /**
     * @Route("/edit/brand/{id}", name="edit_brand", requirements={
     *     "id": "\d+"
     * })
     *
     */
    public function editBrandAction($id)
    {
        $brand = $this->getDoctrine()
            ->getRepository('AppBundle:Brand')
            ->find($id);

        if (!$brand) {
            throw $this->createNotFoundException(
                'No brand found for id '.$id
            );
        }

        $form = $this->createFormBuilder($brand)
            ->add('id', HiddenType::class)
            ->add('name', TextType::class)
            ->getForm();


        return $this->render('brand/edit_dialog.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/edit/brand/save", name="edit_brand_save")
     *
     */
    public function editBrandSaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AppBundle:Brand')->find($request->request->get('id'));

        if (!$brand) {
            throw $this->createNotFoundException(
                'No product found for id '.$request->request->get('id')
            );
        }

        $brand->setName($request->request->get('name'));
        $em->flush();

        return new Response('1');
    }

    /**
     *
     * @Route("/delete/brand/{id}", name="delete_brand", requirements={
     *     "id": "\d+"
     * })
     *
     */
    public function deleteBrandAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $brand = $em->getRepository('AppBundle:Brand')->find($id);

        if (!$brand) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $em->remove($brand);
        $em->flush();

        //return $this->redirectToRoute('insert_category');
        return new Response('1');
    }

}
