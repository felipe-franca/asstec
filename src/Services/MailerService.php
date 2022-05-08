<?php

namespace App\Services;

use App\Entity\Tickets;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;

class MailerService
{
    private $template;
    private $subject;
    private $transport;

    public function __construct($template, $subject=null)
    {
        $this->template = $template;
        $this->subject = $subject;
        $this->transport = new GmailSmtpTransport('helptechfmu@gmail.com', 'HelpTechFMU2022!');
    }

    public function newTicketEmail(Tickets $ticket)
    {
        $email = (new Email())
            ->from(new Address('helptechfmu@gmail.com', 'Equipe HelpTech'))
            ->to($ticket->getClient()->getEmail())
            ->subject($this->subject?: 'Equipe HelpTech')
            ->html($this->template);

        $mailer = new Mailer($this->transport);
        $result = $mailer->send($email);

        return $result;
    }
}
