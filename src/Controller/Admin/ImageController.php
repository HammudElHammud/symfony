<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Image;
use App\Entity\Admin\trip;
use App\Form\Admin\ImageType;
use App\Form\TripType;
use App\Repository\Admin\ImageRepository;
use App\Repository\Admin\tripRepository;
use http\Client\Curl\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/", name="admin_image_index", methods={"GET"})
     */
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('admin/admin/image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/trip-image-gallery/{id}", name="trip_image_gallery", methods={"GET"})
     * @param $id
     * @param Request $request
     * @param trip $trip
     * @param ImageRepository $imageRepository
     * @return Response
     */
    public function tripImage($id, Request $request,trip $trip, ImageRepository $imageRepository): Response
    {
        return $this->render('admin/admin/image/trip-image-gallery.html.twig', [
            'images' => $imageRepository->findBy(['trip' => $trip]),
            'id' => $id,
            'trip' => $trip,
        ]);
    }

    /**
     * @Route("/update-trip-image/{id}/{trip_id}", name="update_trip_image", methods={"GET"})
     * @return Response
     */
    public function updateTripImage($id, $trip_id, Request $request,Image $image, tripRepository $tripRepository, ImageRepository $imageRepository): Response
    {
        $trip = $tripRepository->findOneBy(['id' => $trip_id]);
        $trip->setImage($image);
        $em = $this->getDoctrine()->getManager();
        $em->persist($trip);
        $em->flush();

        return $this->redirectToRoute('trip_image_gallery', [
            'images' => $imageRepository->findAll(),
            'id' => $trip_id,
        ]);
    }



    /**
     * @Route("/{tid}/new", name="admin_image_new", methods={"GET","POST"})
     * @param Request $request
     * @param $tid
     * @param ImageRepository $imageResponsitry
     * @return Response
     */
    public function new(Request $request , $tid, ImageRepository  $imageResponsitry): Response
    {
        $imageListe = $imageResponsitry->findBy(['trip_id' => 'tip']);
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted()){
            dump($request);
            die();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('admin_image_index');
        }

        return $this->render('admin/admin/image/new.html.twig', [
            'image' => $image,
            'imageListe' => $imageListe,
            'tid' => $tid,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_image_show", methods={"GET"})
     */
    public function show(Image $image): Response
    {
        return $this->render('admin/admin/image/show.html.twig', [
            'image' => $image,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_image_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Image $image): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_image_index');
        }

        return $this->render('admin/admin/image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/trip-delete/{id}/{trip_id}", name="trip_image_delete", methods={"DELETE"})
     */
    public function tripImageDelete(Request $request, Image $image, $trip_id): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            unlink($this->getParameter('images_directory') . '/'.$image->getImage());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }
        return $this->redirectToRoute('trip_image_gallery', [
            'id' => $trip_id,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_image_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_image_index');
    }

    /**
     * @Route("/{id}/iedit", name="admin_trip_iedit", methods={"GET","POST"})
     * @param Request $request
     * @param $id
     * @param trip $trip
     * @return Response
     */
    public function iedit(Request $request, $id, trip $trip, ImageRepository $imageRepository): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() ) {
            $file = $form['image']->getData();
            if($file)
            {
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    $image->setImage($fileName);
                    $image->setTrip($trip);
                    $em->persist($image);
                    $trip->setImage($image);
                    $em->persist($trip);
                    $em->flush();

                    return $this->redirectToRoute('admin_trip_iedit',['id'=>$trip->getId()] );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
        }

        return $this->render('admin/admin/image/new.html.twig', [
            'trip' => $trip,
            'id' => $id,
            'form' => $form->createView(),
            'images' => $imageRepository->findBy(['trip' => $trip])
        ]);
    }







    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
