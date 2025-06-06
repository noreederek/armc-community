<?php



namespace humhub\modules\content\widgets;

use humhub\libs\BasePermission;
use humhub\modules\content\components\ContentActiveRecord;
use yii\base\Widget;

/**
 * Edit Link for Wall Entries
 *
 * This widget will attached to the WallEntryControlsWidget and displays
 * the "Edit" Link to the Content Objects.
 *
 * @package humhub.modules_core.wall.widgets
 * @since 0.10
 */
class EditLink extends Widget
{
    /**
     * @var ContentActiveRecord
     */
    public $model = null;

    /**
     * @var string edit route.
     */
    public $url;

    /**
     * @var defines the edit type of the wallentry
     */
    public $mode;


    /**
     * Executes the widget.
     */
    public function run()
    {
        if (!$this->url) {
            return;
        }

        if ($this->model->content->canEdit()) {
            return $this->render('editLink', [
                'editUrl' => $this->url,
                'mode' => $this->mode,
            ]);
        }
    }

}
