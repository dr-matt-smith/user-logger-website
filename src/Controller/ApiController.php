<?php

namespace App\Controller;

use App\Entity\Log;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;



/**
 * @Route("/api", name="api_")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/{userId}/{scene}/{message}", name="new")
     */
    public function new($userId, $scene, $message)
    {
        $log = new Log();
        $log->setUserId($userId);
        $log->setTimestamp(new \DateTime());
        $log->setScene($scene);
        $log->setMessage($message);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($log);
        $entityManager->flush();

        return new Response("success");
    }



}
