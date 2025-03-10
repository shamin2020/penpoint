<?php

namespace src\Models;

require_once 'Model.php';

class Comment extends Model
{
    private ?string $description = null;
    private ?int $creator_id = null;
    private ?int $article_id = null;
    private ?int $parent_id = null;
    private array $replies = [];

    public function __construct(
        ?int $id = null,
        ?string $description = null,
        ?int $article_id = null,
        ?int $creator_id = null,
        ?int $parent_id = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        parent::__construct($id, $createdAt, $updatedAt);
        $this->setDescription($description);
        $this->setArticleId($article_id);
        $this->setCreatorId($creator_id);
        $this->setParentId($parent_id);
    }

    public function getComment(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    public function getCreatorId(): ?int {
        return $this->creator_id;
    }

    public function setCreatorId(?int $creatorId): void {
        $this->creator_id = $creatorId;
    }

    public function getArticleId(): ?int {
        return $this->article_id;
    }

    public function setArticleId(?int $articleId): void {
        $this->article_id = $articleId;
    }

    public function getParentId(): ?int {
        return $this->parent_id;
    }

    public function setParentId(?int $parentId): void {
        $this->parent_id = $parentId;
    }

    public function getReplies(): array {
        return $this->replies;
    }

    public function setReplies(array $replies): void {
        $this->replies = $replies;
    }

    /**
     * Fill the model properties from an associative array.
     *
     * @param array $attributes  An associative array of attributes.
     * @return self
     */
    public function fill(array $attributes): self {
        foreach ($attributes as $attribute => $value) {
            switch ($attribute) {
                case 'id':
                    $this->setId($value);
                    break;
                case 'description':
                    $this->setDescription($value);
                    break;
                case 'author_id': 
                    $this->setCreatorId($value);
                    break;
                case 'article_id':
                    $this->setArticleId($value);
                    break;
                case 'parent_id':
                    $this->setParentId($value);
                    break;
                case 'created_at':
                    $this->setCreatedAt($value);
                    break;
                case 'updated_at':
                    $this->setUpdatedAt($value);
                    break;
                default:
                    // You may want to handle unknown attributes here.
                    break;
            }
        }
        return $this;
    }
}
