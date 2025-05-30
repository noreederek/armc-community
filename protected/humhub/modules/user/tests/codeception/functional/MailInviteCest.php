<?php



namespace user\functional;

use user\FunctionalTester;
use Yii;
use yii\helpers\Url;
use yii\mail\MessageInterface;

class MailInviteCest
{
    public function testInviteInDirectory(FunctionalTester $I)
    {
        $I->wantTo('ensure that users can be invited by mail within the directory');

        Yii::$app->getModule('user')->settings->set('auth.internalUsersCanInviteByEmail', 1);

        $I->amUser2();
        $I->amOnDirectory()->clickMembers();
        $I->amGoingTo('invite a user by mail');

        $I->see('Invite new people', 'a');

        $I->sendAjaxPostRequest(Url::to(['/user/invite']), ['Invite[emails]' => 'a@test.de,b@test.de']);
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

    public function testInviteInAdminSection(FunctionalTester $I)
    {
        $I->wantTo('ensure that users can be invited by mail within the directory');

        // Should also work with deactivated invite setting
        Yii::$app->getModule('user')->settings->set('auth.internalUsersCanInviteByEmail', 0);

        $I->amAdmin();
        $I->amGoingTo('invte a user by mail');

        $I->sendAjaxPostRequest(Url::to(['/user/invite']), ['Invite[emails]' => 'a@test.de,b@test.de']);
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
