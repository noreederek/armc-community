<?php



namespace humhub\modules\user\models;

use humhub\components\ActiveRecord;

/**
 * This is the model class for table "user_http_session".
 *
 * The followings are the available columns in table 'user_http_session':
 * @property string $id
 * @property int $expire
 * @property int $user_id
 * @property string $data
 *
 * @package humhub.modules_core.user.models
 * @since 0.5
 * @author Luke
 */
class Session extends ActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'user_http_session';
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
