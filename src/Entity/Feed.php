<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedRepository::class)]
class Feed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $author_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author_nickname = null;

    #[ORM\Column(length: 255)]
    private ?string $author_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author_picture = null;

    #[ORM\Column]
    private ?int $post_id = null;

    #[ORM\Column(length: 255)]
    private ?string $post_filename = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $post_description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $post_created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getAuthorNickname(): ?string
    {
        return $this->author_nickname;
    }

    public function setAuthorNickname(?string $author_nickname): self
    {
        $this->author_nickname = $author_nickname;

        return $this;
    }

    public function getAuthorName(): ?string
    {
        return $this->author_name;
    }

    public function setAuthorName(string $author_name): self
    {
        $this->author_name = $author_name;

        return $this;
    }

    public function getAuthorPicture(): ?string
    {
        return $this->author_picture;
    }

    public function setAuthorPicture(?string $user_picture): self
    {
        $this->author_picture = $user_picture;

        return $this;
    }

    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    public function setPostId(int $post_id): self
    {
        $this->post_id = $post_id;

        return $this;
    }

    public function getPostFilename(): ?string
    {
        return $this->post_filename;
    }

    public function setPostFilename(string $post_filename): self
    {
        $this->post_filename = $post_filename;

        return $this;
    }

    public function getPostDescription(): ?string
    {
        return $this->post_description;
    }

    public function setPostDescription(?string $post_description): self
    {
        $this->post_description = $post_description;

        return $this;
    }

    public function getPostCreatedAt(): ?\DateTimeImmutable
    {
        return $this->post_created_at;
    }

    public function setPostCreatedAt(\DateTimeImmutable $post_created_at): self
    {
        $this->post_created_at = $post_created_at;

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    public function setAuthorId(int $author_id): self
    {
        $this->author_id = $author_id;

        return $this;
    }
}
