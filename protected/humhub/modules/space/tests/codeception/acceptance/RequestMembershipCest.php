<?php


namespace space\acceptance;

use Exception;
use space\AcceptanceTester;

class RequestMembershipCest
{
    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testRequestMembershipAccept(AcceptanceTester $I)
    {
        $I->wantTo('ensure that accepting an users space membership works.');

        $I->amUser1();
        $I->amOnSpace1();
        $I->seeElement('[data-space-request-membership]');
        $I->click('[data-space-request-membership]');

        $I->waitForText('Request Membership', null, '#globalModal');
        $I->fillField('#request-message', 'Hi, I want to join this space.');
        $I->click('Send', '#globalModal');
        $I->waitForText('Your request was successfully submitted to the space administrators.');
        $I->click('Close', '#globalModal');

        $I->waitForText('Pending');

        $I->amAdmin(true);
        $I->seeInNotifications('Peter Tester requests membership for the space Space 1', true);

        $I->waitForText('Pending Approvals', null, '.tab-menu .active');
        $I->see('Hi, I want to join this space.', '.grid-view');
        $I->click('Accept', '.grid-view');

        $I->wait(1);

        $I->amUser1(true);

        $I->seeInNotifications('Admin Tester approved your membership for the space Space 1', true);
        $I->waitForText('User 1 Space 1 Post Private', null, '#wallStream');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testRequestMembershipDecline(AcceptanceTester $I)
    {
        $I->wantTo('ensure that declining an users space membership works.');

        $I->amUser1();
        $I->amOnSpace1();
        $I->seeElement('[data-space-request-membership]');
        $I->click('[data-space-request-membership]');

        $I->waitForText('Request Membership', null, '#globalModal');
        $I->fillField('#request-message', 'Hi, I want to join this space.');
        $I->click('Send', '#globalModal');
        $I->waitForText('Your request was successfully submitted to the space administrators.');
        $I->click('Close', '#globalModal');

        $I->waitForText('Pending');

        $I->amAdmin(true);
        $I->seeInNotifications('Peter Tester requests membership for the space Space 1', true);

        $I->waitForText('Pending Approvals', null, '.tab-menu .active');

        $I->click('.dropdown-navigation', '.controls-header');
        $I->waitForText('Members', null, '.controls-header');
        $I->click('Members', '.controls-header');

        $I->waitForText('Member since');
        $I->see('Pending Approvals');
        $I->click('Pending Approvals');

        $I->waitForText('Decline');
        $I->click('Decline');

        $I->waitForElementVisible('#wallStream');
        $I->dontSeeInNotifications('Peter Tester requests membership for the space Space 1');

        $I->amUser1(true);

        $I->seeInNotifications('Admin Tester declined your membership request for the space Space 1', true);
        $I->waitForElementVisible('[data-space-request-membership]');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testRequestMembershipRevoke(AcceptanceTester $I)
    {
        $I->wantTo('ensure that revoking an users space membership works.');

        $I->amUser1();
        $I->amOnSpace1();
        $I->seeElement('[data-space-request-membership]');
        $I->click('[data-space-request-membership]');

        $I->waitForText('Request Membership', null, '#globalModal');
        $I->fillField('#request-message', 'Hi, I want to join this space.');
        $I->click('Send', '#globalModal');
        $I->waitForText('Your request was successfully submitted to the space administrators.');
        $I->click('Close', '#globalModal');

        $I->waitForText('Pending');
        $I->click('Pending');
        $I->waitForText('Confirm');
        $I->click('Confirm');
        $I->waitForText('Join'); // Back to dashboard
        $I->amOnSpace1();
        $I->waitForText('Join', null, '[data-space-request-membership]');

        $I->amAdmin(true);
        $I->dontSeeInNotifications('Peter Tester requests membership for the space Space 1');
        $I->amOnSpace1();
        $I->dontSeeElement('.panel-danger');

        $I->click('.dropdown-navigation', '.controls-header');
        $I->waitForText('Members', null, '.controls-header');
        $I->click('Members', '.controls-header');

        $I->waitForText('Manage members');
        $I->dontSee('Pending Approvals');
    }
}
