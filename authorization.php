<?php

require_once 'phpgen_settings.php';
require_once 'components/application.php';
require_once 'components/security/permission_set.php';
require_once 'components/security/user_authentication/table_based_user_authentication.php';
require_once 'components/security/grant_manager/user_grant_manager.php';
require_once 'components/security/grant_manager/composite_grant_manager.php';
require_once 'components/security/grant_manager/hard_coded_user_grant_manager.php';
require_once 'components/security/grant_manager/table_based_user_grant_manager.php';
require_once 'components/security/table_based_user_manager.php';

include_once 'components/security/user_identity_storage/user_identity_session_storage.php';

require_once 'database_engine/mysql_engine.php';

$grants = array('defaultUser' => 
        array('vacina' => new PermissionSet(false, false, false, false),
        'especie' => new PermissionSet(false, false, false, false),
        'animal' => new PermissionSet(false, false, false, false),
        'usuário' => new PermissionSet(false, false, false, false),
        'perfil' => new PermissionSet(false, false, false, false),
        'funcionário' => new PermissionSet(false, false, false, false))
    ,
    'Anthonio Barros Silva' => 
        array('vacina' => new AdminPermissionSet(),
        'especie' => new AdminPermissionSet(),
        'animal' => new AdminPermissionSet(),
        'usuário' => new AdminPermissionSet(),
        'perfil' => new AdminPermissionSet(),
        'funcionário' => new AdminPermissionSet())
    ,
    'Andressa Aguiar Barros' => 
        array('vacina' => new PermissionSet(false, false, false, false),
        'especie' => new PermissionSet(false, false, false, false),
        'animal' => new PermissionSet(true, false, true, false),
        'usuário' => new PermissionSet(false, false, false, false),
        'perfil' => new PermissionSet(false, false, false, false),
        'funcionário' => new PermissionSet(false, false, false, false))
    ,
    'Admin' => 
        array('vacina' => new AdminPermissionSet(),
        'especie' => new AdminPermissionSet(),
        'animal' => new AdminPermissionSet(),
        'usuário' => new AdminPermissionSet(),
        'perfil' => new AdminPermissionSet(),
        'funcionário' => new AdminPermissionSet())
    ,
    'guest' => 
        array('vacina' => new PermissionSet(false, false, false, false),
        'especie' => new PermissionSet(false, false, false, false),
        'animal' => new PermissionSet(false, false, false, false),
        'usuário' => new PermissionSet(false, false, false, false),
        'perfil' => new PermissionSet(false, false, false, false),
        'funcionário' => new PermissionSet(false, false, false, false))
    );

$appGrants = array('defaultUser' => new PermissionSet(true, false, false, false),
    'Anthonio Barros Silva' => new AdminPermissionSet(),
    'Andressa Aguiar Barros' => new PermissionSet(false, false, false, false),
    'Admin' => new AdminPermissionSet(),
    'guest' => new PermissionSet(true, false, false, false));

$dataSourceRecordPermissions = array();

$tableCaptions = array('vacina' => 'Vacina',
'especie' => 'Especie',
'animal' => 'Animal',
'usuário' => 'Usuário',
'perfil' => 'Perfil',
'funcionário' => 'Funcionário');

$usersTableInfo = array(
    'TableName' => 'usuário',
    'UserId' => 'ID',
    'UserName' => 'Nome',
    'Password' => 'Senha',
    'Email' => '',
    'UserToken' => '',
    'UserStatus' => ''
);

function EncryptPassword($password, &$result)
{

}

function VerifyPassword($enteredPassword, $encryptedPassword, &$result)
{

}

function BeforeUserRegistration($username, $email, $password, &$allowRegistration, &$errorMessage)
{

}    

function AfterUserRegistration($username, $email)
{

}    

function PasswordResetRequest($username, $email)
{

}

function PasswordResetComplete($username, $email)
{

}

function CreatePasswordHasher()
{
    $hasher = CreateHasher('');
    if ($hasher instanceof CustomStringHasher) {
        $hasher->OnEncryptPassword->AddListener('EncryptPassword');
        $hasher->OnVerifyPassword->AddListener('VerifyPassword');
    }
    return $hasher;
}

function CreateTableBasedGrantManager()
{
    return null;
}

function CreateTableBasedUserManager() {
    global $usersTableInfo;
    return new TableBasedUserManager(MyPDOConnectionFactory::getInstance(), GetGlobalConnectionOptions(), $usersTableInfo, CreatePasswordHasher(), false);
}

function SetUpUserAuthorization()
{
    global $grants;
    global $appGrants;
    global $dataSourceRecordPermissions;

    $hasher = CreatePasswordHasher();

    $hardCodedGrantManager = new HardCodedUserGrantManager($grants, $appGrants);
    $tableBasedGrantManager = CreateTableBasedGrantManager();
    $grantManager = new CompositeGrantManager();
    $grantManager->AddGrantManager($hardCodedGrantManager);
    if (!is_null($tableBasedGrantManager)) {
        $grantManager->AddGrantManager($tableBasedGrantManager);
    }

    $userAuthentication = new TableBasedUserAuthentication(new UserIdentitySessionStorage(), true, $hasher, CreateTableBasedUserManager(), false, false, false);

    GetApplication()->SetUserAuthentication($userAuthentication);
    GetApplication()->SetUserGrantManager($grantManager);
    GetApplication()->SetDataSourceRecordPermissionRetrieveStrategy(new HardCodedDataSourceRecordPermissionRetrieveStrategy($dataSourceRecordPermissions));
}
