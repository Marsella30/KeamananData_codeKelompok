<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function build()
    {
        return $this
            ->subject('Berikut Link Anda')
            ->view('emails.linkMail')
            ->with([
                'url' => $this->url
            ]);
    }
}
