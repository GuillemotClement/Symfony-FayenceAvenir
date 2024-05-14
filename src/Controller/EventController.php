<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    #[Route('/event', name: 'event')]
    public function index(EventRepository $eventRepo): Response
    {
        $user = $this->getUser();
        $events = $eventRepo->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'user' => $user
        ]);
    }

    #[Route('/event/new', name: 'add_event')]
    public function addEvent(EntityManagerInterface $em, Request $request){
        $user = $this->getUser();
        $event = new Event();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        if($eventForm->isSubmitted() && $eventForm->isValid()){

            $picture = $eventForm->get('pictureFile')->getData();
            $folder = $this->getParameter('event.folder');
            $ext = $picture->guessExtension();
            $filename = bin2hex(random_bytes(10)) . '.' . $ext;
            $picture->move($folder, $filename);
            $event->setPicture($this->getParameter('event.folder.public_path') . '/events/' . $filename);



            $event->setAuthor($user);
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Evenement ajouter avec succes');
            return $this->redirectToRoute('event');
        }
        return $this->render('event/add.html.twig', [
            'form' => $eventForm->createView(),
            'user' => $user
        ]);
    }

    #[Route('event/show/{id}', name: 'show_event')]
    public function showEvent(?string $id, EventRepository $eventRepo)
    {
        $event = $eventRepo->find($id);
        return $this->render('event/show.html.twig', [
            'event' => $event
        ]);
    }

    #[Route('event/delete/{id}', name: 'delete_event')]
    public function deleteEvent(?int $id, EventRepository $eventRepo, EntityManagerInterface $em)
    {
        $event = $eventRepo->find($id);
        $em->remove($event);
        $em->flush();
        $this->addFlash('success', 'Evènement supprimé avec succès');

        return $this->redirectToRoute('administration');
    }
}
