<?php



namespace humhub\tests\codeception\unit\components\access;

use humhub\components\access\ActionAccessValidator;

class TestActionValidator extends ActionAccessValidator
{
    protected function validate($rule)
    {
        if (!$rule['return']) {
            $this->access->code = 404;
            $this->access->reason = 'Not you again!';
            return false;
        }
        return true;
    }
}
