<style>
    form {
        border: 3px solid #f1f1f1;
    }

    /* Full-width inputs */
    input[type=text],
    input[type=password] {
        width: 50%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
    }

    /* Set a style for all buttons */
    button {
        background-color: #04AA6D;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 50%;
    }

    /* Add a hover effect for buttons */
    button:hover {
        opacity: 0.8;
    }

    /* Add padding to containers */
    .container {
        padding: 16px;
    }

</style>

<div class="container" style="text-align: center;">
    <form action="" method="post">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password"> </br> </br>
        <input type="submit" value="Login">
    </form>
</div>

<?php
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == 'onerentcar' && $password == 'rentcarone_.') {

        echo 'Login success';

        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['logged_in'] = true;
        setcookie('username', $username, time() + (86400 * 30), "/");

        sleep(2);
        header('Location: index.php');
    } else {
        echo '<script>alert("Invalid Username or Password")</script>';
    }
}
?>