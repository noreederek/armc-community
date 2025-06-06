<?php



namespace humhub\components;

use humhub\modules\content\components\ContentContainerActiveRecord;

/**
 * UrlManager
 *
 * @since 1.3
 * @author Luke
 */
class UrlManager extends \yii\web\UrlManager
{
    /**
     * @var ContentContainerActiveRecord
     */
    public static $cachedLastContainerRecord;

    public function init()
    {
        parent::init();

        $this->addRules([
            ['class' => WellKnownUrlRule::class],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function createUrl($params)
    {
        $params = (array)$params;

        if (isset($params['container']) && $params['container'] instanceof ContentContainerActiveRecord) {
            $params['contentContainer'] = $params['container'];
            unset($params['container']);
        }

        if (isset($params['contentContainer']) && $params['contentContainer'] instanceof ContentContainerActiveRecord) {
            $params['cguid'] = $params['contentContainer']->guid;
            static::$cachedLastContainerRecord = $params['contentContainer'];
            unset($params['contentContainer']);
        }

        return parent::createUrl($params);
    }
}
