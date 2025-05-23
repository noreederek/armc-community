<?php



namespace humhub\modules\user\grid;

use humhub\modules\user\widgets\Image as UserImage;

/**
 * ImageColumn
 *
 * @since 1.3
 * @author Luke
 */
class ImageColumn extends BaseColumn
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->options['style'] = 'width:38px';
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        return UserImage::widget(['user' => $this->getUser($model), 'width' => 34]);
    }

}
