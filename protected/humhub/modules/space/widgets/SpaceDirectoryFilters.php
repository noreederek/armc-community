<?php



namespace humhub\modules\space\widgets;

use humhub\libs\Html;
use humhub\modules\ui\widgets\DirectoryFilters;
use Yii;

/**
 * SpaceDirectoryFilters displays the filters on the directory spaces page
 *
 * @since 1.9
 * @author Luke
 */
class SpaceDirectoryFilters extends DirectoryFilters
{
    /**
     * @inheritdoc
     */
    public $pageUrl = '/space/spaces';

    protected function initDefaultFilters()
    {
        $this->addFilter('keyword', [
            'title' => Yii::t('SpaceModule.base', 'Find Spaces by their description or by their tags'),
            'placeholder' => Yii::t('SpaceModule.base', 'Search...'),
            'type' => 'input',
            'inputOptions' => ['autocomplete' => 'off'],
            'wrapperClass' => 'col-md-6 form-search-filter-keyword',
            'afterInput' => Html::submitButton('<span class="fa fa-search"></span>', ['class' => 'form-button-search']),
            'sortOrder' => 100,
        ]);

        $this->addFilter('sort', [
            'title' => Yii::t('SpaceModule.base', 'Sorting'),
            'type' => 'dropdown',
            'options' => [
                'sortOrder' => '',
                'name' => Yii::t('SpaceModule.base', 'By Name'),
                'newer' => Yii::t('SpaceModule.base', 'Newest first'),
                'older' => Yii::t('SpaceModule.base', 'Oldest first'),
            ],
            'sortOrder' => 200,
        ]);

        $this->addFilter('connection', [
            'title' => Yii::t('SpaceModule.base', 'Status'),
            'type' => 'dropdown',
            'options' => [
                '' => Yii::t('SpaceModule.base', 'Any'),
                'member' => Yii::t('SpaceModule.base', 'Member'),
                'follow' => Yii::t('SpaceModule.base', 'Following'),
                'none' => Yii::t('SpaceModule.base', 'Neither..nor'),
                'separator' => '———————————',
                'archived' => Yii::t('SpaceModule.base', 'Archived'),
            ],
            'sortOrder' => 300,
        ]);
    }

    public static function getDefaultValue(string $filter): string
    {
        switch ($filter) {
            case 'sort':
                return 'sortOrder';
        }

        return parent::getDefaultValue($filter);
    }
}
