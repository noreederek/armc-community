<?php



namespace humhub\modules\user\models;

use humhub\components\ActiveRecord;
use humhub\components\behaviors\PolymorphicRelation;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentAddonActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\user\notifications\Mentioned;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_mentioning".
 * The followings are the available columns in table 'user_mentioning':
 *
 * @property int $id
 * @property string $object_model
 * @property int $object_id
 * @property int $user_id
 */
class Mentioning extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_mentioning';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => PolymorphicRelation::class,
                'mustBeInstanceOf' => [ContentActiveRecord::class, ContentAddonActiveRecord::class],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_model', 'object_id', 'user_id'], 'required'],
            [['object_id', 'user_id'], 'integer'],
            [['object_model'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $mentionedSource = $this->getPolymorphicRelation();

        $originator = $this->getOriginatorBySource($mentionedSource);

        if (!$originator) {
            throw new Exception("Invalid polymorphic relation for Mentioning!");
        }

        // Send Notification
        Mentioned::instance()->from($originator)->about($mentionedSource)->send($this->user);

        return parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @param $source
     * @return User|null
     */
    private function getOriginatorBySource($source)
    {
        if ($source instanceof ContentActiveRecord) {
            /** @var ContentActiveRecord $source */
            return $source->content->createdBy;
        } elseif ($source instanceof ContentAddonActiveRecord) {
            /** @var ContentAddonActiveRecord $source */
            return $source->user;
        }

        return null;
    }

    /**
     * Parses a given text for mentioned users and creates an mentioning for them.
     *
     * @param ContentActiveRecord|ContentAddonActiveRecord $record
     * @param string $text
     *
     * @return User[] Mentioned users
     * @throws Exception
     * @deprecated since 1.3 use [[\humhub\modules\content\widgets\richtext\RichText::processText()]] instead
     */
    public static function parse($record, $text)
    {
        $result = [];
        if ($record instanceof ContentActiveRecord || $record instanceof ContentAddonActiveRecord) {
            preg_replace_callback('@\@\-u([\w\-]*?)($|\s|\.)@', function ($hit) use (&$record, &$result) {
                $result = array_merge($result, static::mention($hit[1], $record));
            }, $text);
        } else {
            throw new Exception("Mentioning can only used in HActiveRecordContent or HActiveRecordContentAddon objects!");
        }
        return $result;
    }

    /**
     * Creates the mentioning for the given `$guids`.
     * This function will skip `$guids` which are already mentioned in the given `$record`.
     *
     * @param string|string[] $guids
     * @param ContentActiveRecord|ContentAddonActiveRecord $record
     * @return array new mentionings for the given $record
     * @throws InvalidArgumentException if an invalid $record is provided
     * @since 1.3
     */
    public static function mention($guids, $record)
    {
        if (!($record instanceof ContentActiveRecord || $record instanceof ContentAddonActiveRecord)) {
            throw new InvalidArgumentException("Mentioning can only used in HActiveRecordContent or HActiveRecordContentAddon objects!");
        }

        // Mention only for published content
        if (!$record->content->getStateService()->isPublished()) {
            return [];
        }

        if (is_string($guids)) {
            $guids = [$guids];
        }

        $result = [];

        foreach ($guids as $guid) {
            $user = User::findOne(['guid' => $guid]);
            if (!$user) {
                continue;
            }

            // Check the user was already mentioned (e.g. edit)
            $mention = static::findOne([
                'object_model' => get_class($record),
                'object_id' => $record->getPrimaryKey(),
                'user_id' => $user->id,
            ]);

            if (!$mention) {
                $mention = new static(['user_id' => $user->id]);
                $mention->setPolymorphicRelation($record);
                $mention->save();

                $result[] = $user;

                // Mentioned users automatically follows the content
                $record->content->getPolymorphicRelation()->follow($user->id);
            }
        }

        return $result;
    }

    /**
     * Related user
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
