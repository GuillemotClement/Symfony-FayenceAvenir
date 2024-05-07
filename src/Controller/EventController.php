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
        $events = $eventRepo->findAll();
        return $this->render('event/index.html.twig', [
            'events' => $events
        ]);
    }

    #[Route('/event/new', name: 'add_event')]
    public function addEvent(EntityManagerInterface $em, Request $request){
        $user = $this->getUser();
        $event = new Event();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        if($eventForm->isSubmitted() && $eventForm->isValid()){
            $event->setAuthor($user);
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Evenement ajouter avec succes');
            return $this->redirectToRoute('event');
        }
        return $this->render('event/add.html.twig', [
            'form' => $eventForm->createView()
        ]);
    }

    #[Route('event/show/{name}', name: 'show_event')]
    public function showEvent(?string $name, EventRepository $eventRepo)
    {
        $event = $eventRepo->findOneBy(['name' => $name]);
        return $this->render('event/show.html.twig', [
            'event' => $event
        ]);
    }
}
