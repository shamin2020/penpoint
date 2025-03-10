<?php

namespace src\Controllers;

use core\Request;
use src\Repositories\UserRepository;

class LoginController extends Controller
{
    /**
     * Show the login page.
     */
    public function index(Request $request): void
    {
        if ($request->isGuest()) {
            $this->render('login');
        }
        $this->redirect('/');
    }

    /**
     * Process the login attempt.
     */
    public function login(Request $request): void
    {
       
       $email = trim($request->input('email'));
       $password = $request->input('password');

       // Validate that both email and password are provided.
       if (empty($email) || empty($password)) {
           $_SESSION['error'] = "Both email and password are required.";
           $this->redirect('/login');
           return;
       }

       $userRepository = new UserRepository();
       $user = $userRepository->getByEmail($email);

     
       if (!$user) {
           $_SESSION['error'] = "User not found.";
           $this->redirect('/login');
           return;
       }

       if (!password_verify($password, $user->getPasswordDigest())) {
           $_SESSION['error'] = "Invalid password.";
           $this->redirect('/login');
           return;
       }

       // Successful login: store the user id in the session.
       session_regenerate_id(true);
       $_SESSION['user_id'] = $user->getId();
       $this->redirect('/');
    }

}
