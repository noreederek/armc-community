<?php



namespace humhub\modules\content\widgets;

use humhub\modules\content\components\ContentActiveRecord;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * PermaLink for Wall Entries
 *
 * This widget will attached to the WallEntryControlsWidget and displays
 * the "Permalink" Link to the Content Objects.
 *
 * @package humhub.modules_core.wall.widgets
 * @since 0.5
 */
class PermaLink extends Widget
{
    /**
     * @var ContentActiveRecord
     */
    public $content;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $permaLink = Url::to(['/content/perma', 'id' => $this->content->content->id], true);

        return $this->render('permaLink', [
            'permaLink' => $permaLink,
            'id' => $this->content->content->id,
        ]);
    }

}
