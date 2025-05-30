<?php



namespace humhub\widgets;

use humhub\modules\user\helpers\AuthHelper;
use Yii;

/**
 * TopMenuRightStackWidget holds items like search (right part)
 *
 * @since 0.6
 * @author Luke
 */
class TopMenuRightStack extends BaseStack
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->addWidget(MetaSearchWidget::class, [], ['sortOrder' => 100]);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // Don't show stack if guest access is disabled and user is not logged in
        if (Yii::$app->user->isGuest && !AuthHelper::isGuestAccessEnabled()) {
            return '';
        }

        return parent::run();
    }

}
