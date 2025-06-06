<?php



namespace humhub\modules\user\widgets;

use humhub\modules\space\models\Space;
use humhub\modules\space\models\Membership;
use humhub\modules\user\models\User;
use yii\base\Widget;

/**
 * UserSpaces widget shows all users public and active spaces in sidebar.
 *
 * @since 0.5
 * @author Luke
 */
class UserSpaces extends Widget
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var int maximum spaces to display
     */
    public $maxSpaces = 30;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $query = Membership::getUserSpaceQuery($this->user)
            ->andWhere(['!=', 'space.visibility', Space::VISIBILITY_NONE])
            ->andWhere(['space.status' => Space::STATUS_ENABLED]);

        $showMoreLink = ($query->count() > $this->maxSpaces);

        return $this->render('userSpaces', [
            'user' => $this->user,
            'spaces' => $query->limit($this->maxSpaces)->all(),
            'showMoreLink' => $showMoreLink,
        ]);
    }

}
