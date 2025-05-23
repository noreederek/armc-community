<?php



namespace humhub\components\access;

/**
 * StrictAccess should be used by all controllers which don't allow guest access if guest mode is inactive.
 * There are only some controllers which require guest access even if guest mode is not active as Login, Registration etc.
 *
 * @package humhub\components\access
 */
class StrictAccess extends ControllerAccess
{
    public function getFixedRules()
    {
        $fixed = parent::getFixedRules();
        $fixed[] = [self::RULE_STRICT];
        return $fixed;
    }
}
