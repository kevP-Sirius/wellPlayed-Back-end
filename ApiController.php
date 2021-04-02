<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/signin", name="signin" , methods={"POST"})
     */
    public function signin(\Swift_Mailer $mailer ,EntityManagerInterface $em ,UserPasswordEncoderInterface $encoder, ?Request $request=null, RoleRepository $role,UserRepository $UserRepository)
    {
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        if($request!== null && $request->headers->get('JWT') == $API_Token )

        { 
            $usernameOrEmail = trim($request->request->get('username'));
            $password = trim($request->request->get('password'));
            
            $currentUserConnect = $UserRepository->findByNameUser($usernameOrEmail);
            
            if($currentUserConnect!=[]){

                $plainPassword = $password;
                $verify = $encoder->isPasswordValid($currentUserConnect[0], $plainPassword, $currentUserConnect[0]->getSalt());
                // si le password est valide et que le user existe on passe à la suite
                
                if($verify === true )
                {
                    $response = new Response();
                    $dir = "C:\Users\kevma\Dev\wellPlayedAPI\src\Controller\music";
                       
                      
                        
                        // Open a directory, and read its contents
                        $LoginStatus = true;
                        $musicList = [];
                        if (is_dir($dir))
                        {

                            if ($dh = opendir($dir)){
                            while (($file = readdir($dh)) !== false){
                                if(strlen($file)>0 && $file!="." && $file!=".." && $file!=","){
                                    $extensions= explode(".", $file);
                                    $filePath = $dir.'/'.$file;
                                    if($extensions[1]=="mp3"||$extensions[1]=="mp4"){
                                        
                                        $musicList[]=$file;

                                    }
                                    
                                }
                        
                            }
                            closedir($dh);
                            }
                        }       
                    $response->setContent(json_encode(
        
                        [   
                            'loginstatus' => $LoginStatus,
                            'musicList'=>$musicList
                        ]));
        
                    $response->headers->set('Content-Type', 'application/json');
                    
                    
                    
        
                    return $response ;
                }else{
                    $response = new Response();
                    $LoginStatus = false;
                    $errorDetail ="wrong id or password";
                    $response->setContent(json_encode(
    
                        [   
                            'loginstatus' => $LoginStatus,
                            'error' => $errorDetail,
                            'JWT' => $request->headers->get('JWT')
                            
                        ]));
        
                    $response->headers->set('Content-Type', 'application/json');
                    return $response ;
                }
               


            }else{
                $response = new Response();
                $LoginStatus = false;
                $errorDetail ="wrong id or password";
                $response->setContent(json_encode(

                    [   
                        'loginstatus' => $LoginStatus,
                        'error' => $errorDetail,
                        'JWT' => $request->headers->get('JWT')
                    ]));
    
                $response->headers->set('Content-Type', 'application/json');
                return $response ;
            }
           
        }else{
            
            $errorDetail ="missing or incorrect Header JWT";
            $response = new Response();
            $response->setContent(json_encode(

                [   
                    'error' => $errorDetail,
                    'JWT' => $request->headers->get('JWT')
                    
                ]));

            $response->headers->set('Content-Type', 'application/json');
                   
            return $response ;
        }
  
    }
    /**
     * @Route("/api/readfile/{filename}", name="readfile" , methods={"GET"})
     */
    public function readfile(?Request $request=null, string $filename)
    {
        $API_Token='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.NFCEbEEiI7zUxDU2Hj0YB71fQVT8YiQBGQWEyxWG0po';
                
        
            
            $dir = "C:\Users\kevma\Dev\wellPlayedAPI\src\Controller\music";
           
            $filePath = $dir.'/'.$filename;
            if(!empty($filename) && file_exists($filePath))
            {
                
                // Define headers               
                // Read the file               
                $response = new Response();
                $response->headers->set('Content-type', 'application/octet-stream');
                $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
                $response->setContent(file_get_contents($filePath));
                return $response;
            }
        
        
    }
}
