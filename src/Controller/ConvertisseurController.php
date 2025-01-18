<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Form\ConvertisseurFormType;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\Filesystem\Filesystem;

#[Route('/convertisseur')]
class ConvertisseurController extends AbstractController
{
    /**
     * Affiche le page principale du convertisseur de fichier
     * @param Request $request
     * @param SluggerInterface $slugger
     */
    #[Route('/', name: 'convertisseur')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        // Créer le formulaire
        $form = $this->createForm(ConvertisseurFormType::class);
        $form->handleRequest($request);

        // Vérifie si le formulaire soit bien envoyé et que le formualire soit valide
        if ($form->isSubmitted() && $form->isValid()) {

            $typeOfFile = $form->get('typeoffile')->getData();

            // Vérifie l'option sélectionné
            if($typeOfFile != '')
            {
                // Récupère le fichier
                $pathFile = $form->get('file')->getData();
                $originalFilename = pathinfo($pathFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);

                // Nouveau nom pour le fichier
                // Evite la duplication de nom dans nos fichiers
                $nameFile = $safeFilename.'-'.uniqid();
                $newFilename = "/files/convertisseur/".$nameFile.'.'.$pathFile->getClientOriginalExtension();

                $originalExtension = $pathFile->getClientOriginalExtension();

                // Déplace le fichier dans le répertoire souhaité
                try {
                    $pathFile->move(
                        $this->getParameter('pathFileConvertisseur_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                
                // Change les droits d'accès sur les fichiers stockés dans le répertoire
                // LibreOffice pourra donc ouvrir le fichier pour le convertir
                shell_exec('chmod 0777 -R /var/www/lavraiemifa/public/files/convertisseur');
                
                /**
                 * --- Exemple de commande bash pour convertir un fichier ---
                 * libreoffice --convert-to pdf --outdir <chemin_dest> 'file'.ext
                 * libreoffice --headless --convert-to pdf /var/www/lavraiemifa'.$newFilename.' --outdir /var/www/lavraiemifa/files/convertisseur/
                 * soffice --convert-to pdf --outdir /var/www/lavraiemifa/public/files/convertisseur/ /var/www/lavraiemifa/public/files/convertisseur/document-de-rma.docx
                 * $output = shell_exec('export HOME=/tmp/ && soffice --headless --infilter="writer_pdf_import" --convert-to docx --outdir /var/www/lavraiemifa/public/files/convertisseur/ /var/www/lavraiemifa/public'.$newFilename);
                 */
                
                // Variable pour la commande à exécuter 
                $outputDir = '/var/www/lavraiemifa/public/files/convertisseur/';
                $filePath = '/var/www/lavraiemifa/public'.$newFilename;

                // Chemin du fichier converti
                $pathFileConvert = "/files/convertisseur/".$nameFile.'.'.$typeOfFile;

                /**
                 * Convertion d'un fichier vers PDF ou inversement
                 */
                if($originalExtension == "pdf")
                {
                    // Détermine le type de document
                    switch($typeOfFile){
                        case "docx":
                        case "odt":
                            // Ici, le fichier est de type document writer
                            $optionInfilter = '--infilter="writer_pdf_import"';
                            break;

                        case "pptx":
                        case "odp":
                                $optionInfilter = '--infilter="impress_pdf_import"';
                                break;

                        case "xlsx":
                        case "ods":
                                $optionInfilter = '--infilter="calc_pdf_import"';
                                break;

                        default:
                            $optionInfilter = '';
                    }

                    // Détermine l'export de document
                    switch($originalExtension){
                        case "docx":
                        case "odt":
                            $optionPDF = ":writer_pdf_Export";
                            break;
                        
                        case "pptx":
                        case "odp":
                            $optionPDF = ":impress_pdf_Export";
                            break;

                        case "xlsx":
                        case "ods":
                            $optionPDF = ":calc_pdf_Export";
                            break;
                        
                        default:
                            $optionPDF = "";
                    }
                }
                // Ici, aucun filtre est nécessaire, si le fichier original / souhaité n'est pas un PDF
                else{
                    $optionInfilter = "";
                    $optionPDF = "";
                }
                
                // Exécute la commande pour convertir le fichier
                $output = shell_exec('export HOME=/tmp/ && soffice --headless '.$optionInfilter.' --convert-to '.$typeOfFile.''.$optionPDF.' --outdir '.$outputDir.' '.$filePath);

                // Supprime le fichier original
                $filesystem = new Filesystem();
                $filesystem->remove('/var/www/lavraiemifa/public'.$newFilename);

                // S'il n'y a pas d'erreur, le fichier est envoyé en téléchragement 
                if($output != null)
                {
                    // Télécharge le fichier converti
                    try{
                        $response = new BinaryFileResponse('/var/www/lavraiemifa/public'.$pathFileConvert);
                        $response->setContentDisposition(
                            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                            $nameFile.'.'.$typeOfFile
                        );

                        // Supprime le fichier converti
                        $filesystem = new Filesystem();
                        $filesystem->remove('/var/www/lavraiemifa/public'.$pathFileConvert);

                        $response->send();

                    } catch(\Exception $e) {
                        // Ajoute un coockie sur le navigateur s'il y a eu une erreur
                        $request->getSession()->set('convertFile', true);

                        return $this->render('convertisseur/index.html.twig', [
                            'formConvertisseur' => $form->createView(),
                        ]);
                    }
                }
                else{
                    // Ajoute un coockie sur le navigateur s'il y a eu une erreur
                    $request->getSession()->set('convertFile', true);

                    return $this->render('convertisseur/index.html.twig', [
                        'formConvertisseur' => $form->createView(),
                    ]);
                }
            }
            else {
                 // Ajoute un coockie sur le navigateur s'il manque le champs "convertir vers"
                $request->getSession()->set('missChamps', true);

                return $this->render('convertisseur/index.html.twig', [
                    'formConvertisseur' => $form->createView(),
                ]);
            }
        }

        // Envoie le formulaire sur la page WEB
        return $this->render('convertisseur/index.html.twig', [
            'formConvertisseur' => $form->createView(),
        ]);
    }
}
