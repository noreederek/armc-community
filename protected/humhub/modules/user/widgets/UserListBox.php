<?php



namespace humhub\modules\user\widgets;

use Yii;
use yii\base\Widget;
use yii\data\Pagination;
use yii\db\ActiveQuery;

/**
 * UserListBox returns the content of the user list modal
 *
 * Example Action:
 *
 * ```php
 * public actionUserList() {
 *       $query = User::find();
 *       $query->where(...);
 *
 *       $title = "Some Users";
 *
 *       return $this->renderAjaxContent(UserListBox::widget(['query' => $query, 'title' => $title]));
 * }
 * ```
 *
 * @author luke
 */
class UserListBox extends Widget
{
    /**
     * @var ActiveQuery
     */
    public $query;

    /**
     * @var string title of the box (not html encoded!)
     */
    public $title = 'Users';

    /**
     * @var int displayed users per page
     */
    public $pageSize = null;

    public bool $hideOnlineStatus = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->pageSize === null) {
            $this->pageSize = Yii::$app->getModule('user')->userListPaginationSize;
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $countQuery = clone $this->query;
        $pagination = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $this->pageSize]);
        $this->query->offset($pagination->offset)->limit($pagination->limit);

        return $this->render("userListBox", [
            'title' => $this->title,
            'users' => $this->query->all(),
            'pagination' => $pagination,
            'hideOnlineStatus' => $this->hideOnlineStatus,
        ]);
    }

}
