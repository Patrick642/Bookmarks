<?php
namespace src\Controller;

use core\Controller, core\View, src\Model\Bookmark, src\Model\User;

class Dashboard extends Controller
{
    public function index(): void
    {
        (new View)->getView('dashboard', [
            'page_title' => 'Dashboard - Bookmarks',
            'is_public' => (new User)->isPublic($_SESSION['user_id']),
            'data' => (new Bookmark)->getAllByUserId($_SESSION['user_id'])
        ]);
    }

    public function addBookmark(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;
        try {
            if ($this->isEmpty($_POST['label']) || $this->isEmpty($_POST['url']))
                throw new \Exception('Not all required fields are filled.');

            $label = $this->sanitizeInput($_POST['label']);
            $url = $this->sanitizeInput($_POST['url']);

            (new Bookmark)->add($_SESSION['user_id'], $label, $url);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);

            exit;
        }
    }

    public function deleteBookmark(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        try {
            if ($this->isEmpty($_POST['bookmark_id']))
                throw new \Exception('Bookmark ID is not set.');

            $bookmark_id = filter_var($_POST['bookmark_id'], FILTER_SANITIZE_NUMBER_INT);

            (new Bookmark)->delete($bookmark_id, $_SESSION['user_id']);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);

            exit;
        }
    }

    public function editBookmark(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        try {
            if ($this->isEmpty($_POST['bookmark_id']) || $this->isEmpty($_POST['title']) || $this->isEmpty($_POST['url']))
                throw new \Exception('Not all required data is set.');

            $bookmark_id = $this->sanitizeInput($_POST['bookmark_id']);
            $bookmark_title = $this->sanitizeInput($_POST['title']);
            $bookmark_url = $this->sanitizeInput($_POST['url']);

            (new Bookmark)->edit($bookmark_id, $bookmark_title, $bookmark_url, $_SESSION['user_id']);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);

            exit;
        }
    }

    public function getBookmark(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        try {
            if ($this->isEmpty($_GET['bookmark_id']))
                throw new \Exception('Bookmark ID is not set.');

            $bookmark_id = filter_var($_GET['bookmark_id'], FILTER_SANITIZE_NUMBER_INT);

            $bookmark = (new Bookmark)->get($bookmark_id, $_SESSION['user_id']);

            echo json_encode([
                'status' => 'success',
                'title' => $bookmark['title'],
                'url' => $bookmark['url']
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);

            exit;
        }
    }

    public function changeBookmarksPrivacy(): void
    {
        if (!$this->request->isXMLHttpRequest())
            exit;

        if ($this->isEmpty($_POST['bool']))
            exit;

        $bool = filter_var($this->sanitizeInput($_POST['bool']), FILTER_VALIDATE_BOOLEAN);

        echo json_encode([
            'status' => ((new User)->setVisibility($_SESSION['user_id'], $bool)) ? 'success' : 'error'
        ]);
    }
}