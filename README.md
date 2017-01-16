# JiraServiceDesk_DisableWelcomeMessages
Disable Welcome E-Mail to New Customers by creating customer before mail is processed

This is a dirty port of https://github.com/lesstif/php-jira-rest-client to make a quick but working fix for https://jira.atlassian.com/browse/JSD-1708

Requirements:
 * Webserver with PHP (I think latest 5.6 is best)
 * Exchange mail server with a dedicated mailbox for your service desk
 * Jira Service Desk user account for API login :)

Most is PHP which you should run with PHP on a simple webserver.
There is a Powershell script (FixJiraUserAccounts.ps1) which you should place near your Exchange server.

- Make a mailbox rule that puts every new mail in a subfolder
- Run the powershell script (which has a nice loop) to process the mail and put the mail back in the root folder afterwards
- Let Jira Service Desk pop the mailbox

There is also a built-in functionality to auto match a new issue to an organization based on e-mail address.

Good luck