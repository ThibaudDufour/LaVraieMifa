<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use App\Repository\UserRepository;
use App\Repository\TracaMailAdminRepository;
use App\Repository\ScoreUserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\AdminMailFormType;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use App\Entity\TracaMailAdmin;

#[Route('/admin')]
class AdminController extends AbstractController
{
    /**
     * Route principale pour afficher la page
     *
     * @param UserInterface|null $user
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     */
    #[Route('/', name: 'app_admin', methods : ['GET','POST'])]
    public function index(?UserInterface $user, EntityManagerInterface $entityManager, UserRepository $userRepository, TracaMailAdminRepository $tracaMailAdminRepository, Request $request, MailerInterface $mailer): Response
    {
        // Information du serveur : Son nom et le nombre de coeurs
        $serveur = shell_exec("cat /proc/cpuinfo | grep -i \"Model\" | awk -F\": \" '{print $2}'");
        $core = shell_exec("cat /proc/cpuinfo | grep -i '^processor' | wc -l");

        exec('sudo crontab -l', $jobs);
        // Récupère @IP de l'utilisateur
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        // Créer le formulaire
        $form = $this->createForm(AdminMailFormType::class);
        $form->handleRequest($request);

        // Récupération des ip bannies
        exec('sudo fail2ban-client status sshd', $log);

        if(count($log) == 0) {
            $ipExplode = [];
        }
        else if(count(explode("\t", $log[count($log)-1])) == 1){
            //output data by json
            $ipExplode = [];
        } 
        else {
            $ipExplode = explode(" ", explode("\t", $log[count($log)-1])[1]);
        }
        
        // Vérifie que le fomurlaire a été envoyé et est valide
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupère les données du formulaire
            $objet = $form->get('objet')->getData();
            $message = $form->get('message')->getData();

            // Envoie un mail à tous les utilisateurs inscrits
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
                    ->to($mailUser) // $mailUser
                    ->subject($objet)
                    ->htmlTemplate('admin/email.html.twig')
                    ->context([
                        'message' => $message,
                        'name' => $nameUser,
                        'firstname' => $firstnameUser
                    ]);

                    $mailer->send($email);
                }
            }

            // Enregistrement du mail pour la traça
            $tracamail = new TracaMailAdmin();
            $tracamail->setObjet($objet);
            $tracamail->setMessage($message);
            $tracamail->setUser($user);

            // Execute la requête
            $entityManager->persist($tracamail);
            $entityManager->flush($tracamail);

            $request->getSession()->set('mail_termine', true);
            
            // Redirige l'utilisateur sur la page principale 
            return $this->redirectToRoute('app_admin');
            
        } else {
            return $this->render('admin/index.html.twig', [
                'adresseIP' => $ip,
                'formMail' => $form->createView(),
                'jobs' => $jobs,
                'serveur' => $serveur,
                'core' => $core,
                "fail2ban" => $ipExplode,
            ]);
        }
    }

    /**
     * API - Récupère les données du serveur (CPU, HDD, RAM, Réseau)
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/infosysteme', name: 'app_admin_infosystem', methods : ['POST'])]
    public function infoSystem(Request $request, UserRepository $userRepository)
    {
        $userMail = $request->request->get("data");
        if($userMail != ""){
            $findUser = $userRepository->findBy(['email' => $userMail]);

            if($findUser != [] && in_array('ROLE_ADMIN', $findUser[0]->getRoles())) {
                //cpu stat
                $prevVal = shell_exec("cat /proc/stat");
                $prevArr = explode(' ',trim($prevVal));
                $prevTotal = $prevArr[2] + $prevArr[3] + $prevArr[4] + $prevArr[5];
                $prevIdle = $prevArr[5];
                usleep(0.15 * 1000000);
                $val = shell_exec("cat /proc/stat");
                $arr = explode(' ', trim($val));
                $total = $arr[2] + $arr[3] + $arr[4] + $arr[5];
                $idle = $arr[5];
                $intervalTotal = intval($total - $prevTotal);
                $stat['cpu'] =  intval(100 * (($intervalTotal - ($idle - $prevIdle)) / $intervalTotal));

                //memory stat
                $stat['mem_percent'] = round(shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'"), 2);
                $mem_result = shell_exec("free | grep Mem | awk '{print $2}'");
                $stat['mem_total'] = round($mem_result / 1024 / 1024, 2);
                $mem_result = shell_exec("free | grep Mem | awk '{print $3}'");
                $stat['mem_used'] = round($mem_result / 1024 / 1024, 2);

                //hdd stat
                $stat['hdd_free'] = round(disk_free_space("/") / 1024 / 1024 / 1024, 2);
                $stat['hdd_total'] = round(disk_total_space("/") / 1024 / 1024/ 1024, 2);
                $stat['hdd_used'] = $stat['hdd_total'] - $stat['hdd_free'];
                $stat['hdd_percent'] = round(sprintf('%.2f',($stat['hdd_used'] / $stat['hdd_total']) * 100), 2);
                $stat['hdd_used'] = round(sprintf('%.2f',($stat['hdd_used'])), 2);

                //USB stat
                $peripherique = "/dev/sdb1";
                $pointMontage = "/mnt/nas";
                if(file_exists($pointMontage)) //peripherique
                {
                    $stat['usb_free'] = round(disk_free_space($pointMontage) / 1024 / 1024 / 1024, 2);
                    $stat['usb_total'] = round(disk_total_space($pointMontage) / 1024 / 1024/ 1024, 2);
                    $stat['usb_used'] = $stat['usb_total'] - $stat['usb_free'];
                    $stat['usb_percent'] = round(sprintf('%.2f',($stat['usb_used'] / $stat['usb_total']) * 100), 2);
                    $stat['usb_used'] = round(sprintf('%.2f',($stat['usb_used'])), 2);
                }
                else {
                    $stat['usb_free'] = "";
                    $stat['usb_total'] = "";
                    $stat['usb_percent'] = "";
                    $stat['usb_used'] = "";
                }

                //network stat
                $stat['network_rx'] = round(trim(file_get_contents("/sys/class/net/eth0/statistics/rx_bytes")) / 1024/ 1024/ 1024, 2);
                $stat['network_tx'] = round(trim(file_get_contents("/sys/class/net/eth0/statistics/tx_bytes")) / 1024/ 1024/ 1024, 2);

                //output data by json
                return new JsonResponse([
                    'code' => 200,
                    'cpu' => $stat['cpu'],
                    'mem_percent' => $stat['mem_percent'],
                    'mem_total' =>  $stat['mem_total'],
                    'mem_used' => $stat['mem_used'],
                    'hdd_free' => $stat['hdd_free'],
                    'hdd_total' => $stat['hdd_total'],
                    'hdd_used' => $stat['hdd_used'],
                    'hdd_percent' => $stat['hdd_percent'],
                    'usb_free' => $stat['usb_free'],
                    'usb_total' => $stat['usb_total'],
                    'usb_used' => $stat['usb_used'],
                    'usb_percent' => $stat['usb_percent'],
                    'network_rx' => $stat['network_rx'],
                    'network_tx' => $stat['network_tx']
                ], 200);
            }
            else {
                return new JsonResponse([
                    'code' => 403,
                    'msg' => "Utilisateur non reconnu ou non autorisé !"
                ], 403);
            }
        }
        else {
            return new JsonResponse([
                'code' => 403,
                'msg' => "Veuillez remplir le champs demandé"
            ], 403);
        }
    }
    
    /**
     *  API - Recherche les mises à jour systèmes
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/MaJ', name: 'app_admin_maj_info', methods : ['POST'])]
    public function MaJSystemInfo(Request $request, UserRepository $userRepository)
    {
        $listeUpdate = [];
        $userMail = $request->request->get("data");
        if($userMail != ""){
            $findUser = $userRepository->findBy(['email' => $userMail]);
   
            if($findUser != [] && in_array('ROLE_ADMIN', $findUser[0]->getRoles())) {
                // Execute la commande pour actualiser la recherche de mises à jour
                exec('sudo apt update', $updates);

                // Si nous devons faire des MAJ, nous récupérons la liste
                if(is_numeric(substr($updates[count($updates)-1],0,1)))
                {
                    exec('apt list --upgradable', $liste);
                    for($i=1; $i<count($liste); $i++)
                        $listeUpdate[$i] = explode('/', $liste[$i])[0];
                }

                //output data by json
                return new JsonResponse([
                    "code" => 200,
                    "info" => $updates[count($updates)-1], // Retourne la dernières ligne du tableau
                    "count" => count($updates), // Compte le nombre de ligne dans la table updates,
                    "liste" => $listeUpdate
                ], 200);
            }
            else {
                return new JsonResponse([
                    'code' => 403,
                    'msg' => "Utilisateur non reconnu ou non autorisé !"
                ], 403);
            }
        }
        else {
            return new JsonResponse([
                "code" => 403,
                "msg" => "Veuillez remplir le champs demandé"
            ], 403);
        }
    }

    /**
     *  API - Mise à jour des packages
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[Route('/MaJ/confirm', name: 'app_admin_maj', methods : ['POST'])]
    public function MaJSystem(Request $request, UserRepository $userRepository)
    {
        $maj = "";
        $userMail = $request->request->get("data");
        if($userMail != ""){
            $findUser = $userRepository->findBy(['email' => $userMail]);
   
            if($findUser != [] && in_array('ROLE_ADMIN', $findUser[0]->getRoles())) {
                // Execute la commande pour mettre à jour les package
                exec('apt list --upgradable', $liste);
                for($i=1; $i<count($liste); $i++)
                    $maj = $maj.explode('/', $liste[$i])[0]." ";
                
                exec('sudo apt upgrade '. $maj .'-y && echo "0" || echo "1"', $log);

                if($log[count($log)-1] == "1")
                {
                    //output data by json
                    return new JsonResponse([
                        "msg" => "Les mises à jour ont échoué",
                    ], 403);
                }
                else {
                    //output data by json
                    return new JsonResponse([
                        "msg" => "Mise(s) à jour réalisée(s) avec succès",
                    ], 200);
                }
            }
            else {
                return new JsonResponse([
                    'code' => 403,
                    'msg' => "Utilisateur non reconnu ou non autorisé !"
                ], 403);
            }
        }
        else {
            return new JsonResponse([
                "code" => 403,
                "msg" => "Veuillez remplir le champs demandé"
            ], 403);
        }
    }


    /**
     * Tableau de bord - NAS
     *
     * @param UserInterface|null $user
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/nas', name: 'app_admin_nas', methods : ['GET'])]
    public function indexNas(?UserInterface $user, EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): Response
    {
        return $this->render('admin/nas.html.twig', [

        ]);
    }


    /**
     * API - Récupère les informations du NAS
     * 
     * https://symfony.com/doc/current/http_client.html
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/infosysteme-nas', name: 'app_admin_infosystem_nas', methods : ['POST'])]
    public function infoSystemNas(Request $request, UserRepository $userRepository)
    {
        $uri = "https://192.168.1.6:4433/api/v2.0";
        $httpClient = HttpClient::create([
            'headers' => [
                'Authorization' => 'Bearer 1-gbdlU9llFY0UAW0eZKJmIj3TLvDXKwaquTyWRJUmBKd4I4vUBYAGi5K9QgPNFGrd',
                'Accept' => 'application/json',
            ],
            'verify_peer' => false,
            'verify_host' => false
        ]);

        $userMail = $request->request->get("data");
        if($userMail != ""){
            $findUser = $userRepository->findBy(['email' => $userMail]);
   
            if($findUser != [] && in_array('ROLE_ADMIN', $findUser[0]->getRoles())) {
                // IP
                try {
                    $response = $httpClient->request('GET', $uri.'/system/general');
                    if($response->getStatusCode() == 200) {
                        $content = $response->toArray();

                        $ip = $content['ui_address'][0];            
                    }
                    else {
                        $ip = "NONOK";
                    }
                } catch (TransportExceptionInterface $e) {
                    $ip = "NONOK";
                }

                // Info
                try {
                    $response = $httpClient->request('GET', $uri.'/system/info');
                    if($response->getStatusCode() == 200) {
                        $content = $response->toArray();

                        $version = $content['version'];
                        $cpumodel = $content['model'];
                        $uptime = str_replace('days', 'jours', explode('.', $content['uptime'])[0]);
                        $cores = $content["cores"];
                    } 
                    else {
                        $version = "NONOK";
                        $cpumodel ="NONOK";
                        $uptime = "NONOK";
                        $cores = "NONOK";
                    }
                } catch (TransportExceptionInterface $e) {
                    $version = "NONOK";
                    $cpumodel ="NONOK";
                    $uptime = "NONOK";
                    $cores = "NONOK";
                }
        
                // State
                try {
                    $response = $httpClient->request('GET', $uri.'/system/state');
                    if($response->getStatusCode() == 200) {
                        $state = str_replace('"', '', $response->getContent());
                    }
                    else {
                        $state = "NONOK";
                    }
                } catch (TransportExceptionInterface $e) {
                    $state = "NONOK";
                }

                // CPU
                try{
                    $response = $httpClient->request('POST', $uri.'/reporting/get_data', [
                        'body' => '
                            {
                                "graphs":[
                                    {
                                        "name":"cpu"
                                    }
                                ],
                                "reporting_query":
                                {
                                    "start":"now-20",
                                    "end":"now",
                                    "aggregate":true
                                }
                            }'
                    ]);
                    if($response->getStatusCode() == 200) {
                        $content = $response->toArray();

                        $sum = 0;
                        for($i = 0; $i < count($content[0]['aggregations']['mean'])-1; $i++)
                        {
                            $sum += $content[0]['aggregations']['mean'][$i];
                        }
                        $cpu = round(($sum/(count($content[0]['aggregations']['mean'])-1)),2);
                    }
                    else {
                        $cpu = "NONOK";
                    }
                } catch (TransportExceptionInterface $e) {
                    $cpu = "NONOK";
                }

                // RAM
                try {
                    $response = $httpClient->request('POST', $uri.'/reporting/get_data', [
                        'body' => '
                            {
                                "graphs":[
                                    {
                                        "name":"memory"
                                    }
                                ],
                                "reporting_query":
                                {
                                    "start":"now-20",
                                    "end":"now",
                                    "aggregate":true
                                }
                            }'
                    ]);
                    if($response->getStatusCode() == 200) {
                        $content = $response->toArray();

                        $sum = 0;
                        for($i = 0; $i < count($content[0]['aggregations']['mean']); $i++)
                        {
                            $sum += $content[0]['aggregations']['mean'][$i];
                        }
                        $ram = round(($sum/(count($content[0]['aggregations']['mean']))),2);
                        //dd($ram / (1024*1024*1024));
                    }
                } catch (TransportExceptionInterface $e) {}

                // HDD
                try{
                    $response = $httpClient->request('GET', $uri.'/pool');
                    if($response->getStatusCode() == 200) {
                        $content = $response->toArray();

                        $size = ($content[0]["topology"]["data"][0]["stats"]["size"]) / (1024*1024*1024);
                        $allocated = round(($content[0]["topology"]["data"][0]["stats"]["allocated"]) / (1024*1024*1024),2);
                        $online = $content[0]["topology"]["data"][0]["status"];
                        $sizepercentage = round(($allocated/$size)*100, 2);
                    }
                    else {
                        $size = "NONOK";
                        $allocated = "NONOK";
                        $online = "NONOK";
                        $sizepercentage  = "NONOK";
                    }   
                } catch (TransportExceptionInterface $e) {
                    $size = "NONOK";
                    $allocated = "NONOK";
                    $online = "NONOK";
                    $sizepercentage  = "NONOK";
                }

                return new JsonResponse([
                    "version" => $version,
                    "cpumodel" => $cpumodel,
                    "uptime" => $uptime,
                    "ip" => $ip,
                    "state" => $state,
                    "cpu" => $cpu,
                    "cores" => $cores,

                    "size" => $size,
                    "allocated" => $allocated,
                    "sizepercentage" => $sizepercentage,
                    "online" => $online,
                ], 200);
            }
            else {
                return new JsonResponse([
                    'code' => 403,
                    'msg' => "Utilisateur non reconnu ou non autorisé !"
                ], 403);
            }
        }
        else {
            return new JsonResponse([
                "code" => 403,
                "msg" => "Veuillez remplir le champs demandé"
            ], 403);
        }
    }

    /**
     * Migre les scores
     */
    
    #[Route('/migrationBDD', name: 'app_nfo_admin', methods : ['GET'])]
    public function info(UserRepository $userRepository, ScoreUserRepository $scoreUserRepository, EntityManagerInterface $entity) : Response
    {

        $scores = $scoreUserRepository->findAll();
        $array = array();

        foreach($scores as $score){
            $score->setTetris($score->getUser()->getScoreTetris());
            $score->setSnake($score->getUser()->getScoreSnake());
            $score->setGame2048($score->getUser()->getScore2048());
            $score->setFlappyBird($score->getUser()->getScoreFlappyBird());
            $score->setSpaceInvaders($score->getUser()->getScoreSpaceInvaders());
            $score->setBubbleShooter($score->getUser()->getScoreBubbleShooter());
            $score->setJurassicPark($score->getUser()->getScoreJurassicPark());
            $score->setDoodleJump($score->getUser()->getScoreDoodleJump());

            $entity->persist($score);
            $entity->flush($score);

            array_push($array, $score->getUser()->getUsername());
            
        }

        return new JsonResponse([
            "score" => $array,
        ], 200);
    }

    /**
     *  Vérifie si nous devons faire un reboot après la mise à jour
     */
    
     #[Route('/necessary-reboot', name: 'app_reboot_admin', methods : ['GET'])]
     public function rebootSystem(UserRepository $userRepository, ScoreUserRepository $scoreUserRepository, EntityManagerInterface $entity) : Response
     {        
        exec('cat /var/run/reboot-required.pkgs && echo "0" || echo "1"', $log);
        
        if($log[count($log)-1] == "1")
        {
            //output data by json
            return new JsonResponse([
                "msg" => 0,
            ], 200);
        }
        else {
            //output data by json
            return new JsonResponse([
                "msg" => 1,
            ], 200);
        }
     }

     #[Route('/fail2ban', name: 'app_fail2ban_admin', methods : ['GET'])]
     public function fail2ban(UserRepository $userRepository, ScoreUserRepository $scoreUserRepository, EntityManagerInterface $entity) : Response
     {       
        exec('sudo fail2ban-client status sshd', $log);

        if(count(explode("\t", $log[count($log)-1])) == 1){
            //output data by json
            return new JsonResponse([
                "msg" => "Aucune adresse IP est bloquée",
            ], 200);
        }
        
        $ipExplode = explode(" ", explode("\t", $log[count($log)-1])[1]);

        foreach($ipExplode as $key => $value){
            $ip[$key] = $value;
        }

        //output data by json
        return new JsonResponse([
            "msg" => $ip,
        ], 200);
     }

     #[Route('/fail2ban/unbanip', name: 'app_unban_fail2ban_admin', methods : ['POST'])]
     public function unbanFail2ban(Request $request, UserRepository $userRepository, ScoreUserRepository $scoreUserRepository, EntityManagerInterface $entity) : Response
     {  
        $ip = $request->request->get("ip");

        exec('sudo fail2ban-client set sshd unbanip '.$ip, $log);

        if($log == "1") {
            return new JsonResponse([
                "msg" => "L'adresse IP ".$ip." a été débanni !",
            ], 200);
        }
        else {
            return new JsonResponse([
                "msg" => "Aucune adresse IP n'a été trouvé",
            ], 400);
        }
     }
}
