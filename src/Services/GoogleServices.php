<?php
/**
 * @author <Akartis>
 * (c) akartis-dev <sitrakaleon23@gmail.com>
 * Do it with love
 */

namespace App\Services;


use App\Entity\Events;
use App\Entity\Token;
use Doctrine\ORM\EntityManagerInterface;
use Google\Client;
use Google_Service_Calendar_EventAttendee;

class GoogleServices
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Get google client
     * @return Client
     * @throws \Google\Exception
     */
    public function getClient(): Client
    {
        $client = new Client();
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'google.json';
        $client->setAuthConfig($path);
        $client->addScope(\Google_Service_Calendar::CALENDAR);
        $redirect_uri = "https://fc8bb57b0991.ngrok.io/callback";
        $client->setRedirectUri($redirect_uri);

        return $client;
    }

    /**
     * Client with last access token
     * @return Client
     * @throws \Google\Exception
     */
    public function getClientForRequest(): Client
    {
        $allToken = $this->em->getRepository(Token::class)->findAll();
        /** @var Token $token */
        $token = end($allToken);

        $client = $this->getClient();
        $client->setAccessToken($token->getToken());

        return $client;
    }

    /**
     * List all event
     * @return \Google_Service_Calendar_Event[]
     * @throws \Google\Exception
     */
    public function getLastEventCalendar()
    {
        $client = $this->getClientForRequest();
        $service = new \Google_Service_Calendar($client);
        $calendarId = 'primary';
        $opt = [
            'maxResults' => 30,
            'orderBy' => 'starttime',
            'singleEvents' => true
        ];
        $results = $service->events->listEvents($calendarId, $opt);

        return $results->getItems();
    }

    /**
     * Add new event
     * @param Events $events
     * @throws \Google\Exception
     */
    public function addEvent(Events $events)
    {
        $client = $this->getClientForRequest();
        $service = new \Google_Service_Calendar($client);

        $event = new \Google_Service_Calendar_Event();
        $event->setSummary($events->getSummary());
        $event->setLocation('Itaosy Antananarivo');
        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDateTime($events->getStart()->format('c'));
        $start->setTimeZone('Africa/Addis_Ababa');
        $event->setStart($start);

        $end = new \Google_Service_Calendar_EventDateTime();
        $end->setDateTime($events->getEnd()->format('c'));
        $end->setTimeZone('Africa/Addis_Ababa');
        $event->setEnd($end);
        $attendee1 = new Google_Service_Calendar_EventAttendee();
        $attendee1->setEmail('leon@gmail.com');
        $attendee = [$attendee1];
        $event->setAttendees($attendee);
        $reminder = new \Google_Service_Calendar_EventReminders();
        $reminder->setUseDefault(true);
        $event->setReminders($reminder);

        try{
            $service->events->insert('primary', $event);
        }catch (\Exception $e){
            dd($e);
        }
    }

    /**
     * Remove event
     * @param string $id
     * @return bool
     * @throws \Google\Exception
     */
    public function removeEvent(string $id): bool
    {
        $client = $this->getClientForRequest();
        $service = new \Google_Service_Calendar($client);

        if($service->events->delete('primary', $id)){
            return true;
        }

        return false;
    }
}
