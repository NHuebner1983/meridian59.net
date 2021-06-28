<?php

require "vendor/autoload.php";

use App\Blakserv\Commands;

$download_game_url = 'http://update.meridian59.us/download/M59-115.Patcher.msi';

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();

$error                   = "";
$success                 = false;
$success_account_message = "You're all set!<br><br><a href=\"{$download_game_url}\">Download Server 115 Game Installer</a>";

if ( ! isset($_SESSION['token']) )
{
    // Only instantiate.
    $_SESSION['token'] = null;
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    /**
     * Attempt to receive form post data.
     */
    $token = $_POST['token'];

    if ( $token != $_SESSION['token'] )
    {
        $error = 'Your page token has changed. Click `Create Account` again now that you have a new token.';
    }

    $new_email        = trim($_POST['new-email']);
    $new_user         = trim($_POST['new-user']);
    $new_pass         = $_POST['new-pass'];
    $new_pass_confirm = $_POST['new-pass-confirm'];

    $is_valid_user = alphanumeric($new_user) == $new_user;

    if ( ! $error && empty($new_email) )
    {
        $error = "Your email cannot be blank.";
    }
    if ( ! $error && ( ! strstr($new_email, ".") || ! strstr($new_email, "@") ) )
    {
        $error = "Your email is invalid.";
    }
    if ( ! $error && empty($new_user) )
    {
        $error = "Your username cannot be blank.";
    }
    if ( ! $error && ! $is_valid_user )
    {
        $error = "Your username may only contain letters and numbers.";
    }
    if ( ! $error && empty($new_pass) )
    {
        $error = "Your password cannot be blank.";
    }
    if ( ! $error && strstr($new_pass, " ") )
    {
        $error = "Your password may not contain spaces.";
    }
    if ( ! $error && $new_pass != $new_pass_confirm )
    {
        $error = "Your passwords didn't match.";
    }

    if ( ! $error )
    {
        /**
         * Check for an existing account name....
         */
        $commands = new Commands(2);

        $accounts = explode("\n", $commands->blakserv->execute('show accounts', "-----ready-----"));

        $exploded = [];

        foreach ( $accounts as $row )
        {
            $account = explode(" ", no_double_spaces($row));

            $exploded[] = array_values(no_blank_headers($account));
        }

        if ( count($exploded) < 5 )
        {
            $error = "The Meridian 59 server may be down for maintenance at this time. Please wait for the server to become available again.";
        }
        else
        {
            $account_exists = false;

            foreach ( $exploded as $index => $account )
            {
                if ( $index < 2 )
                {
                    continue;
                }

                $account_id       = $account[0];
                $account_username = $account[1];

                if ( strtolower(trim($new_user)) == strtolower(trim($account_username)) )
                {
                    $account_exists = true;
                    $error          = "That username is already taken.";
                    break;
                }
            }

            if ( ! $account_exists )
            {
                /**
                 * CREATE ACCOUNT NOW
                 */
                $response = $commands->blakserv->execute("create account user {$new_user} {$new_pass} ${new_email}", "-----ready-----");

                if ( ! strstr($response, 'Created ACCOUNT') )
                {
                    $error = "The server did not respond with a confirmation of the account being created. If you try to create the account again and it already exists, you may need to contact a game administrator for help.";
                }
                else
                {
                    /**
                     * Get the account ID from the response...
                     */
                    $new_account_id = (int)numeric(substr(trim($response), -10));

                    if ( ! $new_account_id )
                    {
                        $error = "The server did not respond with an Account ID. If you try to create the account again and it already exists, you may need to contact a game administrator for help.";
                    }
                    else
                    {
                        /**
                         * Create 2 slots for the user
                         */
                        $response = $commands->blakserv->execute("create user {$new_account_id}", "-----ready-----");
                        $response = $commands->blakserv->execute("create user {$new_account_id}", "-----ready-----");
                        $success  = true;
                    }
                }
            }
        }
    }
}

$_SESSION['token'] = uniqid();

function alphanumeric($str)
{
    return preg_replace("/[^a-zA-Z0-9]/", "", $str);
}

function numeric($str)
{
    return preg_replace("/[^0-9]/", "", $str);
}

function no_blank_headers($arr)
{
    foreach ( $arr as $key => $item )
    {
        if ( $key )
        {
            break;
        }

        if ( empty($item) )
        {
            unset($arr[$key]);
        }
    }

    return $arr;
}

