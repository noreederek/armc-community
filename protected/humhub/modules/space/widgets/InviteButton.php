<?php


namespace humhub\modules\space\widgets;

use humhub\modules\space\models\Space;
use humhub\modules\space\permissions\InviteUsers;
use yii\base\Widget;

/**
 * InviteButton class
 *
 * @author luke
 * @since 0.11
 */
class InviteButton extends Widget
{
    /**
     * @var Space
     */
    public $space;

    /**
     * @inheritDoc
     */
    public function run()
    {
        if (!$this->space->getPermissionManager()->can(new InviteUsers())) {
            return;
        }

        return $this->render('inviteButton', ['space' => $this->space]);
    }

}
