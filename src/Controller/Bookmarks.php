<?php
namespace src\Controller;

use core\Controller;
use src\Model\Bookmark\BookmarkModel;
use src\Model\User\UserModel;

final class Bookmarks extends Controller
{
    private const MAX_BOOKMARKS_PER_PAGE = 20;

    private BookmarkModel $bookmarkModel;
    private UserModel $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->bookmarkModel = new BookmarkModel();
        $this->userModel = new UserModel();
    }

    public function dashboard(): void
    {
        if (!$this->userModel->getIsValid($this->session->getUserId()))
            $this->redirect('/complete_registration');

        $this->view->get('bookmarks/dashboard.phtml', [
            'pageTitle' => 'Dashboard - Bookmarks',
            'userId' => $this->session->getUserId(),
            'isPublic' => $this->userModel->getIsPublic($this->session->getUserId()),
            'bookmarks' => $this->bookmarkModel->getIntervalByUserId($this->session->getUserId(), 0, self::MAX_BOOKMARKS_PER_PAGE),
            'userHasMoreBookmarks' => ($this->bookmarkModel->getUserBookmarkCount($this->session->getUserId()) > self::MAX_BOOKMARKS_PER_PAGE) ? true : false,
            'maxLabelLength' => $this->bookmarkModel->validator::MAX_LABEL_LENGTH,
            'maxUrlLength' => $this->bookmarkModel->validator::MAX_URL_LENGTH,
        ]);
    }

    public function shared(): void
    {
        if (!$this->requiredInputs('GET', ['u']))
            throw new \ErrorException('Not found', 404);

        $userId = $this->userModel->getIdByUsername($this->dataUtility->sanitizeInput($_GET['u']));

        if ($userId === null)
            throw new \ErrorException('Not found', 404);

        if ($this->session->getUserId() == $userId)
            $this->redirect('/dashboard');

        $this->view->get('bookmarks/shared.phtml', [
            'pageTitle' => $this->userModel->getUsername($userId) . ' - Bookmarks',
            'userId' => $userId,
            'isPublic' => $this->userModel->getIsPublic($userId),
            'username' => $this->userModel->getUsername($userId),
            'bookmarks' => $this->userModel->getIsPublic($userId) ? $this->bookmarkModel->getIntervalByUserId($userId, 0, self::MAX_BOOKMARKS_PER_PAGE) : [],
            'userHasMoreBookmarks' => ($this->bookmarkModel->getUserBookmarkCount($userId) > self::MAX_BOOKMARKS_PER_PAGE) ? true : false
        ]);
    }

    public function addBookmark(): void
    {
        if (!$this->requiredInputs('POST', ['label', 'url']))
            $this->jsonEncode(success: false, message: 'Not all required fields are filled.');

        $label = $this->dataUtility->sanitizeInput($_POST['label']);
        $url = $this->dataUtility->addProtocol($this->dataUtility->sanitizeInput($_POST['url']));

        if ($this->bookmarkModel->add($this->session->getUserId(), $label, $url))
            $this->jsonEncode();

        $this->jsonEncode(success: false, message: $this->bookmarkModel->validator->getError());
    }

    public function editBookmark(): void
    {
        if (!$this->requiredInputs('POST', ['bookmark_id', 'label', 'url']))
            $this->jsonEncode(success: false, message: 'Not all required data is set.');

        $bookmarkId = $this->dataUtility->sanitizeInput($_POST['bookmark_id']);
        $bookmarkLabel = $this->dataUtility->sanitizeInput($_POST['label']);
        $bookmarkUrl = $this->dataUtility->addProtocol($this->dataUtility->sanitizeInput($_POST['url']));

        if ($this->bookmarkModel->getUserId($bookmarkId) !== $this->session->getUserId())
            throw new \ErrorException('You do not have permission to perform this action.', 403);

        if ($this->bookmarkModel->update($bookmarkId, $bookmarkLabel, $bookmarkUrl))
            $this->jsonEncode();

        $this->jsonEncode(success: false, message: $this->bookmarkModel->validator->getError());
    }

    public function deleteBookmark(): void
    {
        if (!$this->requiredInputs('POST', ['bookmark_id']))
            $this->jsonEncode(success: false, message: 'Bookmark ID is not set.');

        $bookmarkId = filter_var($_POST['bookmark_id'], FILTER_SANITIZE_NUMBER_INT);

        if ($this->bookmarkModel->getUserId($bookmarkId) !== $this->session->getUserId())
            throw new \ErrorException('You do not have permission to perform this action.', 403);

        if ($this->bookmarkModel->delete($bookmarkId))
            $this->jsonEncode();

        $this->jsonEncode(success: false, message: $this->bookmarkModel->validator->getError());
    }

    public function getBookmark(): void
    {
        if (!$this->requiredInputs('GET', ['bookmark_id']))
            $this->jsonEncode(success: false, message: 'Bookmark ID is not set.');

        $bookmarkId = filter_var($_GET['bookmark_id'], FILTER_SANITIZE_NUMBER_INT);

        if ($this->bookmarkModel->getUserId($bookmarkId) !== $this->session->getUserId())
            throw new \ErrorException('You do not have permission to perform this action.', 403);

        $this->jsonEncode([
            'label' => $this->bookmarkModel->getLabel($bookmarkId),
            'url' => $this->bookmarkModel->getUrl($bookmarkId)
        ]);
    }

    public function changeBookmarksPrivacy(): void
    {
        if (!$this->requiredInputs('POST', ['bool']))
            $this->jsonEncode(success: false);

        $bool = filter_var($_POST['bool'], FILTER_VALIDATE_BOOLEAN);

        if ($this->userModel->updateVisibility($this->session->getUserId(), $bool))
            $this->jsonEncode();

        $this->jsonEncode(success: false);
    }

    public function getMoreBookmarks(): void
    {
        if (!$this->requiredInputs('GET', ['user_id', 'offset']))
            $this->jsonEncode(success: false);

        $userId = $this->dataUtility->sanitizeInput($_GET['user_id']);
        $offset = $this->dataUtility->sanitizeInput($_GET['offset']);

        $render = '';

        if ($this->session->getUserId() != $userId && !$this->userModel->getIsPublic($userId))
            throw new \ErrorException('', 403);

        foreach ($this->bookmarkModel->getIntervalByUserId($userId, $offset, self::MAX_BOOKMARKS_PER_PAGE) as $r) {
            $render .= $this->view->getString('inc/bookmark.phtml', [
                'bookmark' => $r
            ]);
        }

        $this->jsonEncode([
            'render' => $render,
            'isLast' => ($this->bookmarkModel->getUserBookmarkCount($userId) <= $offset + self::MAX_BOOKMARKS_PER_PAGE) ? true : false
        ]);
    }
}