<?php

namespace Krixon\MultiFactorAuthExamples;

use Krixon\MultiFactorAuth\Barcode\GoogleQRGenerator;
use Krixon\MultiFactorAuth\Barcode\GoQRGenerator;
use Krixon\MultiFactorAuth\Barcode\Options;
use Krixon\MultiFactorAuth\HTTP\CurlClient;
use Krixon\MultiFactorAuth\MultiFactorAuth;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

session_start();

$issuer      = 'Example Issuer';
$accountName = 'jane.doe@example.com';
$mfa         = MultiFactorAuth::default($issuer);

function barcode_generator()
{
    $client = new CurlClient();

    switch ($_REQUEST['barcode-generator'] ?? 'google') {
        case 'goqr':
            return new GoQRGenerator($client);
        default:
            return new GoogleQRGenerator($client);
    }
}

function reset(MultiFactorAuth $mfa)
{
    $_SESSION['counter']                = 1;
    $_SESSION['secret']                 = $mfa->generateSecret();
    $_SESSION['barcode-generator']      = barcode_generator();
    $_SESSION['barcode-generator-name'] = 'google';
    $_SESSION['barcode-width']          = 200;
    $_SESSION['barcode-height']         = 200;
    $_SESSION['barcode-format']         = 'png';
    $_SESSION['barcode-margin']         = 1;
    $_SESSION['barcode-ec']             = 'L';
    $_SESSION['barcode-fgcolor']        = '#000000';
    $_SESSION['barcode-bgcolor']        = '#ffffff';
    $_SESSION['barcode-charset-source'] = 'UTF-8';
    $_SESSION['barcode-charset-target'] = 'UTF-8';
}

if (!isset($_SESSION['counter']) || !isset($_SESSION['secret'])) {
    reset($mfa);
} else {
    switch ($_REQUEST['action'] ?? null) {
        case 'reset_counter':
            $_SESSION['counter'] = $_REQUEST['reset_counter'];
            break;
        case 'save':
            $_SESSION['barcode-generator']      = barcode_generator();
            $_SESSION['barcode-generator-name'] = $_REQUEST['barcode-generator'] ?? 'google';
            $_SESSION['barcode-width']          = $_REQUEST['barcode-width'] ?? 200;
            $_SESSION['barcode-height']         = $_REQUEST['barcode-height'] ?? 200;
            $_SESSION['barcode-format']         = $_REQUEST['barcode-format'] ?? 'png';
            $_SESSION['barcode-margin']         = $_REQUEST['barcode-margin'] ?? 1;
            $_SESSION['barcode-ec']             = $_REQUEST['barcode-ec'] ?? 'L';
            $_SESSION['barcode-fgcolor']        = $_REQUEST['barcode-fgcolor'] ?? '#000000';
            $_SESSION['barcode-bgcolor']        = $_REQUEST['barcode-bgcolor'] ?? '#ffffff';
            $_SESSION['barcode-charset-source'] = $_REQUEST['barcode-charset-source'] ?? 'UTF-8';
            $_SESSION['barcode-charset-target'] = $_REQUEST['barcode-charset-target'] ?? 'UTF-8';
            break;
        case 'reset':
            reset($mfa);
            break;
    }
}

$mfa->setBarcodeGenerator($_SESSION['barcode-generator']);

$options = new Options(
    $_SESSION['barcode-width'],
    $_SESSION['barcode-height'],
    $_SESSION['barcode-format'],
    $_SESSION['barcode-margin'],
    $_SESSION['barcode-ec'],
    $_SESSION['barcode-fgcolor'],
    $_SESSION['barcode-bgcolor'],
    $_SESSION['barcode-charset-source'],
    $_SESSION['barcode-charset-target']
);

$timeBasedQr  = $mfa->generateTimeBasedBarcode($_SESSION['secret'], $accountName, $options);
$eventBasedQr = $mfa->generateEventBasedBarcode($_SESSION['secret'], $accountName, $options);
$result       = null;

if (isset($_REQUEST['time_code'])) {
    $result = $mfa->verifyTimeBasedCode($_SESSION['secret'], $_REQUEST['time_code']);
} elseif (isset($_REQUEST['event_code'])) {
    $result = $mfa->verifyEventBasedCode($_SESSION['secret'], $_REQUEST['event_code'], $_SESSION['counter']);
    if ($result) {
        ++$_SESSION['counter'];
    }
}

