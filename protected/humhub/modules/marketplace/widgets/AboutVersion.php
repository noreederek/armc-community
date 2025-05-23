<?php



namespace humhub\modules\marketplace\widgets;

use humhub\components\Widget;
use humhub\modules\marketplace\models\Licence;
use humhub\modules\marketplace\Module;
use Yii;

class AboutVersion extends Widget
{
    /**
     * @inheritDoc
     */
    public function run()
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('marketplace');

        $licence = $module->getLicence();

        if ($licence->type === Licence::LICENCE_TYPE_PRO) {
            if (isset(Yii::$app->params['hosting'])) {
                return $this->render('about_version_pro_cloud', ['licence' => $licence]);
            } else {
                return $this->render('about_version_pro', ['licence' => $licence]);
            }
        } else {
            return $this->render('about_version');
        }
    }

}
