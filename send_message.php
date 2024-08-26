<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $username = $_COOKIE['username'];
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $new_message = $username . '; ' . $message . PHP_EOL;
        file_put_contents('messages.txt', $new_message, FILE_APPEND);
        echo 'Message sent';
    }
}
?>
