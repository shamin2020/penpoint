<?php

use core\Router;
use src\Controllers\ArticleController;
use src\Controllers\LoginController;
use src\Controllers\LogoutController;
use src\Controllers\RegistrationController;
use src\Controllers\SettingsController;
use src\Controllers\VoteController;
use src\Controllers\GithubLoginController;
use src\Controllers\ResetPasswordController;
use src\Controllers\CommentController;

/**
 * Home / Articles
 */
Router::get('/', [ArticleController::class, 'index']);  // Lists articles
Router::get('/article', [ArticleController::class, 'show']); // Show a single article (article.view.php)

/**
 * Article CRUD
 */
Router::get('/articles/create', [ArticleController::class, 'create']); 
Router::post('/articles', [ArticleController::class, 'store']);    
Router::get('/articles/edit', [ArticleController::class, 'edit']);  
Router::post('/articles/update', [ArticleController::class, 'update']); 
Router::post('/articles/delete', [ArticleController::class, 'delete']); 
Router::post('/vote', [VoteController::class, 'vote']);
Router::post('/comments/create', [CommentController::class, 'create']);
Router::post('/comments/delete', [CommentController::class, 'delete']);

/**
 * Authentication
 */
Router::get('/login', [LoginController::class, 'index']);  
Router::post('/login', [LoginController::class, 'login']); 

Router::get('/auth/github', [GithubLoginController::class, 'redirectToGithub']);
Router::get('/auth/github/callback', [GithubLoginController::class, 'handleCallback']);


Router::get('/logout', [LogoutController::class, 'logout']); 

/**
 * Registration
 */
Router::get('/register', [RegistrationController::class, 'index']);  
Router::post('/register', [RegistrationController::class, 'register']); 

/**
 * Password Reset  */
Router::get('/reset-password', [ResetPasswordController::class, 'showResetForm']); 
Router::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

/**
 * User Settings
 */
Router::get('/settings', [SettingsController::class, 'index']); 
Router::post('/settings/update', [SettingsController::class, 'update']); 


