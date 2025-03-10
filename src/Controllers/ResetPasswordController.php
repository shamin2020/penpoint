<?php

namespace src\Controllers;

use core\Request;
use src\Repositories\UserRepository;

class ResetPasswordController extends Controller
{
    /**
     * Display the reset password form.
     * 
     * If a token is provided in the query string, display the new password form.
     * Otherwise, display the form to request a password reset.
     */
    public function showResetForm(Request $request): void
    {
        $token = $request->input('token');
        if ($token) {
            // If token exists, show form to enter new password.
            // For this demo, we check the token against a session variable.
            if (isset($_SESSION['reset_token']) && $_SESSION['reset_token'] === $token) {
                $this->render('resetPassword', ['token' => $token]);
                return;
            } else {
                $_SESSION['error'] = "Invalid or expired token.";
                $this->redirect('/reset-password');
                return;
            }
        } else {
            // Show the form to request a password reset (enter email).
            $this->render('resetPassword');
        }
    }

    /**
     * Process the reset password request.
     * 
     * If no token is present, this handles the email submission and sends a reset link.
     * If a token is present, this handles the new password submission.
     */
    public function resetPassword(Request $request): void
    {
        $token = $request->input('token');
        if (!$token) {
            // Process password reset request by email.
            $email = trim($request->input('email'));
            if (empty($email)) {
                $_SESSION['error'] = "Please provide your email address.";
                $this->redirect('/reset-password');
                return;
            }
            $userRepository = new UserRepository();
            $user = $userRepository->getByEmail($email);
            if (!$user) {
                $_SESSION['error'] = "No user found with that email address.";
                $this->redirect('/reset-password');
                return;
            }
            // Generate a token and store it in the session (for demo purposes).
            $token = bin2hex(random_bytes(16));
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;
            // In a real application, send an email with a link including the token.
            $_SESSION['success'] = "A password reset link has been sent to your email. For demo purposes, click <a href='/reset-password?token=$token' class='hover:underline'><strong class='underline'>here</strong></a> to reset your password.";
            $this->redirect('/reset-password');
            return;
        } else {
            // Process new password submission.
            $newPassword = $request->input('password');
            $confirmPassword = $request->input('confirm_password');
            if (empty($newPassword) || empty($confirmPassword)) {
                $_SESSION['error'] = "Please fill out all password fields.";
                $this->redirect("/reset-password?token=$token");
                return;
            }
            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = "Passwords do not match.";
                $this->redirect("/reset-password?token=$token");
                return;
            }
            // Enforce a minimum password length and require at least one symbol.
            if (strlen($newPassword) < 8 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $newPassword)) {
                $_SESSION['error'] = "Password must be at least 8 characters and include at least one symbol.";
                $this->redirect("/reset-password?token=$token");
                return;
            }
            if (!isset($_SESSION['reset_email'])) {
                $_SESSION['error'] = "Reset session expired. Please try again.";
                $this->redirect('/reset-password');
                return;
            }
            $email = $_SESSION['reset_email'];
            $userRepository = new UserRepository();
            $user = $userRepository->getByEmail($email);
            if (!$user) {
                $_SESSION['error'] = "User not found.";
                $this->redirect('/reset-password');
                return;
            }
            // Hash the new password.
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            $updated = $userRepository->updatePassword($user->getId(), $hashedPassword);
            if (!$updated) {
                $_SESSION['error'] = "Failed to update password.";
                $this->redirect("/reset-password?token=$token");
                return;
            }
            // Clear the reset token and email from the session.
            unset($_SESSION['reset_token'], $_SESSION['reset_email']);
            $_SESSION['success'] = "Your password has been updated. Please log in.";
            $this->redirect('/login');
        }
    }
}
