<?php



namespace humhub\components\bootstrap;

use yii\base\BootstrapInterface;

/**
 * LanguageSelector automatically sets the language of the i18n component
 *
 * @see \humhub\components\i18n\I18N
 * @author luke
 */
class LanguageSelector implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        mb_internal_encoding('UTF-8');

        $app->i18n->autosetLocale();
    }
}
