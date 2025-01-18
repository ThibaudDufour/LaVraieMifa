<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Drive;
use App\Form\DriveFormType;
use App\Repository\DriveRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/drive')]
class DriveController extends AbstractController
{
    /**
     * Affiche la page principale du drive
     * @param Request $resquest
     * @param UserInterface $user
     * @param DriveRepository $driveRepository
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     */
    #[Route('/', name: 'app_drive', methods : ['GET','POST'])]
    public function index(Request $request, ?UserInterface $user, DriveRepository $driveRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $formulaire = false;

        // Gère le formulaire si l'utilisateur est connecté
        if($user){

            // Créer le formulaire
            $file = new Drive();
            $form = $this->createForm(DriveFormType::class, $file);
            $form->handleRequest($request);

            $formulaire = $form->createView();
            
            // Vérifie l'état du formulaire
            if($form->isSubmitted() && $form->isValid())
            {
                // Récupère la donnée dans le champs "name"
                $nameFile = $form->get('name')->getData();

                //Remplace tous les ',",(,),espace pour éviter toutes interférence avec le système
                $nameFilePath = $slugger->slug($nameFile);

                // Récupère le fichier
                $pathFile = $form->get('file')->getData();
                $originalFilename = pathinfo($pathFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                // Nouveau nom pour le fichier
                // Evite la duplication de nom dans nos fichiers
                $newFilename = "/files/".$nameFilePath.'-'.uniqid().'.'.$pathFile->getClientOriginalExtension();

                $extension = strtolower($pathFile->getClientOriginalExtension());
                $size = number_format($pathFile->getSize()/1048576,2);

                // Move the file to the directory where brochures are stored
                try {
                    $pathFile->move(
                        $this->getParameter('pathFile_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // Sélectionne l'icône adapté pour le fichier
                if($extension == "zip" || $extension == "rar")
                {
                    $picture = "/images/logo/archive-folder.png";
                }
                else if($extension == "jpeg" || $extension == "png" || $extension == "jpg" || $extension == "gif" || $extension == "tif" || $extension == "psd")
                {
                    $picture = "/images/logo/picture.png";
                }
                else if($extension == "mp3" || $extension == "wav" || $extension == "ogg" || $extension == "wma" || $extension == "mid")
                {
                    $picture = "/images/logo/audio-file.png";
                }
                else if($extension== "pdf")
                {
                    $picture= "/images/logo/icone_pdf.png";
                }
                else if($extension == "mp4" || $extension == "mov" || $extension == "avi" || $extension == "wmf" || $extension == "flv")
                {
                    $picture= "/images/logo/video.png";
                }
                else if($extension== "c" || $extension== "cpp" || $extension== "csharp" || $extension== "html" || $extension== "css" || $extension== "scss" || $extension== "js" || $extension== "java" || $extension== "sql" || $extension== "php" || $extension== "py" || $extension== "xml" || $extension== "bat" || $extension== "json")
                {
                    $picture= "/images/logo/code-file.png";
                }
                else if($extension == "docx" || $extension == "doc" || $extension == "odt")
                {
                    $picture= "/images/logo/ms-word.png";
                }
                else if($extension == "pptx" || $extension == "ppt" || $extension == "odp" || $extension == "odg")
                {
                    $picture= "/images/logo/ms-powerpoint.png";
                }
                else if($extension == "xlsx" || $extension == "xls" || $extension == "ods")
                {
                    $picture= "/images/logo/ms-excel.png";
                }
                else if($extension == "txt" || $extension == "rtf")
                {
                    $picture= "/images/logo/text.png";
                }
                else {
                    $picture = "/images/logo/view-file.png";
                }

                // Prépare la requête pour stocker les données dans la BDD
                $file->setUser($user);
                $file->setPathFile($newFilename);
                $file->setName($nameFile);
                $file->setDate(new \DateTime());
                $file->setExtension($extension);
                $file->setSize($size);
                $file->setPathImg($picture);

                // Execute la requête
                $entityManager->persist($file);
                $entityManager->flush($file);

                // Ajoute un coockie sur le navigateur
                $request->getSession()->set('addFile', true);

                return $this->redirectToRoute('app_drive');
            }
        }    

        return $this->render('drive/index.html.twig', [
            'driveFile' => $driveRepository->findBy([], ['id' => 'DESC']),
            'driveForm' => $formulaire
        ]);    
    }

    /**
     * Supprime un fichier du drive
     * @param int $id
     * @param DriveRepository $driveRepository
     * @param EntityManagerInterface entityManager
     * @param UserInterface $user
     */
    #[Route('/delete/{id}', name: 'app_delete_drive')]
    public function delete($id, DriveRepository $driveRepository, EntityManagerInterface $entityManager, ?UserInterface $user): Response
    {
        // Trouve le fichier à supprimer avec son id
        $driveFile = $driveRepository->find($id);

        // Vérifie que l'utilisateur soit connecter
        if($user && ($user == $driveFile->getUser())){

            // Supprime le fichier
            $deleteFile = $driveFile->getPathFile();
            $filesystem = new Filesystem();
            $filesystem->remove('/var/www/lavraiemifa/public'.$deleteFile);

            $entityManager->remove($driveFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_drive');
    }
}
