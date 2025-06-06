<?php



namespace humhub\modules\web\pwa\widgets;

use Yii;
use yii\base\WidgetEvent;
use yii\helpers\Url;
use humhub\components\Widget;
use humhub\modules\web\Module;
use humhub\modules\ui\view\components\View;

/**
 * Class LayoutHeader
 *
 * @package humhub\modules\ui\widgets
 */
class LayoutHeader extends Widget
{
    /**
     * Registers mobile app related Head Tags
     *
     * @param View $view
     */
    public static function registerHeadTags(View $view)
    {

        $view->registerMetaTag(['name' => 'theme-color', 'content' => Yii::$app->view->theme->variable('primary')]);
        $view->registerMetaTag(['name' => 'application-name', 'content' => Yii::$app->name]);

        // Apple/IOS headers
        // https://developer.apple.com/library/archive/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html
        $view->registerMetaTag(['name' => 'apple-mobile-web-app-title', 'content' => Yii::$app->name]);
        $view->registerMetaTag(['name' => 'mobile-web-app-capable', 'content' => 'yes']);
        $view->registerMetaTag(['name' => 'apple-mobile-web-app-status-bar-style', 'content' => Yii::$app->view->theme->variable('primary')]);

        $view->registerLinkTag(['rel' => 'manifest', 'href' => Url::to(['/web/pwa-manifest/index'])]);

        /** @var Module $module */
        $module = Yii::$app->getModule('web');
        if ($module->enableServiceWorker !== false) {
            static::registerServiceWorker($view);
        }
    }

    private static function registerServiceWorker(View $view)
    {
        $cacheId = Yii::$app->cache->getOrSet('service-worker-cache-id', function () {
            return time();
        });
        $serviceWorkUrl = Url::to(['/web/pwa-service-worker/index', 'v' => $cacheId]);
        $rootPath = Yii::getAlias('@web') . '/';

        $view->registerJs(<<<JS
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('$serviceWorkUrl', { scope: '$rootPath' })
                    .then(function (registration) {
                        if (typeof afterServiceWorkerRegistration === "function") {
                            // Allow Modules like `fcm-push` to register after registration
                            afterServiceWorkerRegistration(registration);
                        }
                    })
            }
JS
            , View::POS_READY, 'serviceWorkerInit');

    }

}
