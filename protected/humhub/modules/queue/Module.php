<?php



namespace humhub\modules\queue;

use humhub\components\Module as BaseModule;

/**
 * Queue base module
 *
 * @author Lucas Bartholemy <lucas@bartholemy.com>
 * @since 1.3
 */
class Module extends BaseModule
{
    /**
     * @var int default ttr for Long Running Jobs
     *
     * @since 1.15
     */
    public $longRunningJobTtr = 60 * 60;
}
