<?php



namespace humhub\modules\web;

use Yii;
use humhub\modules\web\security\controllers\ReportController;
use humhub\modules\web\pwa\controllers\ManifestController;
use humhub\modules\web\pwa\controllers\OfflineController;
use humhub\modules\web\pwa\controllers\ServiceWorkerController;

/**
 * This module provides general web components.
 *
 * @since 1.4
 */
class Module extends \humhub\components\Module
{
    /**
     * @inheritdoc
     */
    public $isCoreModule = true;

    /**
     * @var mixed web security settings
     */
    public $security;

    /**
     * @since 1.8
     * @var bool Disable Service Worker and PWA Support
     */
    public $enableServiceWorker = true;

    /**
     * @inheritdoc
     */
    public $controllerMap = [
        'pwa-manifest' => ManifestController::class,
        'pwa-offline' => OfflineController::class,
        'pwa-service-worker' => ServiceWorkerController::class,
        'security-report' => ReportController::class,
    ];

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Yii::t('WebModule.base', 'Web');
    }

}
