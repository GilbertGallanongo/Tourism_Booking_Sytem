param(
    [Parameter(Mandatory = $true)]
    [string] $ApiKey,

    [string] $WorkspaceName = "Gilbert's Workspace",

    [string] $CollectionPath = ".\postman\Tourism_Booking_System_API.postman_collection.json",

    [string] $EnvironmentPath = ".\postman\Tourism_Booking_System.postman_environment.json"
)

$ErrorActionPreference = "Stop"

function Invoke-PostmanApi {
    param(
        [Parameter(Mandatory = $true)]
        [string] $Method,

        [Parameter(Mandatory = $true)]
        [string] $Uri,

        [object] $Body = $null
    )

    $headers = @{
        "X-Api-Key" = $ApiKey
        "Accept" = "application/json"
    }

    if ($null -eq $Body) {
        return Invoke-RestMethod -Method $Method -Uri $Uri -Headers $headers
    }

    $headers["Content-Type"] = "application/json"

    return Invoke-RestMethod `
        -Method $Method `
        -Uri $Uri `
        -Headers $headers `
        -Body ($Body | ConvertTo-Json -Depth 100)
}

$baseUri = "https://api.getpostman.com"

Write-Host "Finding Postman workspace: $WorkspaceName"
$workspacesResponse = Invoke-PostmanApi -Method "GET" -Uri "$baseUri/workspaces"
$workspace = $workspacesResponse.workspaces | Where-Object { $_.name -eq $WorkspaceName } | Select-Object -First 1

if (-not $workspace) {
    $available = ($workspacesResponse.workspaces | ForEach-Object { $_.name }) -join ", "
    throw "Workspace '$WorkspaceName' was not found. Available workspaces: $available"
}

Write-Host "Using workspace: $($workspace.name) ($($workspace.id))"

$collection = Get-Content -LiteralPath $CollectionPath -Raw | ConvertFrom-Json
$environment = Get-Content -LiteralPath $EnvironmentPath -Raw | ConvertFrom-Json

Write-Host "Importing collection..."
$collectionResponse = Invoke-PostmanApi `
    -Method "POST" `
    -Uri "$baseUri/collections?workspace=$($workspace.id)" `
    -Body @{ collection = $collection }

Write-Host "Importing environment..."
$environmentResponse = Invoke-PostmanApi `
    -Method "POST" `
    -Uri "$baseUri/environments?workspace=$($workspace.id)" `
    -Body @{ environment = $environment }

Write-Host "Done."
Write-Host "Collection: $($collectionResponse.collection.name)"
Write-Host "Environment: $($environmentResponse.environment.name)"
