<?php



namespace humhub\modules\like;

use humhub\components\ActiveRecord;
use humhub\components\behaviors\PolymorphicRelation;
use humhub\components\Event;
use humhub\modules\like\models\Like;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\db\StaleObjectException;

/**
 * Events provides callbacks to handle events.
 *
 * @author luke
 */
class Events extends BaseObject
{
    /**
     * On User delete, also delete all comments
     *
     * @param Event $event
     * @return bool
     * @throws Throwable
     * @throws StaleObjectException
     */
    public static function onUserDelete($event)
    {
        foreach (Like::findAll(['created_by' => $event->sender->id]) as $like) {
            /** @var Like $like */
            $like->delete();
        }

        return true;
    }

    /**
     * On any ActiveRecord deletion check for assigned likes
     *
     * @param $event
     * @return bool
     * @throws Throwable
     * @throws StaleObjectException
     */
    public static function onActiveRecordDelete($event)
    {
        /** @var ActiveRecord $record */
        $record = $event->sender;
        if ($record->hasAttribute('id')) {
            foreach (Like::findAll(['object_id' => $record->id, 'object_model' => PolymorphicRelation::getObjectModel($record)]) as $like) {
                $like->delete();
            }
        }

        return true;
    }

    /**
     * Callback to validate module database records.
     *
     * @param Event $event
     */
    public static function onIntegrityCheck($event)
    {
        $integrityController = $event->sender;
        $integrityController->showTestHeadline("Like (" . Like::find()->count() . " entries)");

        foreach (Like::find()->each() as $like) {
            if ($like->source === null) {
                if ($integrityController->showFix("Deleting like id " . $like->id . " without existing target!")) {
                    $like->delete();
                }
            }
            // User exists
            if ($like->user === null) {
                if ($integrityController->showFix("Deleting like id " . $like->id . " without existing user!")) {
                    $like->delete();
                }
            }
        }
    }

    /**
     * On initalizing the wall entry controls also add the like link widget
     *
     * @param Event $event
     */
    public static function onWallEntryLinksInit($event)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('like');

        if ($module->canLike($event->sender->object)) {
            $event->sender->addWidget(widgets\LikeLink::class, ['object' => $event->sender->object], ['sortOrder' => 20]);
        }
    }


    /**
     * @return Module the like module
     */
    private static function getModule()
    {
        return Yii::$app->getModule('like');
    }

}
