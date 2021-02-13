<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantCreated extends Notification
{
    private $fqdn;

    public function __construct(string $fqdn)
    {
        $this->fqdn = $fqdn;
    }

    public function via()
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = "http://{$this->fqdn}";

        $app = config('app.name');

        return (new MailMessage())
            ->subject("{$app} Cuenta creada")
            ->greeting("Hola {$notifiable->name},")
            ->line("Tu cuenta de inquilino ha sido creada en {$app}!")
            ->action('Acceder ahora', $url);
    }
}
