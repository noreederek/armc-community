<?php



namespace humhub\modules\ui\filter\models;

use Yii;
use yii\base\Model;

abstract class Filter extends Model
{
    public const AUTO_LOAD_GET = 0;
    public const AUTO_LOAD_POST = 1;
    public const AUTO_LOAD_ALL = 2;

    /**
     * @var string can be used to overwrite the default formName used by [[load()]]
     */
    public $formName;

    /**
     * @var bool Whether or not to automatically load the filter state from request.
     */
    public $autoLoad = self::AUTO_LOAD_ALL;

    /**
     * @var bool True - if data was loaded at least one time
     */
    protected $isLoaded = false;

    abstract public function apply();

    public function init()
    {
        if (Yii::$app->request->isConsoleRequest) {
            return;
        }

        if ($this->autoLoad === static::AUTO_LOAD_ALL) {
            $this->load(Yii::$app->request->get());
            $this->load(Yii::$app->request->post());
        } elseif ($this->autoLoad === static::AUTO_LOAD_GET) {
            $this->load(Yii::$app->request->get());
        } elseif ($this->autoLoad === static::AUTO_LOAD_POST) {
            $this->load(Yii::$app->request->post());
        }
    }

    public function formName()
    {
        return $this->formName ?: parent::formName();
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            $this->isLoaded = true;
            return true;
        }

        return false;
    }

}
