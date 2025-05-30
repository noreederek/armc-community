<?php



namespace humhub\modules\user\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "group_admin".
 *
 * @property int $id
 * @property int $user_id
 * @property int $group_id
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 * @property User $user
 * @property Group $group
 */
class GroupUser extends ActiveRecord
{
    public const SCENARIO_REGISTRATION = 'registration';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'group_id'], 'required'],
            [['user_id', 'group_id'], 'integer'],
            [['group_id'], 'validateGroupId'],
            [['user_id', 'group_id'], 'unique', 'targetAttribute' => ['user_id', 'group_id'], 'message' => 'The combination of User ID and Group ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REGISTRATION] = ['group_id'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'group_id' => 'Group ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->group !== null && $this->group->groupSpaces !== null) {
                foreach ($this->group->groupSpaces as $groupSpace) {
                    $groupSpace->space->addMember($this->user->id);
                }
            }
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
    }

    /**
     * Returns all Group relation
     *
     * @return ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(Group::class, ['id' => 'group_id']);
    }

    /**
     * Returns all User relation
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Validator for group field during registration
     */
    public function validateGroupId()
    {
        if ($this->scenario == self::SCENARIO_REGISTRATION) {
            if ($this->group_id != '') {
                $registrationGroups = Group::getRegistrationGroups($this->user);
                foreach ($registrationGroups as $group) {
                    if ($this->group_id == $group->id) {
                        return;
                    }
                }

                // Not found group in groups available during registration
                $this->addError('group_id', 'Invalid group given!');
            }
        }
    }

}
