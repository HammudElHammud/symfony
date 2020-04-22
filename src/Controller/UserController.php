<?php

namespace App\Controller;

use App\Entity\Admin\Image;
use App\Entity\Admin\trip;
use App\Entity\Comment;
use App\Entity\Joined;
use App\Entity\Saved;
use App\Entity\Setting;
use App\Entity\User;
use App\Form\Admin\ImageType;
use App\Form\Admin\tripType;
use App\Form\CommentType;
use App\Form\UserType;
use App\Repository\Admin\ImageRepository;
use App\Repository\Admin\tripRepository;
use App\Repository\CommentRepository;
use App\Repository\JoinedRepository;
use App\Repository\SavedRepository;
use App\Repository\SettingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user-profile", methods={"GET", "POST"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository,SettingRepository $settingRepository,  Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $fuser = new User();
        $setting = $settingRepository->findAll()[0];
        $form = $this->createForm(UserType::class, $fuser);
        $form->handleRequest($request);
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if($form->isSubmitted() && $request->query->get('task') == 'username')
        {
            if($fuser->getName() != $this->getUser()->getName()){

                $user->setName($fuser->getName());
                $em->persist($user);
                $em->flush();
            }
        }
        if($form->isSubmitted() && $request->query->get('task') == 'password')
        {   if($request->request->get('user')['password'] == $request->request->get('repassword'))
            {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
            }
            $em->persist($user);
            $em->flush();
        }

        return $this->render('home/User/user-index.html.twig', [
            'title' => 'Your Profile | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
        ]);
    }

    /**
     * @Route("/comments", name="user-comment", methods={"GET"})
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function comment(CommentRepository $commentRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        $comments = $commentRepository->findBy(['commented_by' => $this->getUser()]);
        return $this->render('home/User/user-comment.html.twig', [
            'comments' => $comments,
            'title' => 'Your Comments | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
        ]);
    }

    /**
     * @Route("/comments/{id}", name="user-profile-comment-edit", methods={"GET", "POST"})
     * @param Comment $comment
     * @param Request $request
     * @return Response
     */
    public function editComment(Comment $comment, Request $request, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('user-comment');
        }
        return $this->render('home/user/user-comment-edit.html.twig', [
            'title' => $comment->getSubject(),
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
            '$comment' => $comment,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/addtosaved/{id}", name="add-to-saved-trip", methods={"GET", "POST"})
     * @param Request $request
     * @param trip $trip
     * @param SavedRepository $savedRepository
     * @return RedirectResponse
     * @throws \Exception
     */
    public function addToSaved(Request $request, trip $trip, SavedRepository $savedRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        $saved = new Saved();
        $saved->setAddedAt(new \DateTime());
        $saved->setUser($this->getUser());
        $saved->setTrip($trip);

        if($savedRepository->findBy(['trip' => $trip, 'user' => $this->getUser()]) == null)
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($saved);
            $em->flush();
            $this->addFlash('success-added-saved', 'This trip have been added to your saved list.');
        }else{
            $this->addFlash('error-added-saved', 'This trip have already been in your saved list.');
        }
        return $this->redirectToRoute('trip_detail', ['id' => $trip->getId()]);
    }


    /**
     * @Route("/saved-list", name="user-saved-list", methods={"GET"})
     * @param SavedRepository $savedRepository
     * @return Response
     */
    public function savedList(SavedRepository $savedRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        $saveds = $savedRepository->findBy(['user' => $this->getUser()]);
        return $this->render('home/User/user-saved-list.html.twig', [
            'title' => 'Your Saved Trips List | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
            'saveds' => $saveds,
        ]);
    }

    /**
     * @Route("/saved-list/delete/{id}", name="delete-saved-list", methods={"DELETE"})
     * @param Request $request
     * @param Saved $saved
     * @return Response
     */
    public function deleteSavedItem(Request $request, Saved $saved, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        if ($this->isCsrfTokenValid('delete'.$saved->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($saved);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user-saved-list');
    }

    /**
     * @Route("/addtojoined/{id}", name="add-to-joined-trip", methods={"GET", "POST"})
     * @param Request $request
     * @param trip $trip
     * @param SavedRepository $savedRepository
     * @return RedirectResponse
     * @throws \Exception
     */
    public function addToJoined(Request $request, trip $trip, JoinedRepository $joinedRepository, SavedRepository $savedRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        $joined = new Joined();
        $joined->setTrip($trip);
        $joined->setAddedAt(new \DateTime());
        $joined->setUser($this->getUser());
        $joined->setStatus('Pending');

        if($joinedRepository->findBy(['trip' => $trip, 'user' => $this->getUser()]) == null)
        {   $saved = $savedRepository->findOneBy(['trip' => $trip, 'user' => $this->getUser()]);
            $em = $this->getDoctrine()->getManager();

            $em->persist($joined);
            if($saved != null)
            {
                $em->remove($saved);
                $em->flush();
                return $this->redirectToRoute('user-joined-list');
            }
            $em->flush();
            $this->addFlash('success-added-joined', 'This trip have been added to your joined list.');
        }else{
            $this->addFlash('error-added-joined', 'This trip have already been in your joined list.');
        }
        return $this->redirectToRoute('trip_detail', ['id' => $trip->getId()]);
    }

    /**
     * @Route("/joined-list", name="user-joined-list", methods={"GET"})
     * @param JoinedRepository $joinedRepository
     * @return Response
     */
    public function joinedList(JoinedRepository $joinedRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        $joineds = $joinedRepository->findBy(['user' => $this->getUser()]);
        return $this->render('home/User/user-joined-trips.html.twig', [
            'title' => 'Your Joined Trips List | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
            'joineds' => $joineds,
        ]);
    }


    /**
     * @Route("/joined-list/delete/{id}", name="delete-joined-list", methods={"DELETE"})
     * @param Request $request
     * @param Joined $joined
     * @return Response
     */
    public function deleteJoinedItem(Request $request, Joined $joined, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        if ($this->isCsrfTokenValid('delete'.$joined->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($joined);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user-joined-list');
    }

    /**
     * @Route("/{id}/iedit", name="upload_profile", methods={"GET","POST"})
     */
    public function uploadProfile(Request $request, $id, User $user, ImageRepository $imageRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() ) {
            $file = $request->files->get('image')['image'];
            if($user->getImage() != null)
            {
                if(file_exists($this->getParameter('images_directory') . '/'.$user->getImage()->getImage()))
                    unlink($this->getParameter('images_directory') . '/'.$user->getImage()->getImage());
            }
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
                    $user->setImage($image);
                    $em->persist($user);
                    $em->flush();

                    return $this->redirectToRoute('upload_profile',['id'=>$user->getId()] );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            }
        }

        return $this->render('home/User/upload-user-profile.html.twig', [
            'title' => 'Upload Image for Trip | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
            'user' => $user,
            'id' => $id,
            'form' => $form->createView(),
            'images' => $user->getImage(),
        ]);
    }

    /**
     * @Route("/my-trip", name="user_trip_index", methods={"GET"})
     */
    public function mytrips(tripRepository $tripRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        return $this->render('home/User/user-my-trip.html.twig', [
            'title' => 'Your Trips List | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
            'trips' => $tripRepository->findBy(['publisher' => $this->getUser()]),
        ]);
    }

    /**
     * @Route("my-trip/new", name="user_my_trip_new", methods={"GET","POST"})
     * @param Request $request
     * @param ImageRepository $imageRepository
     * @return Response
     * @throws \Exception
     */
    public function tripnew(Request $request, ImageRepository $imageRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
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
            return $this->redirectToRoute('user_trip_index');
        }

        return $this->render('home/User/user-new-my-trip.html.twig', [
            'title' => $trip->getTitle(),
            'keywords' => $trip->getKeywords(),
            'description' => $trip->getDescription(),
            'trip' => $trip,
            'form' => $form->createView(),
            'images' => $imageRepository->findAll(),
        ]);
    }

    /**
     * @Route("my-trip/{id}", name="user_my_trip_show", methods={"GET"})
     */
    public function show(trip $trip, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        return $this->render('home/User/user-my-trip-show.html.twig', [
            'trip' => $trip,
        ]);
    }

    /**
     * @Route("my-trip/{id}/edit", name="user_my_trip_edit", methods={"GET","POST"})
     */
    public function tripedit(Request $request, trip $trip, ImageRepository $imageRepository, SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
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

            return $this->redirectToRoute('user_trip_index');
        }

        return $this->render('home/User/user-my-trip-edit.html.twig', [
            'title' => $trip->getTitle(),
            'keywords' => $trip->getKeywords(),
            'description' => $trip->getDescription(),
            'trip' => $trip,
            'form' => $form->createView(),
            'images' => $imageRepository->findAll(),
        ]);
    }


    /**
     * @Route("/my-trip/{id}", name="user_trip_delete", methods={"DELETE"})
     */
    public function tripdelete(Request $request, trip $trip): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trip->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_trip_index');
    }
}
