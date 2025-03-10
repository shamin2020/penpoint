<?php

namespace src\Controllers;

use core\Request;
use src\Repositories\ArticleRepository; 
use src\Repositories\UserRepository;

class CommentController extends Controller
{
    /**
     * Process creating a comment or a reply.
     */
    public function create(Request $request): void
    {
        // Ensure user is authenticated.
        if (!$request->isAuthenticated()) {
            $_SESSION['error'] = "You must be logged in to post a comment.";
            $this->redirect('/login');
            return;
        }

        $articleId = (int)$request->input('article_id');
        $description = trim($request->input('description'));
        $parentId = $request->input('parent_id') !== null ? (int)$request->input('parent_id') : null;
        $authorId = $_SESSION['user_id'];

        if (empty($description)) {
            $_SESSION['error'] = "Comment cannot be empty.";
            $this->redirect("/article?id=$articleId");
            return;
        }

    
        $articleRepository = new ArticleRepository();
        $comment = $articleRepository->addComment($description, $articleId, $authorId, $parentId);

        if (!$comment) {
            $_SESSION['error'] = "Failed to post comment.";
        } else {
            $_SESSION['success'] = "Comment posted successfully.";
        }
        $this->redirect("/article?id=$articleId");
    }

    /**
     * Process deletion of a comment.
     */
    public function delete(Request $request): void
    {
        // Ensure user is authenticated.
        if (!$request->isAuthenticated()) {
            $_SESSION['error'] = "You must be logged in to delete a comment.";
            $this->redirect('/login');
            return;
        }

        $commentId = (int)$request->input('id');
        $articleId = (int)$request->input('article_id');
        $userRepository = new UserRepository();
        $articleRepository = new \src\Repositories\ArticleRepository();
        
        // Retrieve the comment. 
        $comments = $articleRepository->getArticleComments($articleId);
        $foundComment = null;
        $flattenComments = function(array $comments) use (&$flattenComments) {
            $flat = [];
            foreach ($comments as $comment) {
                $flat[] = $comment;
                if (!empty($comment->getReplies())) {
                    $flat = array_merge($flat, $flattenComments($comment->getReplies()));
                }
            }
            return $flat;
        };
        $flatComments = $flattenComments($comments);
        foreach ($flatComments as $comment) {
            if ($comment->getId() === $commentId) {
                $foundComment = $comment;
                break;
            }
        }
        if (!$foundComment) {
            $_SESSION['error'] = "Comment not found.";
            $this->redirect("/article?id=$articleId");
            return;
        }
        // Check that the logged-in user is the comment's author.
        if ($_SESSION['user_id'] != $foundComment->getCreatorId()) {
            $_SESSION['error'] = "Unauthorized access.";
            $this->redirect("/article?id=$articleId");
            return;
        }
        
        $deleted = $articleRepository->deleteComment($commentId);
        if (!$deleted) {
            $_SESSION['error'] = "Failed to delete comment.";
        } else {
            $_SESSION['success'] = "Comment deleted successfully.";
        }
        $this->redirect("/article?id=$articleId");
    }
}
