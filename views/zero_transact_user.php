<?php
// (c)Perez Karjee(www.aas9.in)
// Project Site www.aas9.in/zerocms
// Created March 2014
require_once '../includes/db.kate.php';
require_once '../includes/zero_http_functions.kate.php';

$dbx = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD) or
    die ('Unable to connect. Check your connection parameters.');

mysqli_select_db(MYSQL_DB, $dbx) or die(mysqli_error($dbx));

if (isset($_REQUEST['action'])) {

    switch ($_REQUEST['action']) {
    case 'Login':
        $email = (isset($_POST['email'])) ? $_POST['email'] : '';
        $password = (isset($_POST['password'])) ? $_POST['password'] : '';
        $sql = 'SELECT
                user_id, access_level, name
            FROM
                zero_users
            WHERE
                email = "' . mysqli_real_escape_string($email, $dbx) . '" AND
                password = PASSWORD("' . mysqli_real_escape_string($password,
                    $dbx) . '")';
        $result = mysqli_query($sql, $dbx) or die(mysqli_error($dbx));
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_array($result);
            extract($row);
            session_start();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['access_level'] = $access_level;
            $_SESSION['name'] = $name;
        }
        mysqli_free_result($result);
        redirect('../index.php');
        break;

    case 'Logout':
        session_start();
        session_unset();
        session_destroy();
        redirect('../index.php');
        break;

		//if (filter_var($newslettermail, FILTER_VALIDATE_EMAIL)) {
//$query = "INSERT INTO newslettermail(email)VALUES('$newslettermail')"; 
//}
    case 'Create Account':
        $name = (isset($_POST['name'])) ? $_POST['name'] : '';
        $email = (isset($_POST['email'])) ? $_POST['email'] : '';
        $password_1 = (isset($_POST['password_1'])) ? $_POST['password_1'] : '';
        $password_2 = (isset($_POST['password_2'])) ? $_POST['password_2'] : '';
        $password = ($password_1 == $password_2) ? $password_1 : '';
		//check if not empty and validate email
		if(!empty($name) && !empty($email) && !empty($password) && filter_var($email, FILTER_VALIDATE_EMAIL)
		&& preg_match('/^[A-Za-z0-9]{1,255}$/', $name) && preg_match('/^[A-Za-z0-9]{6,32}$/', $password))
		{
            $sql = 'INSERT INTO zero_users
                    (email, password, name)
                VALUES
                ("' . mysqli_real_escape_string($email, $dbx) . '",
                PASSWORD("' . mysqli_real_escape_string($password, $dbx) . '"), 
                "' . mysqli_real_escape_string($name, $dbx) . '")';
            mysqli_query($sql, $dbx) or die(mysqli_error($dbx));

            session_start();
            $_SESSION['user_id'] = mysqli_insert_id($dbx);
            $_SESSION['access_level'] = 1;
            $_SESSION['name'] = $name;
        }
        redirect('../index.php');
        break;

    case 'Modify Account':
        $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : '';
        $email = (isset($_POST['email'])) ? $_POST['email'] : '';
        $name = (isset($_POST['name'])) ? $_POST['name'] : '';
        $access_level = (isset($_POST['access_level'])) ? $_POST['access_level']
            : '';
				//check if not empty and validate email
        if (!empty($user_id) && !empty($name) && !empty($email) &&
            !empty($access_level) && filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^[A-Za-z0-9]{1,255}$/', $name))
			{
            $sql = 'UPDATE `zero_users` SET
                    email = "' . mysqli_real_escape_string($email, $dbx) . '",
                    name = "' . mysqli_real_escape_string($name, $dbx) . '",
                    access_level = "' . mysqli_real_escape_string($access_level, $dbx) . '"
                WHERE
                    user_id = ' . $user_id;
                 
      	
                    
            mysqli_query($sql, $dbx) or die(mysqli_error($dbx));
        }
        redirect(''.$site.'/views/zero_admin.php');
        break;

    case 'Recover Password':
        $email = (isset($_POST['email'])) ? $_POST['email'] : '';
        if (!empty($email)) {
            $sql = 'SELECT email FROM zero_users WHERE email="' .
                mysqli_real_escape_string($email, $dbx) . '"';
            $result = mysqli_query($sql, $dbx) or die(mysqli_error($dbx));
            if (mysqli_num_rows($result) > 0) {
                $password = strtoupper(substr(sha1(time()), rand(0, 32), 8));
                $subject = 'Comic site password reset';
                $body = 'Forgot your password? we will send you a new one. ' . 
                    'We\'ve reset it for you!' . "\n\n";
                $body .= 'Your new password is: ' . $password;
                mail($email, $subject, $body);
            }
            mysqli_free_result($result);
        }
        redirect(''.$site.'/views/zero_login.php');
        break;

    case 'Change my info':
        session_start();
        $email = (isset($_POST['email'])) ? $_POST['email'] : '';
        $name = (isset($_POST['name'])) ? $_POST['name'] : '';
		//check if not empty and validate email
        if (!empty($name) && !empty($email) && !empty($_SESSION['user_id']) && filter_var($email, FILTER_VALIDATE_EMAIL)
		&& preg_match('/^[A-Za-z0-9]{1,255}$/', $name))
        {
            $sql = 'UPDATE zero_users SET
                    email = "' . mysqli_real_escape_string($email, $dbx) . '",
                    name = "' . mysqli_real_escape_string($name, $dbx) . '"
                WHERE
                    user_id = ' . $_SESSION['user_id'];
            mysqli_query($sql, $dbx) or die(mysqli_error($dbx));
        }
        redirect(''.$site.'/views/zero_cpanel.php');
        break;
    default:
        redirect('../index.php');
    }
} else {
    redirect('../index.php');
}
?>
