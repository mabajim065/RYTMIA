<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use App\Models\User;
use App\Models\Competicion;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->addScope(Calendar::CALENDAR);
    }

    /**
     * Crea un evento en el calendario del usuario (admin) y añade asistentes.
     */
    public function createEventWithAttendees(User $creator, Competicion $competicion, array $attendeesEmails)
    {
        if (!$creator->google_token) {
            return false;
        }

        $this->client->setAccessToken($creator->google_token);

        // Si el token ha expirado, refrescarlo
        if ($this->client->isAccessTokenExpired()) {
            if ($creator->google_refresh_token) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($creator->google_refresh_token);
                $creator->update(['google_token' => $newToken['access_token']]);
            } else {
                return false;
            }
        }

        $service = new Calendar($this->client);

        $attendees = [];
        foreach ($attendeesEmails as $email) {
            if (!empty($email)) {
                $attendees[] = new \Google\Service\Calendar\EventAttendee(['email' => $email]);
            }
        }

        $event = new Event([
            'summary' => 'Competición: ' . $competicion->nombre,
            'location' => $competicion->direccion ?? $competicion->lugar,
            'description' => 'Has sido invitado a la competición de Gimnasia Rítmica - RYTMIA',
            'start' => [
                'date' => $competicion->fecha->format('Y-m-d'),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'date' => $competicion->fecha->format('Y-m-d'),
                'timeZone' => config('app.timezone'),
            ],
            'attendees' => $attendees,
        ]);

        $calendarId = 'primary';
        $optParams = ['sendUpdates' => 'all']; // Enviar invitación por email a todos los asistentes
        
        try {
            $service->events->insert($calendarId, $event, $optParams);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
