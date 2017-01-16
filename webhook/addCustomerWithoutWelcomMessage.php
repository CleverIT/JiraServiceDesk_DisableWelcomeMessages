<?php

require ('../vendor/autoload.php');

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\ServiceDesk\OrganizationService;
use JiraRestApi\ServiceDesk\ServiceDeskService;
use JiraRestApi\User\UserService;
use JiraRestApi\Group\GroupService;
use JiraRestApi\JiraException;

header('Content-Type: text/html; charset=utf-8');

$config = new ArrayConfiguration(
    [
        'jiraHost' => 'https://company.atlassian.net',
        'jiraUser' => 'log@email.tld',
        'jiraPassword' => 'SecurePassword',
    ]
);

$emailConfig = [
    '@customerdomain.tld' => 'CustomerNameInJira',
    '@customerdomain2.tld' => 'CustomerName2InJira'
];

if(!isset($_GET['email'])) {
    die('Please supply email');
}


try {

    $email = $_GET['email'];
    $user = new UserService($config);
    $param = ['username' => $email];
    $userSearch = $user->search($param);
    $userExists = false;
    if (count($userSearch) > 0) {
        foreach ($userSearch as $userSearchEntry) {
            if ($userSearchEntry->emailAddress == $email) {
                $userExists = true;
            }
        }
    }
    if ($userExists) die ('User already exists :)');

    $username = $email;

    $user = new UserService($config);
    //$y = $user->get(['username' => $username]);
    $y = $user->add($username, 'DefaultDummyPass1!:)', $email, $email);

    echo '<pre>' . PHP_EOL;

    print_r($y);

    $group = new GroupService($config);
    //$z = $group->getMember('jira-servicedesk-users');
    //print_r($z);

    try {
        $x = $group->removeUser('jira-servicedesk-users', $username);
        print_r($x);
    } catch (Exception $e) {
        print("Error Occured! " . $e->getMessage());
    }

    $servicedesk = new ServiceDeskService($config);
    $z = $servicedesk->addCustomer(2, [$username]);
    print_r($z);

    $orgName = null;
    foreach ($emailConfig as $emailConfigName => $emailConfigValue) {
        if (strpos($email, $emailConfigName) !== false) {
            $orgName = $emailConfigValue;
        }
    }

    if ($orgName != null) {
        echo 'Organziation based on e-mail: ' . $orgName . PHP_EOL;

        $org = new OrganizationService($config);
        $x = 0;
        $allOrgs = [];
        do {
            $orgValue = $org->get($x * 50, 50);
            foreach ($orgValue->values as $orgEntry) {
                $allOrgs[$orgEntry->name] = $orgEntry->id;
            }
            $x++;
            //if ($size)
        } while ($orgValue->size == 50);

        if (isset($allOrgs[$orgName])) {
            //print_r($org->get());
            print_r($org->addUser($allOrgs[$orgName], [$username]));
            echo 'Organization found' . PHP_EOL;
        }
    }



} catch (JiraException $e) {
    print("Error Occured! " . $e->getMessage());
}


