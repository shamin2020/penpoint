<?php

namespace src\Controllers;

class LogoutController extends Controller
{
    public function logout(): void
    {
        // Unset all session variables.
        $_SESSION = [];
        
        // Delete the session cookie if it exists.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destroy the session.
        session_destroy();
        $this->redirect('/');
    }

}
