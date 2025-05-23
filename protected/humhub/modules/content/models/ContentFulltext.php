<?php


namespace humhub\modules\content\models;

use humhub\components\ActiveRecord;

/**
 * This is the model class for table 'content_fulltext'.
 *
 * The followings are the available columns in table 'content_fulltext':
 * @property int $content_id
 * @property string $contents
 * @property string $comments
 * @property string $files
 *
 * @since 1.16
 */
class ContentFulltext extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content_fulltext';
    }
}
