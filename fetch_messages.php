<?php
$messages = [];
if (file_exists('messages.txt')) {
    $messages = file('messages.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}
foreach ($messages as $msg) {
    list($msg_username, $msg_content) = explode('; ', $msg, 2);
    $msg_profile_pic = 'profile_pictures/' . $msg_username . '.jpg';
    if (!file_exists($msg_profile_pic)) {
        $msg_profile_pic = 'default_profile.jpg';
    }
    echo '<div class="chat-message">
            <img src="' . htmlspecialchars($msg_profile_pic) . '" alt="Profile Picture">
            <div>
                <p class="username">' . htmlspecialchars($msg_username) . '</p>
                <p>' . htmlspecialchars($msg_content) . '</p>
            </div>
        </div>';
}
?>
