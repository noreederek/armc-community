<?php



namespace space\functional;

use space\FunctionalTester;
use Yii;
use yii\helpers\Url;
use yii\mail\MessageInterface;

class MailInviteCest
{
    public function testExternalInvite(FunctionalTester $I)
    {
        $I->wantTo('ensure that users can be invited by mail within the directory');

        Yii::$app->getModule('user')->settings->set('auth.internalUsersCanInviteByEmail', 1);

        $I->amUser2();
        $I->amOnSpace1();
        $I->see('Invite');
        $I->amGoingTo('invite a user by mail');

        $I->sendAjaxPostRequest(Url::to(['/space/membership/invite', 'cguid' => $I->getFixtureSpace(0)->guid]), ['InviteForm[inviteEmails]' => 'a@test.de,b@test.de']);

        $I->seeEmailIsSent(2);

        /* @var $messages MessageInterface[] */
        $messages = $I->grabSentEmails();


        if (!array_key_exists('a@test.de', $messages[0]->getTo())) {
            $I->see('a@test.de not in mails');
        }

        if (!array_key_exists('b@test.de', $messages[1]->getTo())) {
            $I->see('b@test.de not in mails');
        }

        $token = $I->fetchInviteToken($messages[0]);
        return;

        $I->logout();

        $I->amOnRoute('/user/registration', ['token' => $token]);
        $I->see('Account registration');
        $I->fillField('User[username]', 'NewUser');
        $I->fillField('Password[newPassword]', 'NewUser123');
        $I->fillField('Password[newPasswordConfirm]', 'NewUser123');
        $I->fillField('Profile[firstname]', 'New');
        $I->fillField('Profile[lastname]', 'User');
        $I->click('#registration-form [type="submit"]');

        $I->see('Dashboard');
    }
}
