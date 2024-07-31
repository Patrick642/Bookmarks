<?php
namespace src\Model\Bookmark;

use core\Model\Model;

final class BookmarkModel extends Model
{
    public BookmarkValidator $validator;

    public function __construct()
    {
        parent::__construct();
        $this->validator = new BookmarkValidator();
    }

    public function add(int $userId, string $label, string $url): bool
    {
        if (!$this->validator->validate(['label' => $label, 'url' => $url]))
            return false;

        $query = 'INSERT INTO bookmark(user_id, label, url, updated_at) VALUES (:user_id, :label, :url, :updated_at)';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':label', $label, \PDO::PARAM_STR);
        $stmt->bindValue(':url', $url, \PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', (new \DateTimeImmutable())->format('Y-m-d H:i:s'), \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function update(int $id, string $label, string $url): bool
    {
        if (!$this->validator->validate(['label' => $label, 'url' => $url]))
            return false;

        $query = 'UPDATE bookmark SET bookmark.label = :label, bookmark.url = :url, bookmark.updated_at = :updated_at WHERE bookmark.id = :id';

        $stmt = $this->db()->prepare($query);

        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':label', $label, \PDO::PARAM_STR);
        $stmt->bindValue(':url', $url, \PDO::PARAM_STR);
        $stmt->bindValue(':updated_at', (new \DateTimeImmutable())->format('Y-m-d H:i:s'), \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getUserId(int $bookmarkId): ?int
    {
        $query = 'SELECT bookmark.user_id FROM bookmark WHERE bookmark.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $bookmarkId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['user_id'];

        return $fetch ?? null;
    }

    public function getLabel(int $bookmarkId): ?string
    {
        $query = 'SELECT bookmark.label FROM bookmark WHERE bookmark.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $bookmarkId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['label'];

        return $fetch ?? null;
    }

    public function getUrl(int $bookmarkId): ?string
    {
        $query = 'SELECT bookmark.url FROM bookmark WHERE bookmark.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $bookmarkId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['url'];

        return $fetch ?? null;
    }

    public function getUpdatedAt(int $bookmarkId): ?string
    {
        $query = 'SELECT bookmark.updated_at FROM bookmark WHERE bookmark.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $bookmarkId, \PDO::PARAM_INT);
        $stmt->execute();
        $fetch = $stmt->fetch(\PDO::FETCH_ASSOC)['updated_at'];

        return $fetch ?? null;
    }

    public function delete(int $bookmarkId): bool
    {
        $query = 'DELETE FROM bookmark WHERE bookmark.id = :id';

        $stmt = $this->db()->prepare($query);
        $stmt->bindValue(':id', $bookmarkId, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function getIntervalByUserId(string $userId, int $start, int $end): array
    {
        $query = 'SELECT bookmark.* FROM bookmark WHERE bookmark.user_id = :user_id ORDER BY bookmark.updated_at DESC LIMIT :offset, :limit';

        $stmt = $this->db()->prepare($query);

        $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $start, \PDO::PARAM_INT);
        $stmt->bindParam(':limit', $end, \PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUserBookmarkCount(string $userId): int
    {
        $query = 'SELECT COUNT(bookmark.id) FROM bookmark WHERE bookmark.user_id = :user_id';

        $stmt = $this->db()->prepare($query);

        $stmt->execute([
            ':user_id' => $userId
        ]);

        return $stmt->fetchColumn();
    }
}