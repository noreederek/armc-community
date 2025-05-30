<?php


namespace comment\acceptance;

use comment\AcceptanceTester;
use Exception;

class CommentCest
{
    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testCreateComment(AcceptanceTester $I)
    {
        $I->amUser1();
        $I->amOnSpace2();
        $I->waitForText('Admin Space 2 Post Private');

        $postEntry = '.wall_humhubmodulespostmodelsPost_13';
        $commentSection = $postEntry . ' .comment-container';

        $I->click('Comment', $postEntry);
        $I->wait(1);

        $I->click('.btn-comment-submit', $commentSection);

        $I->waitForText('The comment must not be empty!', null, $commentSection);

        $I->fillField($commentSection . ' .humhub-ui-richtext[contenteditable]', 'Test comment');

        $I->click('.btn-comment-submit', $commentSection);

        $I->waitForElementVisible('#comment-message-1');
        $I->see('Test comment', '#comment-message-1');
        $I->dontSee('The comment must not be empty!', null, $commentSection);
    }
}
