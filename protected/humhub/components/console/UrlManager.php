<?php


namespace humhub\components\console;

use humhub\components\InstallationState;
use Yii;

/**
 * @inheritdoc
 */
class UrlManager extends \humhub\components\UrlManager
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $urlParts = parse_url($this->getConfiguredBaseUrl());

        $this->setBaseUrl($urlParts['path'] ?? '');

        $hostInfo = $urlParts['scheme'] . '://' . $urlParts['host'];
        if (isset($urlParts['port'])) {
            $hostInfo .= ':' . $urlParts['port'];
        }
        $this->setHostInfo($hostInfo);
        $this->setScriptUrl($this->getBaseUrl() . ($this->getScriptUrl() ?: '/index.php'));

        parent::init();
    }

    private function getConfiguredBaseUrl()
    {
        if (Yii::$app->installationState->hasState(InstallationState::STATE_DATABASE_CREATED)) {
            $baseUrl = Yii::$app->settings->get('baseUrl');
            if (!empty($baseUrl)) {
                return $baseUrl;
            }
        }

        return 'http://localhost';
    }
}
