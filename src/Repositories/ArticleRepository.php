<?php

namespace src\Repositories;

require_once 'Repository.php';
require_once __DIR__ . '/../Models/Article.php';

use src\Models\Article as Article;
use src\Models\User;
use src\Models\Comment as Comment;
use PDO;
use PDOException;

class ArticleRepository extends Repository
{
    /**
     * @return Article[]
     */
    public function getArticles(): array
    {
        try {
            $sqlStatement = $this->pdo->query("SELECT * FROM articles;");
            $rows = $sqlStatement->fetchAll();
    
            $articles = [];
            foreach ($rows as $row) {
                $articles[] = (new Article())->fill($row);
            }
    
            return $articles;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function saveArticle(string $title, string $url, string $authorId): Article|false
    {
        try{
            $createdAt = date('Y-m-d H:i:s');

            // Use prepared statements to prevent SQL injection
            $sqlStatement = $this->pdo->prepare(
                "INSERT INTO articles (created_at, updated_at, url, title, author_id) 
                VALUES (:createdAt, NULL, :url, :title,:authorId )");

            $sqlStatement->execute([
                ':createdAt' => $createdAt,
                ':url' => $url,
                ':title' => $title,
                ':authorId'=> $authorId
            ]);         
    
            if ($sqlStatement->rowCount() === 1) {
                
                $id = $this->pdo->lastInsertId();
    
                $sqlStatement = $this->pdo->prepare("SELECT * FROM articles WHERE id = :id");
                $sqlStatement->execute([':id' => $id]);
                $result = $sqlStatement;
               
                return (new Article())->fill($result->fetch());
            }
            return false;
        } catch (\PDOException $e){
            error_log($e->getMessage());
            return false;
        }
    }

    public function getArticleById(int $id): Article|false {
        try {
            $sqlStatement = $this->pdo->prepare("SELECT * FROM articles WHERE id = ?");
            $result = $sqlStatement->execute([$id]);
            if ($result) {
                $row = $sqlStatement->fetch();
                if (!$row) {  // No article found
                    return false;
                }
                return (new Article())->fill($row);
            }
            return false;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    

    public function updateArticle(int $id, string $title, string $url): bool
    {
        try {
            $sqlStatement = $this->pdo->prepare(
                "UPDATE articles SET title = :title, url = :url, updated_at = :updatedAt WHERE id = :id"
            );
    
            $updated = $sqlStatement->execute([
                ':title' => $title,
                ':url' => $url,
                ':updatedAt' => date('Y-m-d H:i:s'),
                ':id' => $id
            ]);
    
            return $updated;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteArticleById(int $id): bool {
        try {
            // Delete associated votes first
            $stmt = $this->pdo->prepare("DELETE FROM votes WHERE article_id = :id");
            $stmt->execute([':id' => $id]);
            
            // Delete associated comments next
            $stmt = $this->pdo->prepare("DELETE FROM comments WHERE article_id = :id");
            $stmt->execute([':id' => $id]);
            
            // Finally, delete the article itself
            $stmt = $this->pdo->prepare("DELETE FROM articles WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    

    public function getArticlesByAuthor(int $authorId): array {
        try {
            $sql = "SELECT * FROM articles WHERE author_id = :authorId ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':authorId' => $authorId]);
            $rows = $stmt->fetchAll();
            
            $articles = [];
            foreach ($rows as $row) {
                $articles[] = (new \src\Models\Article())->fill($row);
            }
            return $articles;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
    
    

    public function getArticlesWithLimit(int $limit, int $offset, string $search = ''): array {
        try {
            // Construct a unique cache key for this query.
            $cacheKey = "articles:limit:$limit:offset:$offset:search:" . md5($search);
            
            // Connect to Redis using Predis with environment variables.
            $redis = new \Predis\Client([
                'scheme'   => 'tcp',
                'host'     => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
                'port'     => $_ENV['REDIS_PORT'] ?? 6379,
                'password' => $_ENV['REDIS_PASSWORD'] ?? null,
            ]);
            
            // Check if the result is cached.
            $cachedData = $redis->get($cacheKey);
            if ($cachedData !== null) {
                $rows = json_decode($cachedData, true);
                $articles = [];
                foreach ($rows as $row) {
                    $articles[] = (new \src\Models\Article())->fill($row);
                }
                return $articles;
            }
            
            // No cache, so query the database.
            if ($search !== '') {
                $sql = "SELECT * FROM articles WHERE title LIKE :search LIMIT :limit OFFSET :offset";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            } else {
                $sql = "SELECT * FROM articles LIMIT :limit OFFSET :offset";
                $stmt = $this->pdo->prepare($sql);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            
            // Cache the results for 300 seconds.
            $redis->setex($cacheKey, 300, json_encode($rows));
            
            $articles = [];
            foreach ($rows as $row) {
                $articles[] = (new \src\Models\Article())->fill($row);
            }
            return $articles;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
    
    
    // Article Comments

     /**
     * Add a new comment (or reply) to an article.
     * 
     * @param string $description
     * @param int $articleId
     * @param int $authorId
     * @param int|null $parentId  Optional parent comment ID (null for root comments)
     * @return Comment|false
     */
    public function addComment(string $description, int $articleId, int $authorId, ?int $parentId = null): Comment|false {
        try {
            $createdAt = date('Y-m-d H:i:s');
            $sql = "INSERT INTO comments (description, article_id, author_id, parent_id, created_at)
                    VALUES (:description, :articleId, :authorId, :parentId, :createdAt)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':description' => $description,
                ':articleId'   => $articleId,
                ':authorId'    => $authorId,
                ':parentId'    => $parentId,
                ':createdAt'   => $createdAt,
            ]);
            if ($stmt->rowCount() === 1) {
                $id = $this->pdo->lastInsertId();
                $stmt2 = $this->pdo->prepare("SELECT * FROM comments WHERE id = :id");
                $stmt2->execute([':id' => $id]);
                $row = $stmt2->fetch();
                if ($row) {
                    return (new Comment())->fill($row);
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Retrieve all comments for an article and build a nested tree.
     * 
     * @param int $articleId
     * @return array  Array of root Comment objects with nested replies.
     */
    public function getArticleComments(int $articleId): array {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM comments WHERE article_id = ? ORDER BY created_at ASC');
            $stmt->execute([$articleId]);
            $rows = $stmt->fetchAll();

            $comments = [];
            foreach ($rows as $row) {
                $comments[] = (new Comment())->fill($row);
            }

            // Build the tree structure:
            $tree = [];
            $children = [];
    
            foreach ($comments as $comment) {
                $parentId = $comment->getParentId();
                if ($parentId === null) {
                    $tree[$comment->getId()] = $comment;
                } else {
                    $children[$parentId][] = $comment;
                }
            }

            foreach ($tree as $id => $comment) {
                $this->buildCommentTree($comment, $children);
            }

            return array_values($tree);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Recursively attach child comments to a parent comment.
     * 
     * @param Comment $comment
     * @param array $children  Array of children grouped by parent ID.
     */
    private function buildCommentTree(Comment $comment, array &$children): void {
        $id = $comment->getId();
        if (isset($children[$id])) {
            $comment->setReplies($children[$id]);
            foreach ($children[$id] as $child) {
                $this->buildCommentTree($child, $children);
            }
        }
    }

    /**
     * Update a comment's description.
     *
     * @param int $commentId
     * @param string $description
     * @return bool
     */
    public function updateComment(int $commentId, string $description): bool {
        try {
            $stmt = $this->pdo->prepare("UPDATE comments SET description = :description WHERE id = :id");
            return $stmt->execute([
                ':description' => $description,
                ':id'          => $commentId,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Delete a comment by its ID.
     *
     * @param int $commentId
     * @return bool
     */
    public function deleteComment(int $commentId): bool {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = :id");
            return $stmt->execute([':id' => $commentId]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
   
   // Article Votes 

    public function getArticleVotes(int $articleId): int {
        try {
            $sqlStatement = $this->pdo->prepare("SELECT SUM(`value`) AS total_votes FROM votes WHERE article_id = :articleId");
            $sqlStatement->execute([':articleId' => $articleId]);
            $result = $sqlStatement->fetch();
            return (int)($result['total_votes'] ?? 0);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
    public function addVote(int $articleId, int $userId, int $value): bool {
        try {
            $sqlStatement = $this->pdo->prepare(
                "INSERT INTO votes (`value`, author_id, article_id) VALUES (:value, :userId, :articleId)"
            );
            return $sqlStatement->execute([
                ':value' => $value,
                ':userId' => $userId,
                ':articleId' => $articleId,
            ]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function deleteVote(int $articleId, int $userId): bool {
        try {
            $stmt = $this->pdo->prepare(
                "DELETE FROM votes WHERE article_id = :articleId AND author_id = :userId"
            );
            return $stmt->execute([
                ':articleId' => $articleId,
                ':userId'    => $userId,
            ]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    public function getUserVoteForArticle(int $articleId, int $userId): ?int {
        try {
            $sqlStatement = $this->pdo->prepare(
                "SELECT `value` FROM votes WHERE article_id = :articleId AND author_id = :userId"
            );
            $sqlStatement->execute([
                ':articleId' => $articleId,
                ':userId'    => $userId,
            ]);
            $result = $sqlStatement->fetch();
            return $result ? (int)$result['value'] : null;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function getArticleAuthor(string $articleId): ?User
    {
        $sqlStatement = $this->pdo->prepare("SELECT users.id, users.name, users.email, users.password_digest, users.profile_picture FROM users INNER JOIN articles a ON users.id = a.author_id WHERE a.id = ?;");
        $success = $sqlStatement->execute([$articleId]);
        if ($success && $sqlStatement->rowCount() !== 0) {
            return (new User())->fill($sqlStatement->fetch());
        } else {
            return null;
        }
    }

    /**
     * This will be helpful for pagination.
     */
    public function getCount(string $search): int
    {
        $sqlStatement = $this->pdo->prepare("SELECT COUNT(*) FROM articles WHERE title LIKE ?;");
        $sqlStatement->execute(["%$search%"]);
        return $sqlStatement->fetchColumn() ?? 0;
    }
}
