<?php

namespace App\Controller;

use App\Entity\Admin\Message;
use App\Entity\Admin\trip;
use App\Entity\Comment;
use App\Form\Admin\MessageType;
use App\Form\CommentType;
use App\Repository\Admin\ImageRepository;
use App\Repository\Admin\tripRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\SettingRepository;
use App\Repository\SliderRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param SettingRepository $settingRepository
     * @param ImageRepository $imageRepository
     * @param SliderRepository $sliderRepository
     * @param tripRepository $tripRepository
     * @return Response
     */
    public function index(SettingRepository $settingRepository, ImageRepository $imageRepository, SliderRepository $sliderRepository, tripRepository $tripRepository)
    {

        /** @var TYPE_NAME $data */
        $setting = $settingRepository->findAll()[0];
//        $em = $this->getDoctrine()->getManager();
//        $silder = $statement->fetchAll();
//        dump($silder);
//        die();
        return $this->render('home/index.html.twig', [
            'title' => 'Trip | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
            'setting' => $setting,
            'trips' => $tripRepository->findBy(['status' => 'Published',], ['updated_at' => 'DESC']),
        ]);
    }

    /**
     * @Route("/aboutus", name="aboutus")
     * @param SettingRepository $settingRepository
     * @return Response
     */
    public  function aboutus( SettingRepository $settingRepository)
    {
        $setting = $settingRepository->findAll()[0];
        return $this->render('home/aboutus/aboutus.html.twig',[
            'title' => 'About Us | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
            'setting' => $setting,
    ]);

    }

    /**
     * @Route("/contantus", name="contantus", methods={"GET","POST"})
     * @param Request $request
     * @param SettingRepository $settingRepository
     * @param SliderRepository $sliderRepository
     * @param tripRepository $tripRepository
     * @return Response
     * @throws \Exception
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function contantus(Request $request, SettingRepository $settingRepository,SliderRepository $sliderRepository , tripRepository $tripRepository): Response
    {
        $setting = $settingRepository->findAll()[0];
        $data = $settingRepository->findAll();
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $message->setUpdatedAt(new DateTime());
            $message->setCreatedAt(new DateTime());
            $message->setIp($_SERVER['REMOTE_ADDR']);
            $message->setStatus("New");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            $email = (new Email())
                ->from($setting->getEmail())
                ->to($message->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('You receive your message!')
                ->html('<p>Dear'. $message->getName() .',</p>'.
                        '<p>We successfully receive your message to us and thank you so much for sending to us</p>');

            /** @var Symfony\Component\Mailer\SentMessage $sentEmail */
            $transport = new GmailTransport($setting->getSmtpemail(), $setting->getSmtppassword());
            $mailer =  new Mailer($transport);
            $sentEmail = $mailer->send($email);
            // $messageId = $sentEmail->getMessageId();

            $this->addFlash('success', 'Messsage send');

            return $this->redirectToRoute('contantus');
        }

        return $this->render('home/contantus/contantus.html.twig', [
            'title' => 'Contact Us | Travel Site',
            'keywords' => $setting->getKeywords(),
            'description' => $setting->getDescription(),
            'message' => $message,
            'form' => $form->createView(),
            'data'=>$data,
            'setting' => $setting,
        ]);
    }


    /**
     * @Route("/trip/{id}", name="trip_detail", methods={"POST", "GET"})
     * @param Request $request
     * @param trip $trip
     * @param CategoryRepository $categoryRepository
     * @param CommentRepository $commentRepository
     * @return Response
     * @throws \Exception
     */
    public function tripDetail(Request $request, trip $trip, ImageRepository $imageRepository, CategoryRepository $categoryRepository, CommentRepository $commentRepository )
    {
        $category = $categoryRepository->findAll();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $comment->setOnTrip($trip);
            $comment->setIp($_SERVER['REMOTE_ADDR']);
            $comment->setCommentedAt(new DateTime());
            $comment->setUpdatedAt(new DateTime());
            $comment->setCommentedBy($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }
        return $this->render('home/TripDetail/TripDetail-index.html.twig',
            [
                'title' => $trip->getTitle(),
                'keywords' => $trip->getKeywords(),
                'description' => $trip->getDescription(),
                'trip' => $trip,
                'category' => $category,
                'comments' => $commentRepository->findBy(['on_trip' => $trip->getId()], ['commented_at' => 'ASC']),
                'images' => $imageRepository->findBy(['trip' => $trip]),

            ]);
    }






}
