<?php
namespace src\Model;

use core\Model, PDO;

class Bookmark extends Model
{
    /**
     * add
     *
     * @param  mixed $user_id
     * @param  mixed $label
     * @param  mixed $url
     * @return bool
     */
    public function add(string $user_id, string $label, string $url): bool
    {
        $query = $this->db()->prepare('INSERT INTO bookmark(user_id, title, url) VALUES(:user_id, :title, :url)');

        return $query->execute([
            ':user_id' => $user_id,
            ':title' => $label,
            ':url' => $url
        ]);
    }

    /**
     * get
     *
     * @param  mixed $bookmark_id
     * @param  mixed $user_id
     * @return array
     */
    public function get(string $bookmark_id, string $user_id): array
    {
        $query = $this->db()->prepare('SELECT bookmark.title, bookmark.url FROM bookmark WHERE bookmark.id = :bookmark_id AND bookmark.user_id = :user_id');

        $query->execute([
            ':bookmark_id' => $bookmark_id,
            ':user_id' => $user_id
        ]);

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * edit
     *
     * @param  mixed $bookmark_id
     * @param  mixed $title
     * @param  mixed $url
     * @param  mixed $user_id
     * @return bool
     */
    public function edit(string $bookmark_id, string $title, string $url, string $user_id): bool
    {
        $query = $this->db()->prepare('UPDATE bookmark SET bookmark.title = :title, bookmark.url = :url WHERE bookmark.id = :bookmark_id AND bookmark.user_id = :user_id');

        return $query->execute([
            ':bookmark_id' => $bookmark_id,
            ':title' => $title,
            ':url' => $url,
            'user_id' => $user_id
        ]);
    }

    /**
     * delete
     *
     * @param  mixed $bookmark_id
     * @param  mixed $user_id
     * @return bool
     */
    public function delete(int $bookmark_id, string $user_id): bool
    {
        $query = $this->db()->prepare('DELETE FROM bookmark WHERE bookmark.id = :bookmark_id AND bookmark.user_id = :user_id');

        return $query->execute([
            ':bookmark_id' => $bookmark_id,
            ':user_id' => $user_id
        ]);
    }

    /**
     * getAllByUserId
     *
     * @param  mixed $user_id
     * @return array
     */
    public function getAllByUserId(string $user_id): array
    {
        $query = $this->db()->prepare('SELECT bookmark.id, bookmark.title, bookmark.url FROM bookmark WHERE bookmark.user_id = :user_id');

        $query->execute([
            ':user_id' => $user_id
        ]);

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}