$timeBasedCode  = $mfa->generateTimeBasedCode($_SESSION['secret']);
$eventBasedCode = $mfa->generateEventBasedCode($_SESSION['secret'], $_SESSION['counter']);

?>
<html>
<head>
    <title>MultiFactorAuth Sandbox</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css"
          integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
</head>
<body>
<div class="container pt-5">
    <?php if (null !== $result) : ?>
        <div class="alert alert-<?= $result ? 'success' : 'danger' ?> mb-5">
            Code was <?= $result ? '' : '<strong>not</strong>' ?> verified successfully.
        </div>
    <?php endif ?>
    <div class="row text-center mb-5">
        <div class="col-5">
            <h2 class="mb-3">Time Based (TOTP)</h2>
            <img src="<?= $timeBasedQr->dataUri() ?>" class="img-thumbnail d-block ml-auto mr-auto mb-3">
            <div class="row text-left">
                <div class="col-4 d-flex align-items-center">Current code</div>
                <div class="col"><code><?= $timeBasedCode->toString() ?></code></div>
            </div>
            <form method="post" class="row text-left">
                <div class="col d-flex align-items-center">Code</div>
                <div class="col"><input type="number" name="time_code" class="form-control"></div>
                <div class="col"><input type="submit" value="Verify" class="btn btn-primary btn-block"></div>
            </form>
        </div>
        <div class="col-5 offset-2">
            <h2 class="mb-3">Event Based (HOTP)</h2>
            <img src="<?= $eventBasedQr->dataUri() ?>" class="img-thumbnail d-block ml-auto mr-auto mb-3">
            <div class="row text-left">
                <div class="col-4 d-flex align-items-center">Current code</div>
                <div class="col"><code><?= $eventBasedCode->toString() ?></code></div>
            </div>
            <form method="post" class="row text-left">
                <div class="col d-flex align-items-center">Current counter</div>
                <div class="col">
                    <input type="number" name="reset_counter" value="<?= $_SESSION['counter'] ?>" class="form-control"
                           min="1">
                </div>
                <div class="col">
                    <button name="action" value="reset_counter" class="btn btn-primary btn-block">Set</button>
                </div>
            </form>
            <form method="post" class="row text-left">
                <div class="col d-flex align-items-center">Code</div>
                <div class="col"><input type="number" name="event_code" class="form-control"></div>
                <div class="col"><input type="submit" value="Verify" class="btn btn-primary btn-block"></div>
            </form>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col text-center lead">
            <h3>Shared Secret</h3>
            <code><?= chunk_split($_SESSION['secret'], 4) ?></code>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col">
            <h3>Barcode Options</h3>
            <div class="alert alert-info">
                Not all options are supported by all generators. If an option does not seem to do anything it is
                probably because the selected generator does not support it.
            </div>
            <form method="post" class="form mt-3">
                <div class="row">
                    <div class="form-group col">
                        <label for="barcode-generator">Generator</label>
                        <select class="form-control" id="barcode-generator" name="barcode-generator">
                            <?php foreach (['google' => 'Google', 'goqr' => 'GoQR'] as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $_SESSION['barcode-generator-name'] === $value ? ' selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="barcode-bgcolor">Background Color</label>
                        <input class="form-control" id="barcode-bgcolor" name="barcode-bgcolor"
                               value="<?= $_SESSION['barcode-bgcolor'] ?>">
                    </div>
                    <div class="form-group col">
                        <label for="barcode-fgcolor">Foreground Color</label>
                        <input class="form-control" id="barcode-fgcolor" name="barcode-fgcolor"
                               value="<?= $_SESSION['barcode-fgcolor'] ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col">
                        <label for="barcode-width">Width</label>
                        <input class="form-control" id="barcode-width" name="barcode-width" min="50" max="1000"
                               value="<?= $_SESSION['barcode-width'] ?>">
                    </div>
                    <div class="form-group col">
                        <label for="barcode-height">Height</label>
                        <input class="form-control" id="barcode-height" name="barcode-height" min="50" max="1000"
                               value="<?= $_SESSION['barcode-height'] ?>">
                    </div>
                    <div class="form-group col">
                        <label for="barcode-margin">Margin</label>
                        <input class="form-control" type="number" id="barcode-margin" name="barcode-margin"
                               value="<?= $_SESSION['barcode-margin'] ?>">
                    </div>
                </div>
                <button name="action" value="save" class="btn btn-primary">Save Changes</button>
                <hr>
                <button name="action" value="reset" class="btn btn-primary">Start Over</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
