<?php
/**
 * @author <Akartis>
 * (c) akartis-dev <sitrakaleon23@gmail.com>
 * Do it with love
 */

namespace App\Controller;


use App\Entity\Events;
use App\Form\EventsType;
use App\Services\GoogleServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    private GoogleServices $googleServices;

    public function __construct(GoogleServices $googleServices)
    {
        $this->googleServices = $googleServices;
    }

    /**
     * @Route("/calendar", name="page.calendar")
     */
    public function calendar(): Response
    {
        $last = $this->googleServices->getLastEventCalendar();

        return $this->render("calendar.html.twig", ['events' => $last]);
    }

    /**
     * @Route("/event", name="page.add.event")
     */
    public function postEvent(Request $request): Response
    {
        $event = new Events();
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->googleServices->addEvent($event);
            $this->addFlash('success', 'Event ajouter avec succès');

            return $this->redirectToRoute('page.calendar');
        }

        return $this->render('event.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/event/{id}", name="event.delete", methods={"DELETE"})
     */
    public function delete(Request $request, string $id): RedirectResponse
    {
        $csrf = $request->request->get('_csrf');
        if ($this->isCsrfTokenValid("delete-$id", $csrf)) {
            if ($this->googleServices->removeEvent($id)) {
                $this->addFlash('success', 'Event supprimer avec succès');
            } else {
                $this->addFlash('danger', "Une erreur s'est produite");
            }
        }

        return $this->redirectToRoute('page.calendar');
    }
}
