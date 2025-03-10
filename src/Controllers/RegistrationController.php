<?php

namespace src\Controllers;

use core\Request;
use src\Repositories\UserRepository;

class RegistrationController extends Controller
{
    public function index(Request $request): void
    {
        // Only allow guests to view the registration page.
        if ($request->isGuest()) {
            $this->render('register');
        } else {
            $this->redirect('/');
        }
    }

    public function register(Request $request): void
    {
        // Get form data from the request.
        $name = trim($request->input('name'));
        $email = trim($request->input('email'));
        $password = $request->input('password');

        // Basic validation: ensure none of the fields are empty.
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = "All fields are required.";
            $this->redirect('/register');
            return;
        }

        // Validate email format.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format.";
            $this->redirect('/register');
            return;
        }

        // Validate password: at least 8 characters and contains at least one symbol.
        if (strlen($password) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $_SESSION['error'] = "Password must be at least 8 characters and include at least one symbol.";
            $this->redirect('/register');
            return;
        }

        // Hash the plaintext password using Bcrypt with a cost of 12.
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Save the user record using the UserRepository.
        $userRepository = new UserRepository();
        $user = $userRepository->saveUser($name, $email, $hashedPassword);

        if (!$user) {
            $_SESSION['error'] = "Registration failed. Please try again.";
            $this->redirect('/register');
            return;
        }

        // Log in the user by saving the user's id in the session.
        $_SESSION['user_id'] = $user->getId();

        $this->redirect('/');
    }
}
