<?php



namespace humhub\modules\installer;

use Exception;
use humhub\components\InstallationState;
use Yii;
use yii\console\Application;
use yii\helpers\Url;
use yii\web\HttpException;

/**
 * InstallerModule provides an web installation interface for the applcation
 *
 * @since 0.5
 */
class Module extends \humhub\components\Module
{
    /**
     * @event on configuration steps init
     */
    public const EVENT_INIT_CONFIG_STEPS = 'steps';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'humhub\modules\installer\controllers';

    /**
     * @var bool enable auto setup
     */
    public bool $enableAutoSetup = false;

    /**
     * Array of config steps
     *
     * @var array
     */
    public $configSteps = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (Yii::$app instanceof Application) {
            return;
        }

        $this->layout = '@humhub/modules/installer/views/layouts/main.php';
        $this->initConfigSteps();
        $this->sortConfigSteps();
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {

        // Block installer, when it's marked as installed
        if (Yii::$app->installationState->hasState(InstallationState::STATE_INSTALLED)) {
            throw new HttpException(500, 'HumHub is already installed!');
        }

        Yii::$app->controller->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    /**
     * Checks if database connections works
     *
     * @return bool state of database connection
     */
    public function checkDBConnection()
    {

        try {
            // call setActive with true to open connection.
            Yii::$app->db->open();
            // return the current connection state.
            return Yii::$app->db->getIsActive();
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * Checks if the application is already configured.
     */
    public function isConfigured()
    {
        if (Yii::$app->settings->get('secret') == '') {
            return false;
        }

        return true;
    }

    protected function initConfigSteps()
    {
        /**
         * Step:  Basic Configuration
         */
        $this->configSteps['basic'] = [
            'sort' => 100,
            'url' => Url::to(['/installer/config/basic']),
            'isCurrent' => function () {
                return (Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'basic');
            },
        ];

        /**
         * Step: Localisation
         */
        $this->configSteps['localisation'] = [
            'sort' => 130,
            'url' => Url::to(['/installer/config/localisation']),
            'isCurrent' => function () {
                return (Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'localisation');
            },
        ];

        /**
         * Step: Use Case
         */
        $this->configSteps['usecase'] = [
            'sort' => 150,
            'url' => Url::to(['/installer/config/use-case']),
            'isCurrent' => function () {
                return (Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'use-case');
            },
        ];

        /**
         * Step: Security
         */
        $this->configSteps['security'] = [
            'sort' => 200,
            'url' => Url::to(['/installer/config/security']),
            'isCurrent' => function () {
                return (Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'security');
            },
        ];

        /**
         * Step: Sample Data
         */
        $this->configSteps['modules'] = [
            'sort' => 300,
            'url' => Url::to(['/installer/config/modules']),
            'isCurrent' => function () {
                return (Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'modules');
            },
        ];

        /**
         * Step: Setup Admin User
         */
        $this->configSteps['admin'] = [
            'sort' => 400,
            'url' => Url::to(['/installer/config/admin']),
            'isCurrent' => function () {
                return (Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'admin');
            },
        ];

        /**
         * Step: Sample Data
         */
        $this->configSteps['sample-data'] = [
            'sort' => 450,
            'url' => Url::to(['/installer/config/sample-data']),
            'isCurrent' => function () {
                return (Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'sample-data');
            },
        ];

        /**
         * Step:  Setup Admin User
         */
        $this->configSteps['finish'] = [
            'sort' => 1000,
            'url' => Url::to(['/installer/config/finish']),
            'isCurrent' => function () {
                return (Yii::$app->controller->id == 'config' && Yii::$app->controller->action->id == 'finish');
            },
        ];

        $this->trigger(self::EVENT_INIT_CONFIG_STEPS);
    }

    /**
     * Get Next Step
     */
    public function getNextConfigStepUrl()
    {
        $foundCurrent = false;
        foreach ($this->configSteps as $step) {
            if ($foundCurrent) {
                return $step['url'];
            }

            if (call_user_func($step['isCurrent'])) {
                $foundCurrent = true;
            }
        }

        return $this->configSteps[0]['url'];
    }

    /**
     * Sorts all configSteps on sort attribute
     */
    protected function sortConfigSteps()
    {
        usort($this->configSteps, function ($a, $b) {
            return ($a['sort'] > $b['sort']) ? 1 : -1;
        });
    }
}
