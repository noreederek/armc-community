<?php



namespace humhub\modules\user\widgets;

use humhub\components\Widget;
use humhub\modules\user\models\User;

/**
 * PeopleIcons shows footer icons for people cards
 *
 * @since 1.9
 * @author Luke
 */
class PeopleIcons extends Widget
{
    /**
     * @var User
     */
    public $user;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('peopleIcons', [
            'user' => $this->user,
        ]);
    }

}
