<?php



namespace humhub\modules\ldap\commands;

use Exception;
use humhub\modules\ldap\authclient\LdapAuth;
use humhub\modules\user\models\User;
use Laminas\Ldap\Ldap;
use Yii;
use yii\base\InvalidArgumentException;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\db\Expression;
use yii\helpers\Console;

/**
 * Console tools for manage Ldap
 * @method updateAttributes(array $array)
 */
class LdapController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'list';

    /**
     * Lists configured LDAP auth clients
     *
     * @return int the exit code
     */
    public function actionList()
    {
        $this->stdout("*** Configured LDAP AuthClients \n\n");

        $clients = [];
        foreach (Yii::$app->authClientCollection->getClients(true) as $id => $client) {
            if ($client instanceof LdapAuth) {
                /** @var LdapAuth $client */
                $clients[] = [$id, $client->getName() . ' (' . $client->getId() . ')', $client->hostname, $client->port, $client->baseDn];
            }
        }

        try {
            echo Table::widget(['headers' => ['AuthClient ID', 'Name (ClientId)', 'Host', 'Port', 'Base DN'], 'rows' => $clients]);
        } catch (Exception $e) {
            $this->stderr("Error: " . $e->getMessage() . "\n\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        print "\n\n";
    }

    /**
     * Returns status information
     *
     * @param string $id the auth client id (default: ldap)
     * @return int status code
     */
    public function actionStatus($id = 'ldap')
    {
        $this->stdout("*** LDAP Status for AuthClient ID: " . $id . "\n\n");

        try {
            $ldapAuthClient = $this->getAuthClient($id);

            $ldap = $ldapAuthClient->getLdap();
            $userCount = $ldap->count($ldapAuthClient->userFilter, $ldapAuthClient->baseDn, Ldap::SEARCH_SCOPE_SUB);
        } catch (Exception $ex) {
            $this->stderr("Error: " . $ex->getMessage() . "\n\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("Host:\t\t" . $ldapAuthClient->hostname . "\n");
        $this->stdout("Port:\t\t" . $ldapAuthClient->port . "\n");
        $this->stdout("BaseDN:\t\t" . $ldapAuthClient->baseDn . "\n\n");

        $this->stdout("LDAP connection successful!\n\n", Console::FG_GREEN);

        $activeUserCount = User::find()->andWhere(['auth_mode' => $ldapAuthClient->getId(), 'status' => User::STATUS_ENABLED])->count();
        $disabledUserCount = User::find()->andWhere(['auth_mode' => $ldapAuthClient->getId(), 'status' => User::STATUS_DISABLED])->count();

        $this->stdout("LDAP user count:\t\t" . $userCount . " users.\n");
        $this->stdout("HumHub user count (active):\t" . $activeUserCount . " users.\n");
        $this->stdout("HumHub user count (disabled):\t" . $disabledUserCount . " users.\n\n");

        return ExitCode::OK;
    }


    /**
     * Synchronizes all ldap users (if autoRefresh is enabled)
     *
     * @param string $id the auth client id (default: ldap)
     * @return int status code
     */
    public function actionSync($id = 'ldap')
    {
        $this->stdout("*** LDAP Sync for AuthClient ID: " . $id . "\n\n");

        try {
            $ldapAuthClient = $this->getAuthClient($id);
            $ldapAuthClient->syncUsers();
        } catch (Exception $ex) {
            $this->stderr("Error: " . $ex->getMessage() . "\n\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("\nLDAP sync completed!\n\n", Console::FG_GREEN);

        return ExitCode::OK;
    }


    /**
     * Lists all users found in the LDAP server
     *
     * @param string $id the auth client id (default: ldap)
     * @return int status code
     */
    public function actionListUsers($id = 'ldap')
    {
        $this->stdout("*** LDAP User List for AuthClient ID: " . $id . "\n\n");

        try {
            $ldapAuthClient = $this->getAuthClient($id);

            $users = [];
            foreach ($ldapAuthClient->getUserCollection() as $user) {
                $authClient = $ldapAuthClient->getAuthClientInstance($user);
                $attributes = $authClient->getUserAttributes();

                $username = (isset($attributes['username']) ? $attributes['username'] : '---');
                $id = (isset($attributes['id']) ? $attributes['id'] : '---');
                $email = (isset($attributes['email']) ? $attributes['email'] : '---');

                $users[] = [$id, $username, $email];
            }

            echo Table::widget(['headers' => ['ID', 'Username', 'E-Mail'], 'rows' => $users]);
        } catch (Exception $ex) {
            $this->stderr("Error: " . $ex->getMessage() . "\n\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }


    /**
     * Resets the LDAP mapping of all or a certain account.
     *
     * @param string $id the auth client id (default: ldap)
     * @param string $userName UserName, if set, the assignment will be deleted for this user only.
     * @return int status code
     */
    public function actionMappingClear($id = 'ldap', $userName = null)
    {
        $this->stdout("*** LDAP Flush user id mappings for AuthClient ID: " . $id . "\n\n");

        if ($userName === null) {
            User::updateAll(['authclient_id' => new Expression('NULL')], ['auth_mode' => $id]);
        } else {
            User::updateAll(['authclient_id' => new Expression('NULL')], ['auth_mode' => $id, 'username' => $userName]);
        }

        $this->stdout("Mapping(s) cleared!\n");
        return ExitCode::OK;
    }


    /**
     * Rebuilds the authclient_id and auth_mode mappings in the user table
     *
     * @param string $id the auth client id (default: ldap)
     * @return int status code
     */
    public function actionMappingRebuild($id = 'ldap')
    {
        $this->stdout("*** LDAP ReMap Users for AuthClient ID: " . $id . "\n\n");

        $i = 0;
        $m = 0;
        $d = 0;

        try {
            $newAuthClient = $this->getAuthClient($id);

            // Loop over users of this authclient
            foreach ($newAuthClient->getUserCollection() as $userEntry) {
                $i++;

                $authClient = $newAuthClient->getAuthClientInstance($userEntry);
                $attributes = $authClient->getUserAttributes();

                if (!isset($attributes['id'])) {
                    print "Skipped - No ID for: " . $attributes['dn'] . "\n";
                    continue;
                }

                // Fix empty 'authclient_id' by e-mail
                if (isset($attributes['email'])) {
                    $user = User::find()->where(['email' => $attributes['email']])->andWhere(['IS', 'authclient_id', new Expression('NULL')])->one();
                    if ($user !== null && User::findOne(['authclient_id' => $attributes['id']]) === null) {
                        $user->updateAttributes(['authclient_id' => $attributes['id']]);
                        $d++;
                    }
                }

                // Fix empty 'authclient_id' by username
                if (isset($attributes['username'])) {
                    $user = User::find()->where(['username' => $attributes['username']])->andWhere(['IS', 'authclient_id', new Expression('NULL')])->one();
                    if ($user !== null && User::findOne(['authclient_id' => $attributes['id']]) === null) {
                        $user->updateAttributes(['authclient_id' => $attributes['id']]);
                        $d++;
                    }
                }

                // Fix wrong/missing 'auth_mode' by authclient_id
                $user = User::findOne(['authclient_id' => $attributes['id']]);
                if ($user !== null && $user->auth_mode != $newAuthClient->getId()) {
                    $user->updateAttributes(['auth_mode' => $newAuthClient->getId()]);
                    $m++;
                }
            }


            $this->stdout("Checked:\t" . $i . " users.\n");
            $this->stdout("Remapped 'authclient_id' value:\t" . $d . " users.\n");
            $this->stdout("Remapped 'auth_mode' value:\t" . $m . " users.\n");
        } catch (Exception $ex) {
            $this->stderr("Error: " . $ex->getMessage() . "\n\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }


    /**
     * Shows all returned user attributes provided by the LDAP connection.
     *
     * @param string $user the username (inserted into the LoginFilter)
     * @param string $id the auth client id (default: ldap)
     * @return int status code
     * @since 1.8
     */
    public function actionShowUser($user, $id = 'ldap')
    {
        $this->stdout("*** LDAP User Details for \"" . $user . "\" for AuthClient ID: " . $id . "\n\n");

        try {
            $ldapAuthClient = $this->getAuthClient($id);

            $dn = $ldapAuthClient->getLdap()->getCanonicalAccountName($user, Ldap::ACCTNAME_FORM_DN);
            $x = $ldapAuthClient->getAuthClientInstance($ldapAuthClient->getLdap()->getEntry($dn));

            $rows = [];
            foreach ($x->getUserAttributes() as $name => $value) {
                if (!is_array($value) && empty(mb_detect_encoding($value))) {
                    $value = '-Binary-';
                }
                $rows[] = [$name, $value];
            }

            echo Table::widget(['headers' => ['LDAP Attribute Name', 'Value'], 'rows' => $rows]) . "\n\n";
        } catch (Exception $ex) {
            $this->stderr("Error: " . $ex->getMessage() . "\n\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }


    /**
     * @param $id
     * @return LdapAuth
     */
    protected function getAuthClient($id)
    {
        /** @var LdapAuth $ldapAuthClient */
        $ldapAuthClient = Yii::$app->authClientCollection->getClient($id, true);

        if (!$ldapAuthClient instanceof LdapAuth) {
            throw new InvalidArgumentException("The specified ID does not match to a LDAP AuthClient");
        }

        return $ldapAuthClient;
    }
}
