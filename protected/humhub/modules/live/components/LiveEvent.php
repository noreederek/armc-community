<?php



namespace humhub\modules\live\components;

use yii\base\BaseObject;

/**
 * LiveEvent implements a message which can be send via live communication
 *
 * @since 1.2
 * @author Luke
 */
abstract class LiveEvent extends BaseObject
{
    /**
     * @see \humhub\modules\content\components\ContentContainerActiveRecord
     * @var int
     */
    public $contentContainerId;

    /**
     * @see \humhub\modules\content\models\Content::VISIBILITY_*
     * @var int
     */
    public $visibility;

    /**
     * Returns the data of this event as array
     *
     * @return array the live event data
     */
    public function getData()
    {
        $data = get_object_vars($this);
        unset($data['visibility']);
        unset($data['contentContainerId']);

        return [
            'type' => str_replace('\\', '.', get_class($this)),
            'contentContainerId' => $this->contentContainerId,
            'visibility' => $this->visibility,
            'data' => $data,
        ];
    }

}
