<?php



namespace humhub\widgets;

/**
 * @inheritdoc
 */
class LinkPager extends \yii\widgets\LinkPager
{
    /**
     * @inheritdoc
     */
    public $maxButtonCount = 5;

    /**
     * @inheritdoc
     */
    public $nextPageLabel = '<i class="fa fa-step-forward"></i>';

    /**
     * @inheritdoc
     */
    public $prevPageLabel = '<i class="fa fa-step-backward"></i>';

    /**
     * @inheritdoc
     */
    public $firstPageLabel = '<i class="fa fa-fast-backward"></i>';

    /**
     * @inheritdoc
     */
    public $lastPageLabel = '<i class="fa fa-fast-forward"></i>';

}
