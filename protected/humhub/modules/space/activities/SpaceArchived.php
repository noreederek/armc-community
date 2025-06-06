<?php



namespace humhub\modules\space\activities;

use humhub\modules\content\models\Content;
use Yii;
use humhub\modules\activity\components\BaseActivity;
use humhub\modules\activity\interfaces\ConfigurableActivityInterface;

/**
 * Description of MemberAdded
 *
 * @author luke
 */
class SpaceArchived extends BaseActivity implements ConfigurableActivityInterface
{
    /**
     * @inheritdoc
     */
    public $viewName = 'spaceArchived';

    /**
     * @inheritdoc
     */
    public $moduleId = 'space';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->visibility = Content::VISIBILITY_PRIVATE;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('SpaceModule.activities', 'Space has been archived');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('SpaceModule.activities', 'Whenever a space is archived.');
    }
}
