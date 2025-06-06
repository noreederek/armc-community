<?php



namespace humhub\modules\installer\forms;

use Yii;
use yii\base\Model;

/**
 * Sample Data Form
 *
 * @since 0.5
 */
class SampleDataForm extends Model
{
    /**
     * @var bool create sample data
     */
    public $sampleData;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sampleData'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sampleData' => Yii::t('InstallerModule.base', 'Set up example content (recommended)'),
        ];
    }

}
