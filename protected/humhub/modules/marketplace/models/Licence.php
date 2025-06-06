<?php



namespace humhub\modules\marketplace\models;

use humhub\modules\marketplace\components\LicenceManager;
use Yii;
use yii\base\Model;

class Licence extends Model
{
    /**
     * Licence types
     */
    public const LICENCE_TYPE_CE = 'community';
    public const LICENCE_TYPE_PRO = 'pro';

    /**
     * @var string the license type
     */
    public $type;

    /**
     * @var string the license key
     */
    public $licenceKey;

    /**
     * @var string name of the license
     */
    public $licencedTo;

    /**
     * @var int the number of maximum users
     */
    public $maxUsers;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->type = static::LICENCE_TYPE_CE;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['licenceKey', 'safe'],
            ['licenceKey', 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'licenceKey' => Yii::t('MarketplaceModule.base', 'License key'),
        ];
    }


    /**
     * Registers the license
     *
     * @return bool
     */
    public function register()
    {
        $result = LicenceManager::request('v1/pro/register', ['licenceKey' => $this->licenceKey]);

        if (empty($result) || !is_array($result) || !isset($result['status'])) {
            $this->addError('licenceKey', Yii::t('MarketplaceModule.base', 'Could not connect to license server!'));
            return false;
        }

        if ($result['status'] === 'ok') {
            return true;
        }

        LicenceManager::remove();
        $this->addError('licenceKey', Yii::t('MarketplaceModule.base', 'Could not update license. Error: ') . $result['message']);
        return false;
    }

}
