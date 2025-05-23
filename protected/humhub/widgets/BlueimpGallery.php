<?php



namespace humhub\widgets;

/**
 * BlueimpGallery gallery layout
 *
 * @see LayoutAddons
 * @author buddha
 * @since 1.2
 */
class BlueimpGallery extends \yii\base\Widget
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('blueimpGallery');
    }

}
