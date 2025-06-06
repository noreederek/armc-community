<?php



namespace tests\codeception\unit\modules\content;

use Yii;
use humhub\modules\friendship\models\Friendship;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;
use Codeception\Specify;
use humhub\modules\post\models\Post;
use humhub\modules\space\models\Space;
use humhub\modules\content\models\Content;
use humhub\modules\stream\actions\ContentContainerStream;

class SpaceContentPermissionTest extends HumHubDbTestCase
{
    /**
     *  - User is the owner of the content
     *  - User is system administrator and the content module setting `adminCanEditAllContent` is set to true (default)
     *  - The user is granted the managePermission set by the model record class
     *  - The user meets the additional condition implemented by the model records class own `canEdit()` function.
     */
    use Specify;

    public $privatePost;
    public $publicPost;
    public $space;

    public function setUp(): void
    {
        parent::setUp();
        $this->becomeUser('Admin');
        $this->space = Space::findOne(['id' => 3]);
        $this->space->visibility = Space::VISIBILITY_ALL;
        $this->space->save();

        $this->privatePost = new Post();
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->privatePost->silentContentCreation = true;
        }
        $this->privatePost->message = "Private Space1 Post";
        $this->privatePost->content->setContainer($this->space);
        $this->privatePost->content->visibility = Content::VISIBILITY_PRIVATE;
        $this->privatePost->save();

        $this->publicPost = new Post();
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->publicPost->silentContentCreation = true;
        }
        $this->publicPost->message = "Public Space1 Post";
        $this->publicPost->content->setContainer($this->space);
        $this->publicPost->content->visibility = Content::VISIBILITY_PUBLIC;
        $this->publicPost->save();
    }

    public function testOwnerPermissions()
    {
        $this->assertTrue($this->privatePost->content->canView());
        $this->assertTrue($this->privatePost->content->canEdit());
        $this->assertTrue($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canEdit());

        $this->setSpaceVisibility(Space::VISIBILITY_NONE);

        $this->reloadPosts();
        $this->assertTrue($this->privatePost->content->canView());
        $this->assertTrue($this->privatePost->content->canEdit());
        $this->assertTrue($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canEdit());
    }

    public function testModeratorPermission()
    {
        $user2 = User::findOne(['id' => 3]);
        $this->assertTrue($this->privatePost->content->canView($user2));
        $this->assertTrue($this->publicPost->content->canView($user2));
        $this->assertTrue($this->publicPost->content->canEdit($user2));
        $this->assertTrue($this->privatePost->content->canEdit($user2));

        // Test again with logged in user
        $this->becomeUser('User2');
        $this->reloadPosts();
        $this->assertTrue($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canView());
        $this->assertTrue($this->publicPost->content->canEdit());
        $this->assertTrue($this->privatePost->content->canEdit());

        // Test with visiblity none, should not have any effect
        $this->setSpaceVisibility(Space::VISIBILITY_NONE);
        $this->reloadPosts();
        $this->assertTrue($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canView());
        $this->assertTrue($this->publicPost->content->canEdit());
        $this->assertTrue($this->privatePost->content->canEdit());
    }

    public function testMemberPermission()
    {
        $user1 = User::findOne(['id' => 2]);
        $this->assertTrue($this->privatePost->content->canView($user1));
        $this->assertTrue($this->publicPost->content->canView($user1));
        $this->assertFalse($this->publicPost->content->canEdit($user1));
        $this->assertFalse($this->privatePost->content->canEdit($user1));

        // Test again with logged in user
        $this->becomeUser('User1');
        $this->reloadPosts();
        $this->assertTrue($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canView());
        $this->assertFalse($this->publicPost->content->canEdit());
        $this->assertFalse($this->privatePost->content->canEdit());

        // Test with visiblity none, should not have any effect
        $this->setSpaceVisibility(Space::VISIBILITY_NONE);
        $this->reloadPosts();
        $this->assertTrue($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canView());
        $this->assertFalse($this->publicPost->content->canEdit());
        $this->assertFalse($this->privatePost->content->canEdit());
    }

    public function testUserPermission()
    {
        $user3 = User::findOne(['id' => 4]);
        $this->assertFalse($this->privatePost->content->canView($user3));
        $this->assertTrue($this->publicPost->content->canView($user3));
        $this->assertFalse($this->publicPost->content->canEdit($user3));
        $this->assertFalse($this->privatePost->content->canEdit($user3));

        // Test again with logged in user
        $this->becomeUser('User3');
        $this->reloadPosts();
        $this->assertFalse($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canView());
        $this->assertFalse($this->publicPost->content->canEdit());
        $this->assertFalse($this->privatePost->content->canEdit());

        // Test with visiblity none, should not have any effect
        $this->setSpaceVisibility(Space::VISIBILITY_NONE);
        $this->reloadPosts();
        $this->assertFalse($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canView());
        $this->assertFalse($this->publicPost->content->canEdit());
        $this->assertFalse($this->privatePost->content->canEdit());

    }

    public function testGuestPermission()
    {

        // Disable guest access
        Yii::$app->getModule('user')->settings->set('auth.allowGuestAccess', false);

        // Guest
        $this->logout();

        // Refresh cached permissions etc
        $this->reloadPosts();

        $this->assertFalse($this->privatePost->content->canView());
        $this->assertFalse($this->publicPost->content->canView());
        $this->assertFalse($this->publicPost->content->canEdit());
        $this->assertFalse($this->privatePost->content->canEdit());

        // Enable guest access
        Yii::$app->getModule('user')->settings->set('auth.allowGuestAccess', true);

        // Refresh cached permissions etc
        $this->reloadPosts();

        $this->assertFalse($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canView());
        $this->assertFalse($this->publicPost->content->canEdit());
        $this->assertFalse($this->privatePost->content->canEdit());

        $this->setSpaceVisibility(Space::VISIBILITY_NONE);
        $this->reloadPosts();
        $this->assertFalse($this->privatePost->content->canView());
        $this->assertTrue($this->publicPost->content->canView());
        $this->assertFalse($this->publicPost->content->canEdit());
        $this->assertFalse($this->privatePost->content->canEdit());
    }

    protected function setSpaceVisibility($visibility)
    {
        $this->space->visibility = $visibility;
        $this->space->save();
    }

    /**
     * Used for resetting the permissionmanager cache etc.
     */
    protected function reloadPosts()
    {
        $this->privatePost = Post::findOne(['id' => $this->privatePost->id]);
        $this->publicPost = Post::findOne(['id' => $this->publicPost->id]);
    }
}
