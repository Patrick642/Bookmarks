<?php
namespace src\Controller;

use core\Controller, src\Model\BookmarkModel, src\Model\UserModel;

class Dashboard extends Controller
{
    private BookmarkModel $bookmark_model;
    private UserModel $user_model;

    public function __construct()
    {
        parent::__construct();
        $this->bookmark_model = new BookmarkModel();
        $this->user_model = new UserModel();
    }

    public function index(): void
    {
        $this->view->get('dashboard/index.phtml', [
            'page_title' => 'Dashboard - Bookmarks',
            'is_public' => $this->user_model->isPublic($_SESSION['user_id']),
            'data' => $this->bookmark_model->getAllByUserId($_SESSION['user_id'])
        ]);
    }

    public function addBookmark(): void
    {
        if (!$this->request->isXMLHttpRequest())
            throw new \ErrorException('', 403);

        try {
            if (!$this->formFields('POST', ['label', 'url']))
                throw new \Exception('Not all required fields are filled.');

            $label = $this->sanitizeInput($_POST['label']);
            $url = $this->sanitizeInput($_POST['url']);

            $this->bookmark_model->add($_SESSION['user_id'], $label, $url);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteBookmark(): void
    {
        if (!$this->request->isXMLHttpRequest())
            throw new \ErrorException('', 403);

        try {
            if (!$this->formFields('POST', ['bookmark_id']))
                throw new \Exception('Bookmark ID is not set.');

            $bookmark_id = filter_var($_POST['bookmark_id'], FILTER_SANITIZE_NUMBER_INT);

            $this->bookmark_model->delete($bookmark_id, $_SESSION['user_id']);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function editBookmark(): void
    {
        if (!$this->request->isXMLHttpRequest())
            throw new \ErrorException('', 403);

        try {
            if (!$this->formFields('POST', ['bookmark_id', 'title', 'url']))
                throw new \Exception('Not all required data is set.');

            $bookmark_id = $this->sanitizeInput($_POST['bookmark_id']);
            $bookmark_title = $this->sanitizeInput($_POST['title']);
            $bookmark_url = $this->sanitizeInput($_POST['url']);

            $this->bookmark_model->edit($bookmark_id, $bookmark_title, $bookmark_url, $_SESSION['user_id']);

            echo json_encode([
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getBookmark(): void
    {
        if (!$this->request->isXMLHttpRequest())
            throw new \ErrorException('', 403);

        try {
            if (!$this->formFields('GET', ['bookmark_id']))
                throw new \Exception('Bookmark ID is not set.');

            $bookmark_id = filter_var($_GET['bookmark_id'], FILTER_SANITIZE_NUMBER_INT);

            $bookmark = $this->bookmark_model->get($bookmark_id, $_SESSION['user_id']);

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
        }
    }

    public function changeBookmarksPrivacy(): void
    {
        if (!$this->request->isXMLHttpRequest())
            throw new \ErrorException('', 403);

        if (!$this->formFields('POST', ['bool']))
            echo json_encode(['status' => 'error']);

        $bool = $this->sanitizeInput($_POST['bool']);

        echo json_encode([
            'status' => ($this->user_model->setVisibility($_SESSION['user_id'], $bool)) ? 'success' : 'error'
        ]);
    }
}