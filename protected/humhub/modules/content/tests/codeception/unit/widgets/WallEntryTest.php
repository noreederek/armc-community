<?php



namespace humhub\modules\content\tests\codeception\unit\widgets;

use humhub\modules\content\widgets\WallEntry;

/**
 * @inheritdoc
 */
class WallEntryTest extends WallEntry
{
    public $wallEntryLayout = "@humhub/modules/content/tests/codeception/unit/widgets/views/wallEntry.php";

    /**
     * @inheritdoc
     */
    public function run()
    {
        return '<div>Wallentry:' . $this->contentObject->message . '</div>';
    }

}