function no_double_spaces($str)
{
    while ( strstr($str, "  ") )
    {
        $str = str_replace("  ", " ", $str);
    }

    return $str;
}

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Meridian59.us - Play Now!</title>
    <style>
        body {
            background-image: url(/images/dark-angels.png);
            background-size:  cover;
            font-family:      "Trebuchet MS", Arial;
        }
        .logo {
            right:    10px;
            top:      10px;
            position: absolute;
        }
        input, select {
            padding:   6px;
            font-size: 15px;
            margin:    1px;
            width:     75%;
        }
        .form-label {
            float:       left;
            width:       33%;
            margin-top:  15px;
            font-size:   16px;
            font-weight: bold;
        }
        .form-field {
            float:      left;
            width:      66%;
            margin-top: 5px;
        }
        .create-box {
            width:      844px;
            height:     463px;
            min-width:  844px;
            min-height: 463px;
            max-width:  844px;
            max-width:  463px;
            background: url(/images/info-window-template.png) no-repeat;
            position:   absolute;
            top:        50%;
            left:       50%;
            transform:  translate(-50%, -50%);
        }
        .create-box a {
            color: #ffd700;
        }
        .create-box .info-area {
            width:     500px;
            position:  absolute;
            left:      305px;
            top:       90px;
            padding:   10px;
            color:     #fff;
            font-size: 15px;
        }
        .create-box .download-button {
            position: absolute;
            left:     25px;
            top:      325px;
            width:    268px;
            height:   120px;
            cursor:   pointer;
        }
        .create-box .header-description {
            border-bottom:  1px solid #999;
            margin-bottom:  10px;
            padding-bottom: 10px;
        }
        .create-box .footer-description {
            margin-top:  10px;
            padding-top: 10px;
            width:       100%;
            text-align:  center;
        }
        .create-box input[type="submit"] {
            font-weight: bold;
        }
        .clear {
            clear: both;
        }
        .space {
            padding: 2px;
            margin:  1px;
        }
        .form-break {
            margin-bottom: 10px;
            position:      relative;
            clear:         both;
        }
    </style>
</head>
<body>
<div>
    <!--<img class="logo" src="/images/115-logo.png" width="309" height="123">-->
</div>

<form id="create-account" action="/play.php" method="post" onsubmit="return submitForm();">
    <input type="hidden" name="token" id="token">
    <div class="create-box">
        <div class="info-area">
            <div class="header-description">
                To create an account, fill in the form below.<br>
                You are allowed to create multiple accounts for the same email.
            </div>

            <div class="clear"></div>

            <div class="form-label">
                Email:
            </div>
            <div class="form-field">
                <input type="text" name="new-email" autocomplete="new-password" value="<?php echo trim($_POST['new-email']) ?>">
            </div>
            <div class="form-break"></div>
            <div class="form-label">
                Username:
            </div>
            <div class="form-field">
                <input type="text" name="new-user" autocomplete="new-password" value="<?php echo trim($_POST['new-user']) ?>">
            </div>
            <div class="form-break"></div>
            <div class="form-label">
                Password:
            </div>
            <div class="form-field">
                <input type="password" name="new-pass" autocomplete="new-password">
            </div>
            <div class="form-break"></div>
            <div class="form-label">
                Repeat Password:
            </div>
            <div class="form-field">
                <input type="password" name="new-pass-confirm" autocomplete="new-password">
            </div>
            <div class="form-break"></div>
            <div class="form-label">
                &nbsp;
            </div>
            <div class="form-field">
                <input type="submit" value="Create Account">
            </div>

            <div class="clear"></div>

            <div class="footer-description">
                You will need to
                <a href="<?php echo $download_game_url ?>">download the game</a> to start playing.
            </div>
        </div>
        <div class="download-button" onclick="window.location.href='<?php echo $download_game_url ?>';">
            &nbsp;
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://unpkg.com/sweetalert2@7.15.1/dist/sweetalert2.all.js"></script>

<script>

    $(function ()
    {
        $('#token').val('<?php echo $_SESSION['token'] ?>');
    });

    function submitForm()
    {
        if ( $('#token').val() == '' || $('#token').val() != '<?php echo $_SESSION['token'] ?>' )
        {
            alert("To prevent spam, you must have javascript enabled.");
            return false;
        }

        return true;
    }

    <?php
    if ( $error )
    {
    ?>
    $(function ()
    {
        swal(
            'Create Account',
            '<?php echo addslashes($error) ?>',
            'error'
        );
    })
    <?php
    }
    if ( $success )
    {
    ?>
    $(function ()
    {
        swal(
            'Create Account',
            '<?php echo addslashes($success_account_message) ?>',
            'success'
        );
    })
    <?php
    }
    ?>

</script>
</body>
</html>
