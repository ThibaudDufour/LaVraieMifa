<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * Affiche la page d'inscription
     * 
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param UserAuthenticator $authenticator
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, MailerInterface $mailer, UserRepository $userRepository ,UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Créer le formulaire pour l'inscription
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Vérifie que le formulaire soit valide et soit envoyé
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère la photo de profil
            $profilPhotoPath = $form->get('profilPhoto')->getData();

            // Vérifie l'existence de la photo de profil
            if ($profilPhotoPath) {
                $originalFilename = pathinfo($profilPhotoPath->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = "/images/user/".$safeFilename.'-'.uniqid().'.'.$profilPhotoPath->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $profilPhotoPath->move(
                        $this->getParameter('profilPhotoPath_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // Stocke le chemin de la photo de profil
                $user->setProfilPhotoPath($newFilename);
            }
            else{
                $user->setProfilPhotoPath("-");
            }

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            //Envoie un mail aux autres utilisateurs
            $allUsers = $userRepository->findAll();
            foreach ($allUsers as $userInfo) {
                // Envoie le mail si l'utilisateur l'autorise
                if($userInfo->getReciveMail())
                {
                    $mailUser = $userInfo->getEmail();
                    $nameUser = $userInfo->getName();
                    $firstnameUser = $userInfo->getfirstname();

                    $email = (new TemplatedEmail())
                    ->from(new Address('no-reply@lavraiemifa.fr', 'La Vraie Mifa'))
                    ->to($mailUser)
                    ->subject("Un " . $user->getfirstname() . " " . $user->getName() . " sauvage apparaît !")
                    ->htmlTemplate('registration/emailArrivant.html.twig')
                    ->context([
                        'name' => $nameUser,
                        'firstname' => $firstnameUser,
                        'nameNewUser' => $user->getfirstname(),
                        'firstnameNewUser' => $user->getName(),
                        'photoPath' => $user->getProfilPhotoPath()
                    ]);

                    $mailer->send($email);
                }
            }

            // Ajoute l'utilisateur dans la BDD
            $entityManager->persist($user);
            $entityManager->flush();

            $request->getSession()->set('connexion', true);

            // Envoie un mail de confirmation au nouvel utilisateur
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@lavraiemifa.fr', 'La Vraie Mifa'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre courriel')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Vérifie le mail de l'utilisateur
     * @param Request $request
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $request->getSession()->set('mailVerifie', true);

        return $this->redirectToRoute('home');
    }
}
