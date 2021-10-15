<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class EmailSender extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        $email = $request->email;
        $nome = $request->nome;
        $tel = $request->tel;
        $problema = $request->problema;

        return $this->view('emails.problem', [
            'email'=>$email, 
            'nome'=>$nome,
            'tel'=>$tel,
            'problema'=>$problema
            ]);
    }
}
