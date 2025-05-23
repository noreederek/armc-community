<?php



namespace humhub\modules\admin\widgets;

use humhub\components\Widget;
use Yii;

/**
 * MaintenanceModeWarning shows a snippet in the dashboard
 * when maintenance mode is active.
 *
 * @package humhub\modules\admin\widgets
 */
class MaintenanceModeWarning extends Widget
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!Yii::$app->settings->get('maintenanceMode')) {
            return;
        }

        return $this->render('maintenanceModeWarning');
    }

}
