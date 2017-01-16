Import-Module -Name "C:\Program Files\Microsoft\Exchange\Web Services\2.2\Microsoft.Exchange.WebServices.dll"

## CONNECT WITH EWS FOR ARCHIVING
$exchService = New-Object Microsoft.Exchange.WebServices.Data.ExchangeService([Microsoft.Exchange.WebServices.Data.ExchangeVersion]::Exchange2013_SP1, [System.TimeZoneInfo]::Local)
$exchService.Credentials = New-Object Microsoft.Exchange.WebServices.Data.WebCredentials("[SUPPORT@DOMAIN.COM", "[PASSWORD]")
$exchService.AutodiscoverUrl("username@domain.tld")


## GET INBOX
$Inbox = [Microsoft.Exchange.WebServices.Data.Folder]::Bind($exchService,[Microsoft.Exchange.WebServices.Data.WellKnownFolderName]::Inbox)

$folderView = new-object Microsoft.Exchange.WebServices.Data.FolderView(1)
$SfSearchFilter = new-object Microsoft.Exchange.WebServices.Data.SearchFilter+IsEqualTo([Microsoft.Exchange.WebServices.Data.FolderSchema]::DisplayName,"Processing")
$FolderResults = $Inbox.FindFolders($SfSearchFilter,$folderView)

##GET PROCESSING FOLDER
$ProcessingFolder = $FolderResults.Folders.Item(0)
Start-Transcript -Path "C:\ManageMailboxScripts\Jiraccounts\Log.txt"

while($true){
    $psPropset = new-object Microsoft.Exchange.WebServices.Data.PropertySet([Microsoft.Exchange.WebServices.Data.BasePropertySet]::FirstClassProperties)
    $MailItems = $ProcessingFolder.FindItems((New-Object Microsoft.Exchange.WebServices.Data.ItemView(100)))
    if($MailItems.TotalCount -gt 0){
        $exchService.LoadPropertiesForItems($MailItems,$psPropset) | Out-Null
        Foreach($MailItem in $MailItems)
        {
            echo $MailItem.From.Name 
            echo $MailItem.From.Address
            
            $url = ("https://your-php-server/webhook/IssueTest.php?email="+$MailItem.From.Address)
            $WebRequest = Invoke-WebRequest -Method Get -Uri $url -UseBasicParsing
            echo $WebRequest.Content
            echo "`n"
            $Moved = $Mailitem.Move($Inbox.Id)
    
        }
    }
    Start-Sleep -s 10
}
Stop-Transcript