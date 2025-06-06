<?php



namespace humhub\modules\file\libs;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\file\Module;
use humhub\modules\file\widgets\FileDownload;
use humhub\libs\Html;
use humhub\libs\MimeHelper;
use humhub\modules\file\models\File;
use humhub\modules\file\handler\FileHandlerCollection;
use humhub\modules\file\handler\DownloadFileHandler;
use humhub\modules\file\converter\PreviewImage;
use humhub\modules\content\components\ContentActiveRecord;
use Yii;
use yii\helpers\Url;

/**
 * FileHelper
 *
 * @since 1.2
 * @author Luke
 */
class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * Checks if given fileName has a extension
     *
     * @param string $fileName the filename
     * @return bool has extension
     */
    public static function hasExtension($fileName)
    {
        /**
         * suggested alternative:
         * `return pathinfo($file, PATHINFO_FILENAME) && pathinfo($file, PATHINFO_EXTENSION);`
         * of
         * `
         * $path_parts = pathinfo($file);
         * return ($path_parts['filename'] ?? false) && ($path_parts['extension'] ?? false);
         * `
         * @see \humhub\tests\codeception\unit\libs\FileHelperTest::testHasExtensionFalsePositives()
         */
        return (strpos($fileName, '.') !== false);
    }

    /**
     * Returns the extension of a file
     *
     * @param string|File $fileName the filename or File model
     * @return string the extension
     */
    public static function getExtension($fileName)
    {
        if ($fileName instanceof File) {
            $fileName = $fileName->file_name;
        }

        if (!is_string($fileName)) {
            return '';
        }

        $fileParts = pathinfo($fileName);
        if (isset($fileParts['extension'])) {
            return $fileParts['extension'];
        }

        return '';
    }

    /**
     * Creates a file with options
     *
     * @param File $file
     * @return string the rendered HTML link
     * @since 1.2
     */
    public static function createLink(File $file, $options = [], $htmlOptions = [])
    {
        $label = (isset($htmlOptions['label'])) ? $htmlOptions['label'] : Html::encode($file->fileName);

        $fileHandlers = FileHandlerCollection::getByType([FileHandlerCollection::TYPE_VIEW, FileHandlerCollection::TYPE_EXPORT, FileHandlerCollection::TYPE_EDIT, FileHandlerCollection::TYPE_IMPORT], $file);
        if (count($fileHandlers) === 1 && $fileHandlers[0] instanceof DownloadFileHandler) {
            $htmlOptions['target'] = '_blank';
            $htmlOptions = array_merge($htmlOptions, FileDownload::getFileDataAttributes($file));
            return Html::a($label, $file->getUrl(), $htmlOptions);
        }

        $htmlOptions = array_merge($htmlOptions, ['data-target' => '#globalModal']);

        $urlOptions = ['/file/view', 'guid' => $file->guid];

        return Html::a($label, Url::to($urlOptions), $htmlOptions);
    }

    /**
     * Determines the content container of a File record
     *
     * @param File $file
     * @return ContentContainerActiveRecord the content container or null
     * @since 1.2
     */
    public static function getContentContainer(File $file)
    {
        $relation = $file->getPolymorphicRelation();

        if ($relation !== null && $relation instanceof ContentActiveRecord) {
            if ($relation->content->container !== null) {
                return $relation->content->container;
            }
        }

        return null;
    }

    /**
     * Returns general file infos as array
     * These information are mainly used by the frontend JavaScript application to handle files.
     *
     * @param File $file the file
     * @return array the file infos
     * @since 1.2
     */
    public static function getFileInfos(File $file)
    {
        $thumbnailUrl = '';
        $previewImage = new PreviewImage();
        if ($previewImage->applyFile($file)) {
            $thumbnailUrl = $previewImage->getUrl();
        }

        return [
            'name' => $file->file_name,
            'guid' => $file->guid,
            'size' => $file->size,
            'mimeType' => $file->mime_type,
            'mimeIcon' => MimeHelper::getMimeIconClassByExtension(self::getExtension($file->file_name)),
            'size_format' => Yii::$app->formatter->asShortSize($file->size, 1),
            'url' => $file->getUrl(),
            'relUrl' => $file->getUrl(null, false),
            'openLink' => FileHelper::createLink($file),
            'thumbnailUrl' => $thumbnailUrl,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getExtensionsByMimeType($mimeType, $magicFile = null)
    {
        $extensionsByMimeType = parent::getExtensionsByMimeType($mimeType, $magicFile);

        /* @var Module $module */
        $module = Yii::$app->getModule('file');
        if (isset($module->additionalMimeTypes) && is_array($module->additionalMimeTypes)) {
            foreach ($module->additionalMimeTypes as $additionalExtension => $additionalMimeType) {
                if ($additionalMimeType === $mimeType) {
                    $extensionsByMimeType[] = $additionalExtension;
                }
            }
        }

        return $extensionsByMimeType;
    }

}
