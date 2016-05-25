# Your script here
param([string]$src = "foo", [string]$dest = "bar", [string]$username, [string]$password)

$userPass = ConvertTo-SecureString $password -AsPlainText -Force

$Credential = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $username, $userPass

try{
    New-PSDrive -Name Z -PSProvider FileSystem -Root $dest -Credential $Credential;

    $date   = GET-DATE;
    $theDay = "" + $date.Day;
    $tail   = "" + $date.Year + $date.Month + $theDay.PadLeft(2, '0');

    # Check if the folder exist if not create it 
    $desttoday = $dest + "\" + $tail;

    If (!(Test-Path $desttoday)) { 
       New-Item -Path $desttoday -ItemType Directory
    }

    $path = "Z:\" + $tail

    Copy-Item $src $path
    Remove-PSDrive -name Z
    WRITE-HOST "Copied file $src to $desttoday"
} catch [System.Exception]{
    continue
}

