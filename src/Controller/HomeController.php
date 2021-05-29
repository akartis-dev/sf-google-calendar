<?php
/**
 * @author <Akartis>
 * (c) akartis-dev <sitrakaleon23@gmail.com>
 * Do it with love
 */

namespace App\Controller;


use App\Entity\Events;
use App\Entity\Token;
use App\Form\EventsType;
use App\Services\GoogleServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private GoogleServices $googleServices;
    private EntityManagerInterface $entityManager;

    public function __construct(GoogleServices $googleServices, EntityManagerInterface $entityManager)
    {
        $this->googleServices = $googleServices;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="page.index")
     */
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $client = $this->googleServices->getClient();

            return $this->redirect($client->createAuthUrl());
        }

        return $this->render('home.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/callback", name="page.callback")
     */
    public function handleGoogle(Request $request): RedirectResponse
    {
        $code = $request->query->get('code');
        $token = $this->googleServices->getClient()->fetchAccessTokenWithAuthCode($code);

        $newToken = (new Token())->setToken($token['access_token']);
        $this->entityManager->persist($newToken);
        $this->entityManager->flush();
        $this->addFlash('success', 'Vous êtes bien connecter');

        return $this->redirectToRoute('page.calendar');
    }
}
