<?php



namespace humhub\modules\space\modules\manage\models;

use humhub\modules\user\components\CheckPasswordValidator;
use Yii;
use yii\base\Model;

/**
 * Form Model for Space Deletion
 *
 * @since 0.5
 */
class DeleteForm extends Model
{
    /**
     * @var string the space name to check
     */
    public $spaceName;


    /**
     * @var string the space name given by the user
     */
    public $confirmSpaceName;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['confirmSpaceName', 'required'],
            ['confirmSpaceName', 'compare', 'compareValue' => $this->spaceName,
                'message' => Yii::t('SpaceModule.base', 'Incorrect name, try again.')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'confirmSpaceName' => Yii::t('SpaceModule.base', 'Space Name'),
        ];
    }

}
