<?php



namespace humhub\modules\admin\libs;

use Exception;
use humhub\modules\marketplace\Module;
use Yii;

/**
 * HumHubAPI provides access to humhub.com for fetching available modules or latest version.
 *
 * @author luke
 */
class HumHubAPI
{
    /**
     * HumHub API
     *
     * @param string $action
     * @param array $params
     * @return array
     */
    public static function request($action, $params = [])
    {
        if (!Yii::$app->params['humhub']['apiEnabled'] || !Yii::$app->hasModule('marketplace')) {
            return [];
        }

        try {
            /** @var Module $marketplace */
            $marketplace = Yii::$app->getModule('marketplace');

            $response = $marketplace->getHumHubApi()->get($action)->addData($params)->send();
            return $response->getData();
        } catch (Exception $ex) {
            Yii::error('Could not parse HumHub API response! ' . $ex->getMessage());
            return [];
        }
    }

    /**
     * Fetch latest HumHub version online
     *
     * @return string latest HumHub Version
     */
    public static function getLatestHumHubVersion($useCache = true)
    {
        $latestVersion = Yii::$app->cache->get('latestVersion');
        if (!$useCache || $latestVersion === false) {
            $info = self::request('v1/modules/getLatestVersion');

            if (isset($info['latestVersion'])) {
                $latestVersion = $info['latestVersion'];
            }

            Yii::$app->cache->set('latestVersion', $latestVersion);
        }

        return $latestVersion;
    }

}
