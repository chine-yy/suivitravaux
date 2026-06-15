<?php

namespace App\Http\Controllers;

use App\Services\PhpMailerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ContactController extends Controller
{
    public function send(Request $request, PhpMailerService $mailer): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $body = View::make('emails.contact', $data)->render();

        $sent = $mailer->send([
            'to'       => 'decidaisomar@gmail.com',
            'to_name'  => 'Contact ' . config('app.name'),
            'subject'  => 'Nouveau message du site ' . config('app.name'),
            'body'     => $body,
            'alt_body' => strip_tags($body),
            'is_html'  => true,
        ]);

        return back()->with($sent ? 'status' : 'error', $sent
            ? 'Merci, votre message a bien été envoyé.'
            : 'Une erreur est survenue, merci de réessayer.')->withFragment('contact');
    }
}

