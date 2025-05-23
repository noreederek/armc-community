<?php



namespace humhub\modules\space\widgets;

use humhub\modules\space\models\Space;
use humhub\components\Widget;

/**
 * SpaceTags lists all tags of the Space
 */
class SpaceTags extends Widget
{
    /**
     * @var Space
     */
    public $space;

    /**
     * @inheritDoc
     */
    public function run()
    {
        return $this->render('spaceTags', ['space' => $this->space]);
    }

}
