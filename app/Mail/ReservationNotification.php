<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $type;
    public $user;  // Declaramos la propiedad $user
    public $business;  // Declaramos la propiedad $business

    public function __construct($reservation, $type)
    {
        $this->reservation = $reservation;
        $this->type = $type;
        $this->user = $reservation->user;
        $this->business = $reservation->business;  // Asignamos el negocio relacionado con la reserva
    }

    public function build()
    {
        $view = $this->type === 'created'
            ? 'emails.reservation_created'
            : 'emails.reservation_updated';

        return $this->view($view)
            ->subject('Notification about your reservation')
            ->with([
                'reservation' => $this->reservation,
                'user' => $this->user,  // Aseguramos que $user esté disponible en la vista
                'business' => $this->business,  // Aseguramos que $business esté disponible en la vista
            ]);
    }
}
