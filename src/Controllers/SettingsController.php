<?php

namespace src\Controllers;

use core\Request;
use src\Repositories\UserRepository;

class SettingsController extends Controller
{
    /**
     * Display the settings/profile page.
     */
    public function index(Request $request): void
    {
        // Ensure the user is logged in.
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to view settings.";
            $this->redirect('/login');
            return;
        }
        
        // Retrieve the current user record.
        $userRepository = new UserRepository();
        $user = $userRepository->getById($_SESSION['user_id']);
        
        // Optionally, update session data with the current username.
        $_SESSION['username'] = $user ? $user->getName() : '';

        // Render the settings view and pass the user data.
        $articleRepository = new \src\Repositories\ArticleRepository();
        $myArticles = $articleRepository->getArticlesByAuthor($_SESSION['user_id']);  // Create this method if needed.
        $this->render('settings', [
                      'user'       => $user,      // existing user data
                      'myArticles' => $myArticles,
                        ]);

    }

    /**
     * Process the update of a user record (username and profile picture).
     */
    public function update(Request $request): void
    {
        // Ensure the user is logged in.
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to update settings.";
            $this->redirect('/login');
            return;
        }

        // Retrieve and validate the new username.
        $username = trim($request->input('username'));
        if (empty($username)) {
            $_SESSION['error'] = "Username cannot be empty.";
            $this->redirect('/settings');
            return;
        }

        // Handle profile picture upload if one was provided.
        $profilePicture = null;
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            // Validate file type (for example, allow only jpg, jpeg, png, and gif).
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $fileInfo = pathinfo($_FILES['profile_picture']['name']);
            $extension = strtolower($fileInfo['extension'] ?? '');
            if (!in_array($extension, $allowed)) {
                $_SESSION['error'] = "Invalid file type for profile picture. Allowed types: jpg, jpeg, png, gif.";
                $this->redirect('/settings');
                return;
            }
            
            // Generate a unique file name.
            $newFileName = uniqid() . '.' . $extension;
            // Ensure the destination directory exists and is writable.
            $destination = __DIR__ . '/../../public/images/profiles/' . $newFileName;
            
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
                $profilePicture = '/images/profiles/' . $newFileName;
            } else {
                $_SESSION['error'] = "Failed to upload profile picture.";
                $this->redirect('/settings');
                return;
            }
        }
        
        // Update the user record via the repository.
        $userRepository = new UserRepository();
        $updated = $userRepository->updateUser($_SESSION['user_id'], $username, $profilePicture);
        if (!$updated) {
            $_SESSION['error'] = "Failed to update profile. Please try again.";
            $this->redirect('/settings');
            return;
        }
        
        // Update session data (e.g., username) so the changes are reflected immediately.
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "Profile updated successfully.";
        $this->redirect('/settings');
    }
}
