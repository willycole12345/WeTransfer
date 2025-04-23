<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UploadNotification extends Mailable  implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $links;
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct($links,$name)
    {
            $this->links = $links;
            $this->name = $name;
    }

     public function build()
    {
       // $this->view('Email.AccountPayment', ['mailData' => $this->mailData])->subject('Professional Overview of Tender Process on K-Tendering Portal');
        return $this->subject('Your files are ready to download')
                ->view('email.Uploadmail')
                ->with(['link' => $this->links,'name' => $this->name]);
    }
     
    
}
