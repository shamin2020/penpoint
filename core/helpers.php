<?php
/**
 * Returns the URL path for an image.
 *
 * @param string $filename The filename of the image.
 * @param string $folder   Optional folder name.
 * @return string          The URL path to the image.
 */
if (!function_exists('image')) {
    function image(string $filename, string $folder = ''): string {
        if ($folder !== '') {
            return "/images/$folder/$filename";
        }
        return "/images/$filename";
    }
}

/**
 * Returns a value from the session.
 *
 * @param string $key The session key.
 * @return mixed      The session data, or null if not set.
 */
if (!function_exists('getSessionData')) {
    function getSessionData(string $key) {
        return $_SESSION[$key] ?? null;
    }
}

/**
 * Retrieves a value from the session and then unsets it.
 *
 * @param string $key The session key.
 * @return mixed      The session data that was popped, or null if not set.
 */
if (!function_exists('popSessionData')) {
    function popSessionData(string $key) {
        $data = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $data;
    }
}

/**
 * Returns old session data for a given key and then unsets it.
 * Useful for repopulating form data after a validation error.
 *
 * @param string $key The session key.
 * @return mixed      The old session data, or null if not set.
 */
if (!function_exists('old')) {
    function old(string $key) {
        $data = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $data;
    }
}

/**
 * Retrieves the total vote count for a given article.
 *
 * @param int $articleId The article ID.
 * @return int           The total vote count.
 */
if (!function_exists('getArticleVotes')) {
    function getArticleVotes(int $articleId): int {
        $articleRepository = new \src\Repositories\ArticleRepository();
        return $articleRepository->getArticleVotes($articleId);
    }
}

/**
 * Retrieves the comments for a given article as an array.
 *
 * @param int $articleId The article ID.
 * @return array       An array of Comment objects.
 */
if (!function_exists('getArticleComments')) {
    function getArticleComments(int $articleId): array {
        $articleRepository = new \src\Repositories\ArticleRepository();
        return $articleRepository->getArticleComments($articleId);
    }
}

/**
 * Retrieves the vote value of a given user for a given article.
 *
 * @param int $articleId The article ID.
 * @param int $userId    The user's ID.
 * @return int|null      The vote value or null if no vote exists.
 */
if (!function_exists('getUserVoteForArticle')) {
    function getUserVoteForArticle(int $articleId, int $userId): ?int {
        $articleRepository = new \src\Repositories\ArticleRepository();
        return $articleRepository->getUserVoteForArticle($articleId, $userId);
    }
}

/**
 * Escapes a string for safe HTML output.
 *
 * @param string $string The string to escape.
 * @return string        The escaped string.
 */
if (!function_exists('e')) {
    function e(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Recursively renders nested comments.
 *
 * @param array  $comments           An array of Comment objects (nested tree structure).
 * @param mixed  $authenticatedUser  The logged-in user object, or null.
 * @param object $userRepository     An instance of UserRepository to fetch comment author details.
 * @param int    $articleId          The ID of the current article.
 */
if (!function_exists('renderComments')) {
    function renderComments(array $comments, $authenticatedUser, $userRepository, int $articleId) {
        echo '<ul class="ml-4 space-y-2">';
        foreach ($comments as $comment) {
            echo '<li class="bg-[#2C3E50] p-2 rounded">';
            
            // Display comment content.
            echo '<p>' . e($comment->getComment()) . '</p>';
            
            // Fetch and display comment author and creation date.
            $commentAuthor = $userRepository->getById($comment->getCreatorId());
            $authorName = $commentAuthor ? e($commentAuthor->getName()) : 'Unknown';
            $createdAt = e($comment->getCreatedAt() ?? 'Unknown Date');
            echo '<p class="text-xs text-gray-400">By: ' . $authorName . ' on ' . $createdAt . '</p>';
            
            // If a user is logged in, display reply link and delete option if the user is the comment author.
            if ($authenticatedUser) {
                echo '<a href="javascript:void(0);" onclick="toggleReplyForm(' . e($comment->getId()) . ')" class="text-sm text-[#E67E22] hover:underline mr-2">Reply</a>';
                
                // Inline reply form (hidden by default)
                echo '<div id="reply-form-' . e($comment->getId()) . '" class="hidden mt-2">';
                echo '<form action="/comments/create" method="POST" class="space-y-2">';
                echo '<input type="hidden" name="article_id" value="' . e($articleId) . '">';
                echo '<input type="hidden" name="parent_id" value="' . e($comment->getId()) . '">';
                echo '<textarea name="description" rows="3" placeholder="Your reply..." class="w-full p-2 bg-gray-700 text-white rounded focus:outline-none focus:ring-2 focus:ring-[#E67E22]"></textarea>';
                echo '<button type="submit" class="px-3 py-1 bg-[#E67E22] text-white rounded hover:bg-[#d66e1e] text-sm">Submit Reply</button>';
                echo '</form>';
                echo '</div>';
                
                if ($authenticatedUser->getId() == $comment->getCreatorId()) {
                    echo '<form action="/comments/delete" method="POST" class="inline-block" onsubmit="return confirm(\'Are you sure you want to delete this comment?\');">';
                    echo '<input type="hidden" name="id" value="' . e($comment->getId()) . '">';
                    echo '<input type="hidden" name="article_id" value="' . e($articleId) . '">';
                    echo '<button type="submit" class="text-sm text-red-500 hover:underline">Delete</button>';
                    echo '</form>';
                }
            }
            
            // Recursively render child comments, if any.
            if (!empty($comment->getReplies())) {
                renderComments($comment->getReplies(), $authenticatedUser, $userRepository, $articleId);
            }
            echo '</li>';
        }
        echo '</ul>';
    }
}
