<?php



namespace humhub\widgets;

use Yii;
use yii\helpers\Json;
use humhub\assets\PjaxAsset;

/**
 * Pjax Widget
 *
 * @author Luke
 */
class PjaxLayoutContent extends \humhub\components\Widget
{
    /**
     * @var array options passed to pjax scrpit
     */
    public $clientOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->clientOptions['pushRedirect'] = true;
        $this->clientOptions['replaceRedirect'] = true;
        $this->clientOptions['cache'] = false;
        $this->clientOptions['timeout'] = 5000;

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        PjaxAsset::register($view);

        $view->registerJsConfig('client.pjax', [
            'active' => self::isActive(),
            'options' => $this->clientOptions,
        ]);
    }

    public static function isActive()
    {
        return Yii::$app->params['enablePjax'];
    }

}
