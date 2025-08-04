<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use Illuminate\Support\Facades\Http;

class ResendEmailServiceTransport implements TransportInterface
{
    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->apiKey = config('services.resend.api_key');
        $this->endpoint = 'https://api.resend.com/emails';
    }

    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        if (!$message instanceof Email) {
            throw new \Exception('Invalid message type');
        }

        $to = $message->getTo();
        $from = $message->getFrom();
        $subject = $message->getSubject();
        $html = $message->getHtmlBody();
        $text = $message->getTextBody();
        $cc = $message->getCc();
        $bcc = $message->getBcc();

        $payload = [
            'from' => $from[0]->getAddress(),
            'to' => array_map(fn($addr) => $addr->getAddress(), $to),
            'subject' => $subject,
            'cc' => array_map(fn($addr) => $addr->getAddress(), $cc),
            'bcc' => array_map(fn($addr) => $addr->getAddress(), $bcc),
        ];

        if ($html) {
            $payload['html'] = $html;
        }
        if ($text) {
            $payload['text'] = $text;
        }

        $response = Http::withToken($this->apiKey)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post($this->endpoint, $payload);

        if ($response->getStatusCode() === 200) {
            return new SentMessage($message, $envelope);
        }

        throw new \Exception('Failed to send email: ' . $response->getBody()->getContents());
    }

    public function __toString(): string
    {
        return 'resend-transport';
    }
}
