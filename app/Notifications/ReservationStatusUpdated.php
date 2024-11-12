<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ReservationStatusUpdated extends Notification
{
    use Queueable;

    protected $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail']; // Enviar tanto por base de datos como por correo
    }

    /**
     * Get the database notification representation.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'reservation_id' => $this->reservation->id,
            'status' => $this->reservation->status,
            'message' => "Tu reserva para el negocio {$this->reservation->business->name} ha sido {$this->reservation->status}."
        ];
    }

    /**
     * Get the mail notification representation.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Actualización de Estado de Reserva')
            ->greeting('Hola ' . $notifiable->name)
            ->line('El estado de tu reserva ha cambiado.')
            ->line('Reserva para: ' . $this->reservation->business->name)
            ->line('Nuevo estado: ' . $this->reservation->status)
            ->action('Ver detalles de la reserva', url('/reservations/' . $this->reservation->id));
    }
}
