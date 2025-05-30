<?php



namespace humhub\modules\file\models;

use yii\web\UploadedFile;
use humhub\modules\file\validators\FileValidator;

/**
 * FileUpload model is used for File uploads handled by the UploadAction via ajax.
 *
 * @see \humhub\modules\file\actions\UploadAction
 * @author Luke
 * @inheritdoc
 * @since 1.2
 */
class FileUpload extends File
{
    /**
     * @var UploadedFile the uploaded file
     */
    public $uploadedFile = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['uploadedFile'], FileValidator::class],
        ];

        return array_merge(parent::rules(), $rules);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // Store file
        if (!empty($changedAttributes) && $this->uploadedFile instanceof UploadedFile) {
            $this->setStoredFile($this->uploadedFile);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Sets uploaded file to this file model
     *
     * @param UploadedFile $uploadedFile
     */
    public function setUploadedFile(UploadedFile $uploadedFile)
    {
        // Set Filename
        $filename = $uploadedFile->getBaseName();
        $extension = $uploadedFile->getExtension();
        if ($extension !== '') {
            $filename .= '.' . $extension;
        }

        $this->file_name = $filename;
        $this->mime_type = $uploadedFile->type;
        $this->size = $uploadedFile->size;
        $this->uploadedFile = $uploadedFile;
    }

}
