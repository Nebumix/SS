<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Form\Type\TagType;
use AppBundle\Entity\Tag;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
{
    /**
     * @Route("/insert/tag", name="insert_tag")
     */
    public function insertTagAction(Request $request)
    {
        $tag = new Tag();
        $tag->setAdded(new \DateTime('now'));
        $tag->setAdminId($this->getUser()->getId());

        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($tag);
            $em->flush();

            return $this->redirectToRoute('insert_tag');
        }

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Tag');
        $tags = $repository->findAll();

        return $this->render('tag/insert.html.twig', array(
            'form' => $form->createView(), 'tags' => $tags
        ));
    }

    /**
     * @Route("/edit/tag/{id}", name="edit_tag", requirements={
     *     "id": "\d+"
     * })
     *
     */
    public function editTagAction($id)
    {
        $tag = $this->getDoctrine()
            ->getRepository('AppBundle:Tag')
            ->find($id);

        if (!$tag) {
            throw $this->createNotFoundException(
                'No tag found for id '.$id
            );
        }

        $form = $this->createFormBuilder($tag)
            ->add('id', HiddenType::class)
            ->add('name', TextType::class)
            ->getForm();


        return $this->render('tag/edit_dialog.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     *
     * @Route("/edit/tag/save", name="edit_tag_save")
     *
     */
    public function editTagSaveAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('AppBundle:Tag')->find($request->request->get('id'));

        if (!$tag) {
            throw $this->createNotFoundException(
                'No product found for id '.$request->request->get('id')
            );
        }

        $tag->setName($request->request->get('name'));
        $em->flush();

        return new Response('1');
    }

    /**
     *
     * @Route("/delete/tag/{id}", name="delete_tag", requirements={
     *     "id": "\d+"
     * })
     *
     */
    public function deleteTagAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('AppBundle:Tag')->find($id);

        if (!$tag) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $em->remove($tag);
        $em->flush();

        //return $this->redirectToRoute('insert_category');
        return new Response('1');
    }

}
