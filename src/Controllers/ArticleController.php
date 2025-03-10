<?php

namespace src\Controllers;

use core\Request;
use src\Repositories\ArticleRepository;

class ArticleController extends Controller
{
    /**
     * Display the page showing the articles.
     */
    public function index(Request $request): void
{
    $search = $request->input('search') ?? '';
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 5;
    $offset = ($currentPage - 1) * $limit;

    $articleRepository = new ArticleRepository();
    $totalArticles = $articleRepository->getCount($search);
    $totalPages = ceil($totalArticles / $limit);
    $articles = $articleRepository->getArticlesWithLimit($limit, $offset, $search);

    $this->render('index', [
        'articles'    => $articles,
        'currentPage' => $currentPage,
        'totalPages'  => $totalPages,
        'search'      => $search,
        'articleRepository'=> $articleRepository,
    ]);
}


    /**
     * Show the form for creating an article.
     */
    public function create(Request $request): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to create an article.";
            $this->redirect('/login');
            return;
        }
        $this->render('new_article');
    }

    /**
     * Process the storing of a new article.
     */
    public function store(Request $request): void
    {
        $title = trim($request->input('title'));
        $url   = trim($request->input('url'));

        // Validate input: both title and URL must not be empty.
        if (empty($title) || empty($url)) {
            $_SESSION['error'] = "Title and URL are required.";
            $this->redirect('/articles/create');
            return;
        }
        // Validate URL format.
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $_SESSION['error'] = "Invalid URL format.";
            $this->redirect('/articles/create');
            return;
        }

        // Ensure the user is logged in.
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to create an article.";
            $this->redirect('/login');
            return;
        }
        $authorId = $_SESSION['user_id'];

        $articleRepository = new ArticleRepository();
        $article = $articleRepository->saveArticle($title, $url, $authorId);
        if (!$article) {
            $_SESSION['error'] = "Failed to create article. Please try again.";
            $this->redirect('/articles/create');
            return;
        }
        // Redirect to the article detail page.
        $_SESSION['success'] = "The Article created successfully";
        $this->redirect("/article?id=" . $article->getId());
    }

    /**
     * Show the form for editing an article.
     */
    public function edit(Request $request): void
    {
        // Get the article ID from the request.
        $id = (int)$request->input('id');
        $articleRepository = new ArticleRepository();
        $article = $articleRepository->getArticleById($id);
        if (!$article) {
            $_SESSION['error'] = "Article not found.";
            $this->redirect('/');
            return;
        }
        // Only allow the article's author to edit it.
        if ($_SESSION['user_id'] != $article->getAuthorId()) {
            $_SESSION['error'] = "Unauthorized access.";
            $this->redirect('/');
            return;
        }
        $this->render('update-article', [
            'article' => $article,
        ]);
    }

/**
 * Process the editing of an article.
 * @param Request $request
 * @return void
 */
public function update(Request $request): void
{
    $id    = (int)$request->input('id');
    $title = trim($request->input('title'));
    $url   = trim($request->input('url'));

    // Validate input: both title and URL must not be empty.
    if (empty($title) || empty($url)) {
        $_SESSION['error'] = "Title and URL are required.";
        $this->redirect("/articles/edit?id=$id");
        return;
    }
    // Validate URL format.
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $_SESSION['error'] = "Invalid URL format.";
        $this->redirect("/articles/edit?id=$id");
        return;
    }

    $articleRepository = new ArticleRepository();
    $article = $articleRepository->getArticleById($id);
    if (!$article) {
        $_SESSION['error'] = "Article not found.";
        $this->redirect('/');
        return;
    }
    // Authorization check: ensure that only the article's author can update it.
    if ($_SESSION['user_id'] != $article->getAuthorId()) {
        $_SESSION['error'] = "Unauthorized access.";
        $this->redirect('/');
        return;
    }

    // Attempt to update the article.
    $updated = $articleRepository->updateArticle($id, $title, $url);
    if (!$updated) {
        $_SESSION['error'] = "Failed to update article.";
        $this->redirect("/articles/edit?id=$id");
        return;
    }
    // If update is successful, redirect to the article detail page.
    $_SESSION['success'] = "The Article updated successfully";
    $this->redirect("/article?id=$id");
}


    /**
     * Process the deleting of an article.
     */
    public function delete(Request $request): void
    {
        $id = (int)$request->input('id');
        $articleRepository = new ArticleRepository();
        $article = $articleRepository->getArticleById($id);
        if (!$article) {
            $_SESSION['error'] = "Article not found.";
            $this->redirect('/');
            return;
        }
        if ($_SESSION['user_id'] != $article->getAuthorId()) {
            $_SESSION['error'] = "Unauthorized access.";
            $this->redirect('/');
            return;
        }

        $deleted = $articleRepository->deleteArticleById($id);
        if (!$deleted) {
            $_SESSION['error'] = "Failed to delete article.";
            $this->redirect("/article?id=$id");
            return;
        }
        $_SESSION['success'] = "The Article deleted successfully";
        $this->redirect('/');
    }

    public function show(Request $request): void
    {
        // Retrieve the article ID from the request query parameters.
        $id = $request->input('id');
        if (!$id) {
            $_SESSION['error'] = "No article specified.";
            $this->redirect('/');
            return;
        }

        $articleRepository = new ArticleRepository();
        $article = $articleRepository->getArticleById((int)$id);
        if (!$article) {
            $_SESSION['error'] = "Article not found.";
            $this->redirect('/');
            return;
        }

        // Retrieve votes and comments for the article using helper functions.
        // Ensure these helper functions exist in your helpers file.
        $votesCount = function_exists('getArticleVotes') ? getArticleVotes($article->getId()) : 0;
        $comments = function_exists('getArticleComments') ? getArticleComments($article->getId()) : [];

        // Render the article view, passing the article, vote count, and comments.
        $this->render('article', [
            'article'    => $article,
            'votesCount' => $votesCount,
            'comments'   => $comments,
        ]);
    }

}
