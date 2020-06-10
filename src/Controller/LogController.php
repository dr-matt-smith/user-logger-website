<?php

namespace App\Controller;

use App\Entity\Application;
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
 * @IsGranted("ROLE_USER")
 */
class LogController extends AbstractController
{
    /**
     * @Route("/application/{id}", name="log_aplication", methods={"GET"})
     */
    public function logsForApplication(Application $application, LogRepository $logRepository): Response
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        $user = $this->getUser();
        $owner = $application->getOwner();
        if($user != $owner && !$isAdmin){
            return new Response("sorry - you cannot access logs for an application you don't own ...");
        }

        return $this->render('log/index.html.twig', [
            'logs' => $logRepository->findBy([
                'application' => $application
            ]),
        ]);


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
            $user = $this->getUser();
            $owner = $log->getApplication()->getOwner();
            if($user != $owner){
                return new Response("sorry - you cannot create a log for an application you don't own ...");
            }

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
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        $user = $this->getUser();
        $owner = $log->getApplication()->getOwner();
        if($user != $owner && !$isAdmin){
            return new Response("sorry - you cannot access logs for an application you don't own ...");
        }

        return $this->render('log/show.html.twig', [
            'log' => $log,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="log_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Log $log): Response
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');


        $user = $this->getUser();
        $owner = $log->getApplication()->getOwner();
        if($user != $owner && !$isAdmin){
            return new Response("sorry - you cannot access logs for an application you don't own ...");
        }

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
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        $user = $this->getUser();
        $owner = $log->getApplication()->getOwner();
        if($user != $owner && !$isAdmin){
            return new Response("sorry - you cannot access logs for an application you don't own ...");
        }


        if ($this->isCsrfTokenValid('delete'.$log->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($log);
            $entityManager->flush();
        }

        return $this->redirectToRoute('log_index');
    }
}
