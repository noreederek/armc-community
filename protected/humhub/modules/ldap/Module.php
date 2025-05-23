<?php



namespace humhub\modules\ldap;

/**
 * Friedship Module
 */
class Module extends \humhub\components\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'humhub\modules\ldap\controllers';

    /**
     * @var int the page size for ldap query, set to 0 to disable pagination
     */
    public $pageSize = 10000;

    /**
     * @var array|null the queried LDAP attributes, leave empty to retrieve all
     */
    public $queriedAttributes = [];
}
