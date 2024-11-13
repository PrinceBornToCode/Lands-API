<?php
require("vendor/autoload.php");

use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\Eth;
use Web3\Contract;
$private_key = 'ac0974bec39a17e36ba4a6b4d238ff944bacb478cbed5efcae784d7bf4f2ff80';

//$provider = new Web3(new HttpProvider('http://localhost:8545', $private_key));
// Load the contract address and ABI from JSON files
$contractDataAddress = json_decode(file_get_contents('../../../../htdocs/hardhatNode/ignition/deployed_contracts/LandTitleDeed_address.json'), true);

$contractDataABI = json_decode(file_get_contents('../../../../htdocs/hardhatNode/ignition/deployed_contracts/LandTitleDeed_abi.json'), true);
$contractAddress = $contractDataAddress['address'];
$contractAbi = $contractDataABI;

// echo json_encode($contractAbi)
$contract = new Contract('http://localhost:8545', $contractDataABI);


?>