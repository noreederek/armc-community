<?php



namespace humhub\modules\installer\forms;

use Yii;
use yii\base\Model;

/**
 * Use Case Form
 *
 * @since 0.5
 */
class UseCaseForm extends Model
{
    /**
     * @var string use case
     */
    public $useCase;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['useCase'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'useCase' => Yii::t('InstallerModule.base', 'I want to use HumHub for:'),
        ];
    }

}
