<?php

namespace App\Controller;

use App\Entity\Log;
use App\Form\LogType;
use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/log_")
 * @IsGranted("ROLE_USER")
 */
class LogCsvController extends AbstractController
{

    /**
     * @Route("csv", name="log_csv", methods={"GET"})
     */
    public function csv(LogRepository $logRepository): Response
    {
        $logs =  $logRepository->findAll();
        $output = 'id,applicationId,participant,timestamp,scene,message' . PHP_EOL;
        foreach ($logs as $log) {
            $output .= $log;
        }

        $response = new Response($output);

        $response->headers->set('Content-Type', 'text/csv');
        return $response;

        // or try:
        // https://gist.github.com/zuzuleinen/6db61b09465e9bc8a7ea
    }


    /**
     * @Route("_delete_all", name="log_delete_all")
     */
    public function deleteAll(LogRepository $logRepository): Response
    {
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $entities = $logRepository->findBy([
            'owner' => $user
        ]);

        foreach ($entities as $entity) {
            $entityManager->remove($entity);
        }

        $entityManager->flush();

        return $this->redirectToRoute('log_index');
    }

}
