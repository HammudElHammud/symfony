<?php

namespace App\Controller\Admin;

use App\Entity\Joined;
use App\Form\JoinedType;
use App\Repository\JoinedRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/joined")
 */
class JoinedController extends AbstractController
{
    /**
     * @Route("/", name="joined_index", methods={"GET"})
     * @param JoinedRepository $joinedRepository
     * @return Response
     */
    public function index(JoinedRepository $joinedRepository): Response
    {
        return $this->render('admin/admin/joined/index.html.twig', [
            'joineds' => $joinedRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="joined_show", methods={"GET"})
     */
    public function show(Joined $joined): Response
    {
        return $this->render('admin/admin/joined/show.html.twig', [
            'joined' => $joined,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="joined_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Joined $joined): Response
    {
        $form = $this->createForm(JoinedType::class, $joined);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('joined_index');
        }

        return $this->render('admin/admin/joined/edit.html.twig', [
            'joined' => $joined,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="joined_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Joined $joined): Response
    {
        if ($this->isCsrfTokenValid('delete'.$joined->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($joined);
            $entityManager->flush();
        }

        return $this->redirectToRoute('joined_index');
    }

    /**
     * @Route("/accept/{id}", name="accept_joined")
     */
    public function acceptJoinedTrip(Request $request, Joined $joined): Response
    {
        if($joined->getStatus() != "Accepted")
        {
            $joined->setStatus('Accepted');
            $em = $this->getDoctrine()->getManager();
            $em->persist($joined);
            $em->flush();
        }
        return $this->redirectToRoute('joined_index');
    }

    /**
     * @Route("/decline/{id}", name="decline_joined")
     */
    public function declineJoinedTrip(Request $request, Joined $joined): Response
    {
        if($joined->getStatus() != "Declined")
        {
            $joined->setStatus('Declined');
            $em = $this->getDoctrine()->getManager();
            $em->persist($joined);
            $em->flush();
        }
        return $this->redirectToRoute('joined_index');
    }


}
