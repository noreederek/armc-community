<?php


namespace web\acceptance;

use Exception;
use web\AcceptanceTester;
use Yii;

class NonceCest
{
    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testNoNonceScript(AcceptanceTester $I)
    {
        $I->amUser();
        $script = "$('body').html('Got ya!')";
        $I->executeJS("$('body').append(\"<script>$script</script>\")");
        $I->wait(1);
        $I->dontSee("Got ya!");
    }

    public function testStatistic(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->amOnRoute(['/admin/setting/statistic']);
        $I->wait(2);
        $I->executeJS('_editor = document.querySelectorAll("div.CodeMirror")[0].CodeMirror; _editor.setValue("<script nonce=\"{{ nonce }}\">$(\".field-statisticsettingsform-trackinghtmlcode\").after(\'<div id=\"test_tracking_script\">Tracking Script</div>\')</script>");');
        $I->jsClick('button[type=submit]');
        $I->wait(2);
        $I->see("Tracking Script", '#test_tracking_script');
    }

    public function testInvalidStatistic(AcceptanceTester $I)
    {
        Yii::$app->settings->set('trackingHtmlCode', '<script>alert("Tracking Script")</script>');
        $I->amAdmin();
        $I->amOnRoute(['/admin/setting/statistic']);
        $I->wait(2);
        $I->executeJS('_editor = document.querySelectorAll("div.CodeMirror")[0].CodeMirror; _editor.setValue("<script>alert(\"Tracking Script\")</script>");');
        $I->jsClick('button[type=submit]');
        $I->wait(2);
        $I->amOnDashboard();
        $I->dontSee("Tracking Script");
    }
}
