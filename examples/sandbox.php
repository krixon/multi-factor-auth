<?php

require __DIR__ . '/../vendor/autoload.php';

use Krixon\MultiFactorAuth\MultiFactorAuth;

session_start();

$issuer      = 'Example Issuer';
$accountName = 'jane.doe@example.com';
$mfa         = MultiFactorAuth::default($issuer);

if (isset($_REQUEST['reset']) || !isset($_SESSION['counter']) || !isset($_SESSION['secret'])) {
    $_SESSION['counter'] = 1;
    $_SESSION['secret']  = $mfa->generateSecret();
}

if (isset($_REQUEST['reset_counter'])) {
    $_SESSION['counter'] = $_REQUEST['reset_counter'];
}

$timeBasedQr  = $mfa->generateTimeBasedBarcode($_SESSION['secret'], $accountName);
$eventBasedQr = $mfa->generateEventBasedBarcode($_SESSION['secret'], $accountName);
$result       = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['time_code'])) {
        $result = $mfa->verifyTimeBasedCode($_POST['time_code'], $_SESSION['secret']);
    } elseif (isset($_POST['event_code'])) {
        $result = $mfa->verifyEventBasedCode($_POST['event_code'], $_SESSION['secret'], $_SESSION['counter']);
        if ($result) {
            ++$_SESSION['counter'];
        }
    }
}

$timeBasedCode  = $mfa->generateTimeBasedCode($_SESSION['secret']);
$eventBasedCode = $mfa->generateEventBasedCode($_SESSION['secret'], $_SESSION['counter']);

?>
<html>
<head>
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
                    <input type="number" name="reset_counter" value="<?= $_SESSION['counter'] ?>" class="form-control">
                </div>
                <div class="col"><input type="submit" value="Reset" class="btn btn-secondary btn-block"></div>
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
        <div class="col text-center">
            Secret: <code><?= chunk_split($_SESSION['secret'], 4) ?></code>
            <form method="post" class="form mt-3">
                <input type="submit" name="reset" value="Start Over" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>
</body>
</html>
