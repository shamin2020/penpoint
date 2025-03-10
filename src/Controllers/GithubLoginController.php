<?php

namespace src\Controllers;

use core\Request;
use src\Repositories\UserRepository;

class GithubLoginController extends Controller
{
    /**
     * Redirect the user to GitHub's OAuth consent screen.
     */
    public function redirectToGithub(Request $request): void
    {
        $clientId = $_ENV['GITHUB_CLIENT_ID'];
        $redirectUri = $_ENV['GITHUB_REDIRECT_URI'];
        $scope = 'user:email'; // Request email access

        $authUrl = "https://github.com/login/oauth/authorize?client_id={$clientId}&redirect_uri=" . urlencode($redirectUri) . "&scope=" . urlencode($scope);
        header("Location: " . $authUrl);
        exit;
    }

    /**
     * Handle the OAuth callback from GitHub.
     */
    public function handleCallback(Request $request): void
    {
        if (!isset($_GET['code'])) {
            $_SESSION['error'] = "No authorization code received from GitHub.";
            $this->redirect('/login');
            return;
        }

        $code = $_GET['code'];
        $clientId =  $_ENV['GITHUB_CLIENT_ID'];
        $clientSecret =  $_ENV['GITHUB_CLIENT_SECRET'];
        $redirectUri =  $_ENV['GITHUB_REDIRECT_URI'];

        // Exchange code for an access token.
        $tokenUrl = "https://github.com/login/oauth/access_token";
        $postData = http_build_query([
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'code'          => $code,
            'redirect_uri'  => $redirectUri,
        ]);

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\nAccept: application/json\r\n",
                'content' => $postData,
            ]
        ];
        $context = stream_context_create($opts);
        $response = file_get_contents($tokenUrl, false, $context);
        $data = json_decode($response, true);
        if (!isset($data['access_token'])) {
            $_SESSION['error'] = "Failed to obtain access token from GitHub.";
            $this->redirect('/login');
            return;
        }
        $accessToken = $data['access_token'];

        // Fetch user info from GitHub API.
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "Authorization: token {$accessToken}\r\nUser-Agent: YourAppName\r\nAccept: application/json\r\n",
            ]
        ];
        $context = stream_context_create($opts);
        $userJson = file_get_contents("https://api.github.com/user", false, $context);
        $githubUser = json_decode($userJson, true);

        // Retrieve primary email if not public.
        if (empty($githubUser['email'])) {
            $emailsJson = file_get_contents("https://api.github.com/user/emails", false, $context);
            $emails = json_decode($emailsJson, true);
            foreach ($emails as $emailInfo) {
                if ($emailInfo['primary'] && $emailInfo['verified']) {
                    $githubUser['email'] = $emailInfo['email'];
                    break;
                }
            }
        }

        if (empty($githubUser['email'])) {
            $_SESSION['error'] = "GitHub account does not have a verified email.";
            $this->redirect('/login');
            return;
        }

        // Check if user exists and create if needed.
        $userRepository = new UserRepository();
        $user = $userRepository->getByEmail($githubUser['email']);

        if (!$user) {
            $randomPassword = password_hash(uniqid(), PASSWORD_BCRYPT);
            $name = $githubUser['name'] ?? $githubUser['login'];
            $user = $userRepository->saveUser($name, $githubUser['email'], $randomPassword);
        }

        // Log the user in.
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->getId();
        $this->redirect('/');
    }
}
