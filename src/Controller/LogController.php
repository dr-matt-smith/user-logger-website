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
 * @Route("/log")
 * @IsGranted("ROLE_ADMIN")
 */
class LogController extends AbstractController
{

    /**
     * @Route("/csv", name="log_csv", methods={"GET"})
     */
    public function csv(LogRepository $logRepository): Response
    {
        $logs =  $logRepository->findAll();
        $output = 'id,userId,timestamp,scene,message' . PHP_EOL;
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
     * @Route("/", name="log_index", methods={"GET"})
     */
    public function index(LogRepository $logRepository): Response
    {
        return $this->render('log/index.html.twig', [
            'logs' => $logRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="log_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $log = new Log();
        $form = $this->createForm(LogType::class, $log);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($log);
            $entityManager->flush();

            return $this->redirectToRoute('log_index');
        }

        return $this->render('log/new.html.twig', [
            'log' => $log,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="log_show", methods={"GET"})
     */
    public function show(Log $log): Response
    {
        return $this->render('log/show.html.twig', [
            'log' => $log,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Log $log): Response
    {
        $form = $this->createForm(LogType::class, $log);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('log_index');
        }

        return $this->render('log/edit.html.twig', [
            'log' => $log,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="log_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Log $log): Response
    {
        if ($this->isCsrfTokenValid('delete'.$log->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($log);
            $entityManager->flush();
        }

        return $this->redirectToRoute('log_index');
    }

    /**
     * @Route("/delete_all", name="log_delete_all")
     */
    public function deleteAll(LogRepository $logRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entities = $logRepository->findAll();

        foreach ($entities as $entity) {
            $entityManager->remove($entity);
        }

        $entityManager->flush();

        return $this->redirectToRoute('log_index');
    }
}
