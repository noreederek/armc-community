<?php


namespace humhub\libs;

use humhub\modules\file\libs\FileHelper;
use humhub\modules\file\models\File;

/**
 * MimeHelper
 *
 * @author luke
 */
class MimeHelper
{
    /** IconClass */
    public const ICON_WORD = 'mime-word';
    public const ICON_EXCEL = 'mime-excel';
    public const ICON_POWERPOINT = 'mime-powerpoint';
    public const ICON_PDF = 'mime-pdf';
    public const ICON_ZIP = 'mime-zip';
    public const ICON_IMAGE = 'mime-image';
    public const ICON_AUDIO = 'mime-audio';
    public const ICON_VIDEO = 'mime-video';
    public const ICON_PHOTOSHOP = 'mime-photoshop';
    public const ICON_ILLUSTRATOR = 'mime-illustrator';
    public const ICON_FILE = 'mime-file';

    /** @var array Map for Extension to IconClass */
    private static $extensionToIconClass = [
        // Word
        'doc' => self::ICON_WORD,
        'docx' => self::ICON_WORD,
        'docm' => self::ICON_WORD,
        'odt' => self::ICON_WORD,
        // Excel
        'xls' => self::ICON_EXCEL,
        'xlsx' => self::ICON_EXCEL,
        'xlsb' => self::ICON_EXCEL,
        'xlsm' => self::ICON_EXCEL,
        'ods' => self::ICON_EXCEL,
        // Powerpoint
        'ppt' => self::ICON_POWERPOINT,
        'pptx' => self::ICON_POWERPOINT,
        'pps' => self::ICON_POWERPOINT,
        'ppsx' => self::ICON_POWERPOINT,
        'odp' => self::ICON_POWERPOINT,
        // PDF
        'pdf' => self::ICON_PDF,
        // Archive
        'zip' => self::ICON_ZIP,
        'gzip' => self::ICON_ZIP,
        'rar' => self::ICON_ZIP,
        'tar' => self::ICON_ZIP,
        '7z' => self::ICON_ZIP,
        // Image
        'jpg' => self::ICON_IMAGE,
        'jpeg' => self::ICON_IMAGE,
        'png' => self::ICON_IMAGE,
        'gif' => self::ICON_IMAGE,
        'webp' => self::ICON_IMAGE,
        'tiff' => self::ICON_IMAGE,
        // Audio
        'mp3' => self::ICON_AUDIO,
        'aiff' => self::ICON_AUDIO,
        'wav' => self::ICON_AUDIO,
        'ogg' => self::ICON_AUDIO,
        // Video
        'avi' => self::ICON_VIDEO,
        'mp4' => self::ICON_VIDEO,
        'mov' => self::ICON_VIDEO,
        'mpeg' => self::ICON_VIDEO,
        'wma' => self::ICON_VIDEO,
        'webm' => self::ICON_VIDEO,
        'mkv' => self::ICON_VIDEO,
        // Adobe Photoshop
        'psd' => self::ICON_PHOTOSHOP,
        // Adobe Illustrator
        'ai' => self::ICON_ILLUSTRATOR,
    ];

    /**
     * Returns Stylesheet Classname based on file extension
     *
     * @param string|File $ext The file extension or file object
     * @return string the CSS Class
     */
    public static function getMimeIconClassByExtension($ext)
    {
        if ($ext instanceof File) {
            $ext = FileHelper::getExtension($ext);
        }

        // lowercase string
        $ext = strtolower($ext);

        if (isset(self::$extensionToIconClass[$ext])) {
            return self::$extensionToIconClass[$ext];
        }

        return self::ICON_FILE;
    }
}
