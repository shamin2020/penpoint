<?php

namespace src\Controllers;

use core\Request;
use src\Repositories\ArticleRepository;

class VoteController extends Controller
{
    public function vote(Request $request): void
    {
        // Ensure the user is logged in.
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to vote.";
            $this->redirect('/login');
            return;
        }

        // Retrieve POST data.
        $articleId = (int)$request->input('article_id');
        $value = (int)$request->input('value'); // Expecting a value of 1 for upvote.

        // Validate vote value.
        if ($value !== 1) {
            $_SESSION['error'] = "Invalid vote value.";
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
            return;
        }

        $articleRepository = new ArticleRepository();
        $userId = $_SESSION['user_id'];

        // Check if the user has already voted for this article.
        $existingVote = $articleRepository->getUserVoteForArticle($articleId, $userId);

        if ($existingVote === null) {
            // If no vote exists, add the vote.
            $voteAdded = $articleRepository->addVote($articleId, $userId, $value);
            if (!$voteAdded) {
                $_SESSION['error'] = "Failed to register your vote. Please try again.";
            } else {
                $_SESSION['success'] = "Your vote has been recorded.";
            }
        } else {
            // Vote exists: remove the vote.
            $voteDeleted = $articleRepository->deleteVote($articleId, $userId);
            if (!$voteDeleted) {
                $_SESSION['error'] = "Failed to remove your vote. Please try again.";
            } else {
                $_SESSION['success'] = "Your vote has been removed.";
            }
        }

        // Redirect back to the previous page.
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}
