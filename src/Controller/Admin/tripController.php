<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Image;
use App\Entity\Admin\trip;
use App\Form\Admin\tripType;
use App\Repository\Admin\ImageRepository;
use App\Repository\Admin\tripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("")
 */
class tripController extends AbstractController
{
    /**
     * @Route("admin/trip", name="admin_trip_index", methods={"GET"})
     */
    public function index(tripRepository $tripRepository): Response
    {
        return $this->render('admin/admin/trip/index.html.twig', [
            'trips' => $tripRepository->findAll(),
        ]);
    }

    /**
     * @Route("admin/trip/new", name="admin_trip_new", methods={"GET","POST"})
     */
    public function new(Request $request, ImageRepository $imageRepository): Response
    {
        $trip = new trip();
        $form = $this->createForm(tripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $file = $request->files->get('trip')['image'];
            $em = $this->getDoctrine()->getManager();
            $image = new Image();
            if($file)
            {
                $fileName = uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    $image->setImage($fileName);
                    $em->persist($image);
                    $trip->setImage($image);
                    $em->persist($trip);
                    $image->setTrip($trip);
                    $em->persist($image);
                }catch (FileException $e)
                {
                    dump("File Error");
                    die();
                }
            }
            $trip->setPublisher($this->getUser());
            $trip->setCreatedAt(new \DateTime());
            $trip->setUpdatedAt(new \DateTime());
            $em->persist($trip);
            $em->flush();
            return $this->redirectToRoute('admin_trip_index');
        }

        return $this->render('admin/admin/trip/new.html.twig', [
            'trip' => $trip,
            'form' => $form->createView(),
            'images' => $imageRepository->findAll(),
        ]);
    }

    /**
     * @Route("admin/trip/{id}", name="admin_trip_show", methods={"GET"})
     */
    public function show(trip $trip): Response
    {
        return $this->render('admin/admin/trip/show.html.twig', [
            'trip' => $trip,
        ]);
    }

    /**
     * @Route("admin/trip/{id}/edit", name="admin_trip_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, trip $trip, ImageRepository $imageRepository): Response
    {
        $form = $this->createForm(tripType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $request->files->get('trip')['image'];
            $em = $this->getDoctrine()->getManager();
            $image = new Image();
            if($file)
            {
                $fileName = uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    $image->setImage($fileName);
                    $em->persist($image);
                    $trip->setImage($image);
                    $em->persist($trip);
                    $image->setTrip($trip);
                    $em->persist($image);
                }catch (FileException $e)
                {
                    dump("File Error");
                    die();
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_trip_index');
        }

        return $this->render('admin/admin/trip/edit.html.twig', [
            'trip' => $trip,
            'form' => $form->createView(),
            'images' => $imageRepository->findAll(),
        ]);
    }


    /**
     * @Route("admin/trip/{id}/iupdate", name="admin_trip_iupdate", methods={"POST"})
     * @param Request $request
     * @param $id
     * @param trip $trip
     * @return Response
     */
    public function iupdate(Request $request, $id, trip $trip): Response
    {
        $tempTrip = new $trip();
        $form = $this->createForm(tripType::class, $tempTrip);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if($form->isSubmitted())
        {
            $file = $form['image']->getData();
            if($file)
            {

                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    $trip->setImage($fileName);
                    $em->persist($trip);
                    $em->flush();
                    return $this->redirectToRoute('admin_trip_index',['id'=>$trip->getId()] );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

            }

        }
        else
        {
            dump("Form Didnt Submitted");
            die();
        }

    }

    /**
     * @Route("admin/trip/{id}", name="admin_trip_delete", methods={"DELETE"})
     */
    public function delete(Request $request, trip $trip): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_trip_index');
    }


}
