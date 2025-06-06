<?php



namespace humhub\modules\friendship\widgets;

use Yii;
use humhub\modules\friendship\models\Friendship;
use yii\base\Widget;

/**
 * A panel which shows users friends in sidebar
 *
 * @since 1.1
 * @author luke
 */
class FriendsPanel extends Widget
{
    /**
     * @var User the target user
     */
    public $user;

    /**
     * @var int limit of friends to display
     */
    public $limit = 30;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!Yii::$app->getModule('friendship')->isFriendshipEnabled()) {
            return;
        }

        $querz = Friendship::getFriendsQuery($this->user);

        $totalCount = $querz->count();
        $friends = $querz->limit($this->limit)->all();

        return $this->render('friendsPanel', [
            'friends' => $friends,
            'friendsShowLimit' => $this->limit,
            'totalCount' => $totalCount,
            'limit' => $this->limit,
            'user' => $this->user,
        ]);
    }

}
