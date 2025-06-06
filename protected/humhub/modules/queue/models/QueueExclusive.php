<?php



namespace humhub\modules\queue\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "queue_exclusive".
 *
 * @property string $id
 * @property string $job_message_id
 * @property int $job_status
 * @property string $last_update
 */
class QueueExclusive extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'queue_exclusive';
    }

}
