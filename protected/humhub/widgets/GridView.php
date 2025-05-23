<?php


namespace humhub\widgets;

use humhub\assets\GridViewAsset;

/**
 * @inheritdoc
 */
class GridView extends \yii\grid\GridView
{
    /**
     * @inheritdoc
     */
    public $tableOptions = ['class' => 'table table-hover'];

    /**
     * @inheritdoc
     */
    public function run()
    {
        GridViewAsset::register($this->view);

        return parent::run();
    }
}
