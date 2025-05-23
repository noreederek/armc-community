<?php



namespace humhub\modules\content\models;

use yii\db\ActiveRecord;

/**
 * Class ContentTagAddition
 *
 * @property int $id
 * @perperty integer $tag_id
 *
 * @since 1.2.2
 * @author buddha
 */
class ContentTagAddition extends ActiveRecord
{
    public function setTag(ContentTag $tag)
    {
        $this->tag_id = $tag->id;
    }
}
