<?php
//Start Session
session_start();

//Setup Globals
$GLOBALS['Path'] = $_SERVER["DOCUMENT_ROOT"] . '/'; //Site Path for anything that needs it.

//Setup requirements
require_once("config/PHPConfig.php");               //php.ini config
require_once("config/tracking.php");                //Google Analytics
require_once("classes/class_logging.php");          //Logging
require_once("classes/class_connection.php");       //Connection.
require_once("classes/class_functions.php");        //Functions.
require_once("classes/class_navigation.php");       //Navigation.
require_once("classes/class_user.php");             //User.
require_once("classes/class_authentication.php");   //Authentication.
require_once("classes/class_blog.php");             //Blog.

//Log each index visit.
Write_Log('views',"Site has logged a page view.");
?>
<HTML>
    <HEAD>
        <link href="css/frontend.css" rel="stylesheet" type="text/css">
        <?php $User = new User($_SESSION['ID']); $User->Display_Theme(); ?>
        <TITLE>
        DustinHendrickson.com - Official Site
        </TITLE>
    </HEAD>

    <BODY>

        <div id="Top-Bar">
            <div class="Login_Area">
                <?php Navigation::write_Login(); ?>
            </div>
                <?php Navigation::write_Private(); ?>
        </div>

        <div id="BodyWrapper">

        <?php Navigation::write_Login_Error(); ?>

        <div id="Header">
            <a href='/' class="Logo"></a>
        </div>

        <div id="Public-Navigation">
            <?php Navigation::write_Public(); ?>
        </div>

        <div id="Content">
            <?php Functions::Display_View(Functions::Get_View()); ?>
        </div>

        <div id="Footer">

        <table width=100% padding=10px>
            <tr>
                <td width="25%" height="25px"><b>Friends</b></td>
                <td width="25%" height="25px"><b>About</b></td>
                <td width="25%" height="25px"><b>Contact</b></td>
                <td width="25%" height="25px"><b>Community</b></td>
            </tr>
            <tr>
                <td width="25%" height="25px"><a href='http://omfg.fm' target='_blank'>OMFG.fm</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
            </tr>
            <tr>
                <td width="25%" height="25px"><a href='http://kylemccarley.com' target='_blank'>KyleMcCarley.com</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
            </tr>
            <tr>
                <td width="25%" height="25px"><a href='http://fake.com' target='_blank'>Fake Site.com</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
            </tr>
            <tr>
                <td width="25%" height="25px"><a href='http://blarg.net' target='_blank'>BLARG.net</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
                <td width="25%" height="25px"><a href='' target='_blank'>Link</a></td>
            </tr>
        </table>

        </div>

    </BODY>
</HTML>
