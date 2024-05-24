<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Service\Uploader;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use IntlDateFormatter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EventController extends AbstractController
{
    // Affichage des évènement
    #[Route('/event', name: 'event')]
    public function index(EventRepository $eventRepo): Response
    {
        $user = $this->getUser();
        $events = $eventRepo->findAll();

        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::SHORT
        );

        $formatter->setPattern('EEEE d MMMM y H:mm');

        $formattedEvents = [];
        foreach ($events as $event) {
            $formattedEvents[] = [
                'event' => $event,
                'formattedDate' => $formatter->format($event->getDate())
            ];
        }



        return $this->render('event/index.html.twig', [
            'events' => $events,
            'formattedEvents' => $formattedEvents,
            'user' => $user
        ]);
    }

    // ajouter un nouvel event
    #[Route('/event/new', name: 'add_event')]
    public function addEvent(EntityManagerInterface $em, Request $request, Uploader $uploader){
        $user = $this->getUser();
        $event = new Event();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        if($eventForm->isSubmitted() && $eventForm->isValid()){
            $picture = $eventForm->get('pictureFile')->getData();
            if($picture){
                $event->setPicture($uploader->uploadEventImage($picture));
            }
            $event->setAuthor($user);
            $em->persist($event);
            $em->flush();
            // $this->addFlash('success', 'Evenement ajouter avec succes');
            return $this->redirectToRoute('event');
        }
        return $this->render('event/add.html.twig', [
            'form' => $eventForm->createView(),
            'user' => $user
        ]);
    }

    // editer un event
    #[Route('/event/edit/{id}', name:'edit_event')]
    #[IsGranted('ROLE_ADMIN')]
    public function editEvent(Request $request, EventRepository $eventRepository, EntityManagerInterface $em, ?int $id, Uploader $uploader)
    {
        $user = $this->getUser();
        $event = $eventRepository->find($id);
        $eventForm = $this->createForm(EventType::class, $event);
        $eventForm->handleRequest($request);
        if($eventForm->isSubmitted() && $eventForm->isValid()){
            $picture = $eventForm->get('pictureFile')->getData();
            $oldPicture = $event->getPicture();
            if($picture){
                $event->setPicture($uploader->uploadEventImage($picture, $oldPicture));
            }
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('event/add.html.twig', [
            'form' =>$eventForm->createView(),
            'user' => $user
        ]);
    }
    
    // afficher un event précis
    #[Route('event/show/{id}', name: 'show_event')]
    public function showEvent(?string $id, EventRepository $eventRepo)
    {
        $event = $eventRepo->find($id);

        $formatter = new IntlDateFormatter(
            'fr_FR',
            IntlDateFormatter::FULL,
            IntlDateFormatter::SHORT
        );
        $formatter->setPattern('EEEE d MMMM y H:mm');

        $formattedDate = $formatter->format($event->getDate());


        return $this->render('event/show.html.twig', [
            'event' => $event,
            'date' => $formattedDate
        ]);
    }

    // supprimer un event
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
