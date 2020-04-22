<?php

namespace App\Controller;

use App\Entity\Admin\Image;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param UserAuthenticator $authenticator
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $file = $request->files->get('image')['image'];
            $image = new Image();
            $image->setStatus("True");
            $em = $this->getDoctrine()->getManager();
            if($file)
            {
                $fileName = uniqid().$user->getName().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    $image->setImage($fileName);
                    $em->persist($image);
                    $user->setImage($image);
                    $user->setStatus('true');
                }catch (FileException $e)
                {
                    dump('Error');
                    die();
                }
            }
            $em->persist($user);
            $em->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/adminregister.html.twig', [
            'registrationForm' => $form->createView(),
            'title' => 'Regsiter | Travel Site',
        ]);
    }
}
