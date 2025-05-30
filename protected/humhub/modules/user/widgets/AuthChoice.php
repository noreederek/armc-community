<?php



namespace humhub\modules\user\widgets;

use humhub\helpers\DeviceDetectorHelper;
use humhub\modules\user\authclient\BaseFormAuth;
use Yii;
use yii\authclient\ClientInterface;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;

class AuthChoice extends \yii\authclient\widgets\AuthChoice
{
    /**
     * Used to retrieve the auth clients in a static way
     * @var string
     */
    private static $authclientCollection = 'authClientCollection';

    /**
     * @var int number of clients to show without folding
     */
    public $maxShowClients = 2;

    /**
     * @var bool show auth button colors
     */
    public $showButtonColors = false;

    public $showOrDivider = false;

    /**
     * @inheritdoc
     */
    public $popupMode = false;

    /**
     * @var ClientInterface[] auth providers list.
     */
    private $_clients;

    /**
     * @param ClientInterface[] $clients auth providers
     */
    public function setClients(array $clients)
    {
        $this->_clients = $clients;
    }

    /**
     * @return ClientInterface[] auth providers
     */
    public function getClients()
    {
        if ($this->_clients === null) {
            $this->_clients = self::filterClients($this->defaultClients());
        }

        return $this->_clients;
    }

    /**
     * Returns default auth clients list.
     * @return bool
     * @throws InvalidConfigException
     */
    public static function hasClients()
    {
        $authClients = self::filterClients(Yii::$app->get(self::$authclientCollection)->getClients());

        return count($authClients) > 0;
    }

    /**
     * Filters out clients which need login form
     * @param $clients
     * @return BaseFormAuth[]
     */
    private static function filterClients($clients)
    {
        $result = [];
        foreach ($clients as $client) {

            // Don't show clients which need login form
            if (!$client instanceof BaseFormAuth) {
                $result[] = $client;
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function defaultBaseAuthUrl()
    {
        $params = $_GET;
        unset($params[$this->clientIdGetParamName]);
        $baseAuthUrl = array_merge(['/user/auth/external'], $params);

        return $baseAuthUrl;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (count($this->getClients()) > 0) {
            parent::init();
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeRun()
    {
        return parent::beforeRun() &&
            count($this->getClients()) > 0 &&
            !(DeviceDetectorHelper::isIosApp() && Yii::$app->params['humhub']['disableAuthChoicesIos']);
    }

    /**
     * Renders the main content, which includes all external services links.
     */
    protected function renderMainContent()
    {
        $clients = $this->getClients();
        $clientCount = count($clients);

        if ($clientCount == 0) {
            return;
        }

        $this->view->registerCssFile('@web-static/resources/user/authChoice.css');
        $this->view->registerJsFile('@web-static/resources/user/authChoice.js');

        echo Html::beginTag('div', ['class' => 'authChoice']);

        $i = 0;
        $extraCssClass = 'btn-sxm';

        foreach ($clients as $client) {
            $i++;
            if ($i == $this->maxShowClients + 1) {
                // Add more button
                echo Html::a('<i class="fa fa-angle-double-down" aria-hidden="true"></i>', '#', ['class' => 'btn btn-default pull-right btn-sxm btn-auth-choice-more']);

                // Div contains more auth clients
                echo Html::beginTag('div', ['class' => 'auth-choice-more-buttons']);
                $extraCssClass = 'btn-sm'; // further buttons small
            }
            $this->clientLink($client, null, ['class' => $extraCssClass]);
            echo "&nbsp;";
        }

        if ($i > $this->maxShowClients) {
            echo Html::endTag('div');
        }
        echo Html::endTag('div');

        if ($this->showOrDivider) {
            echo Html::tag('div', Html::tag('hr') . Html::tag('div', Yii::t('UserModule.base', 'or')), ['class' => 'or-container']);
        }
    }

    /**
     * @inheritdoc
     */
    public function clientLink($client, $text = null, array $htmlOptions = [])
    {
        $viewOptions = $client->getViewOptions();

        if (isset($viewOptions['widget'])) {
            parent::clientLink($client, $text, $htmlOptions);
            return;
        }

        if (isset($viewOptions['buttonBackgroundColor'])) {
            $textColor = (isset($viewOptions['buttonColor'])) ? $viewOptions['buttonColor'] : '#FFF';
            $btnStyle = Html::cssStyleFromArray(['color' => $textColor . '!important', 'background-color' => $viewOptions['buttonBackgroundColor'] . '!important']);
            $btnClasses = '.btn-ac-' . $client->getName() . ', .btn-ac-' . $client->getName() . ':hover, .btn-ac-' . $client->getName() . ':active, .btn-ac-' . $client->getName() . ':visited';

            if ($this->showButtonColors) {
                echo Html::style($btnClasses . ' {' . $btnStyle . '}');
            }
        }

        if (!isset($htmlOptions['class'])) {
            $htmlOption['class'] = '';
        }
        $htmlOptions['class'] .= ' ' . 'btn btn-default btn-ac-' . $client->getName();
        $htmlOptions['data-pjax-prevent'] = '';

        $icon = (isset($viewOptions['cssIcon'])) ? '<i class="' . $viewOptions['cssIcon'] . '" aria-hidden="true"></i>' : '';
        echo parent::clientLink($client, $icon . $client->getTitle(), $htmlOptions);

        return;
        parent::clientLink($client, $text, $htmlOptions);
    }

}
