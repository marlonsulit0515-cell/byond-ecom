<?php

namespace App\View\Components;


use Illuminate\View\Component;

class AuthModal extends Component
{
    public $redirectAfterLogin;
    public $message;
    
    public function __construct($redirectAfterLogin = null, $message = null)
    {
        $this->redirectAfterLogin = $redirectAfterLogin;
        $this->message = $message ?? 'Please log in or create an account to complete your purchase.';
    }

    public function render()
    {
        return view('components.auth-modal');
    }
}
