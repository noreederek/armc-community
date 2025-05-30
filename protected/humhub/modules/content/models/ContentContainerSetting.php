<?php



namespace humhub\modules\content\models;

use humhub\components\SettingActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "contentcontainer_setting".
 *
 * @property int $id
 * @property string $module_id
 * @property int $contentcontainer_id
 * @property string $name
 * @property string $value
 * @property ContentContainer $contentcontainer
 * @since 1.1
 */
class ContentContainerSetting extends SettingActiveRecord
{
    /** @inheritdoc */
    public const CACHE_KEY_FORMAT = 'settings-%s-%d';

    /** @inheritdoc */
    public const CACHE_KEY_FIELDS = ['module_id', 'contentcontainer_id'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contentcontainer_setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_id', 'contentcontainer_id', 'name'], 'required'],
            [['contentcontainer_id'], 'integer'],
            [['value'], 'string'],
            [['module_id', 'name'], 'string', 'max' => 50],
            [['module_id', 'contentcontainer_id', 'name'], 'unique', 'targetAttribute' => ['module_id', 'contentcontainer_id', 'name'], 'message' => 'The combination of Module ID, Contentcontainer ID and Name has already been taken.'],
            [['contentcontainer_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentContainer::class, 'targetAttribute' => ['contentcontainer_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_id' => 'Module ID',
            'contentcontainer_id' => 'Contentcontainer ID',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getContentcontainer()
    {
        return $this->hasOne(ContentContainer::class, ['id' => 'contentcontainer_id']);
    }

}
