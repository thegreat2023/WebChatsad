<?php
// Check if cookies for username and password are set
if (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
    $username = $_COOKIE['username'];
    $password = $_COOKIE['password'];

    // Path to the user's file
    $user_file = 'users/' . $username . '.txt';

    // Verify if the user's file exists and check the password
    if (file_exists($user_file)) {
        $stored_info = file($user_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Extract the stored password
        $stored_password = '';
        foreach ($stored_info as $line) {
            if (strpos($line, 'password: ') === 0) {
                $stored_password = trim(str_replace('password: ', '', $line));
                break;
            }
        }

        if ($password === $stored_password) {
            // Handle profile picture upload if form was submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
                $target_file = 'profile_pictures/' . $username . '.jpg';

                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                    echo "<script>alert('Profile picture uploaded successfully');</script>";
                } else {
                    echo "<script>alert('Error uploading profile picture');</script>";
                }
            }

            // Get profile picture path, with fallback to default image
            $profile_pic_path = 'profile_pictures/' . $username . '.jpg';
            if (!file_exists($profile_pic_path)) {
                $profile_pic_path = 'default_profile.jpg'; // Path to your default profile picture
            }

            // Handle message sending functionality
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
                $message = trim($_POST['message']);
                if (!empty($message)) {
                    $new_message = $username . '; ' . $message . PHP_EOL;
                    file_put_contents('messages.txt', $new_message, FILE_APPEND);

                }
            }

            // Fetch messages for display in the chat box
            $messages = [];
            if (file_exists('messages.txt')) {
                $messages = file('messages.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            }
        } else {
            // Redirect to login if the password is incorrect
            header('Location: login.php');
            exit;
        }
    } else {
        // Redirect to login if the user file does not exist
        header('Location: login.php');
        exit;
    }
} else {
    // Redirect to login if cookies are not set
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Page</title>
    <style>
        /* General body styling */
        body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            overflow: hidden;
        }

        /* Sidebar styling */
        .sidebar {
            width: 35%;
            background-color: #ffc0cb;
            padding: 20px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            bottom: 0;
        }

        /* Profile container styling */
        #profile-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        #profile-picture {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        #profile-container p {
            margin: 0;
            font-weight: bold;
            color: #333;
        }

        /* News board styling */
        #news-board {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        #news-video {
            width: 100%;
            height: auto;
            border-radius: 10px;
            display: block;
        }

        /* Controls section styling */
        #controls {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .control-button {
            background-color: #ff69b4;
            border: none;
            padding: 10px 20px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .control-button:hover {
            background-color: #ff1493;
        }

        .chat-container {
            margin-left: 35%;
            padding: 10px;
            box-sizing: border-box;
            height: calc(100vh - 40px); /* Adjust height to take full viewport height minus margins */
        }

        .chat-header {
            background-color: #ff69b4;
            color: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            
           
        }

        .chat-box {
        
            height: 80%;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            background-color: white;
            
            border-radius: 5px;
        }

        .chat-message {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .chat-message img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .chat-message div {
            max-width: 80%;
        }

        .chat-message p {
            display: flex;
            margin: 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            align-items: flex-start
        }
        
        .chat-message .username {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Message entry styling */
        .message-entry {
            
            display: flex;
            align-items: center;
        justify-content: center;
            width: 100%;
        }

        #message-input {
            position: absolute
            flex: 1;
          padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            margin-left: 10px;
             box-sizing: border-box;
            buttom: 2px;
            width: 100vh;
            
        }

        #send-button {
            background-color: #ff69b4;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        #send-button:hover {
            background-color: #ff1493;
        }
      
.chat-btn {
    display:none;
} 
        #plusIcon {
          
            width: 50px; /* Adjust size as needed */
            height: 50px; /* Adjust size as needed */
            background-color: #ff69b4; /* Pink background color */
            color: white; /* White color for the plus sign */
            font-size: 24px; /* Size of the plus sign */
            font-weight: bold;
            text-align: center; /* Center the plus sign horizontally */
            line-height: 50px; /* Vertically center the plus sign */
            border-radius: 50%; /* Make the div a circle */
            cursor: pointer;
            transition: background-color 0.3s ease; /* Smooth hover effect */
        }

        #plusIcon:hover {
            background-color: #ff1493; /* Darker pink on hover */
        }
        .arrow {
            display :none;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: 100vh; /* Make sidebar fill the viewport height */
                position: fixed; /* Keep sidebar fixed */
                top: 0;
                left: 0;
                background-color: #ffc0cb; /* Pink background */
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 20px; /* Add padding for better spacing */
                box-sizing: border-box;
                overflow: hidden; /* Prevent overflow */
                
                  transition: transform 0.3s ease;
            }
            .sidebar.open {
              transform: translateX(0); /* show the sidebar when open */
            }
           
           .chat-box {
               height: 73%;
           }
            #message-input {
              
                width: 26vh;
                margin: 5;
            }
            .message-entry {
              margin: 0;
               width: 100%;
                
            }
            #profile-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                margin-top: 20px; /* Add space from top if needed */
            }

            #profile-picture {
                width: 125px; /* Adjust size as needed */
                height: 125px; /* Adjust size as needed */
                border-radius: 50%;
                margin-bottom: 10px; /* Space between picture and username */
                position: absolute;
                top: 10px;
                right: 50%
                left: 50%;
            }

            #profile-container p {
                font-size: 18px; /* Adjust size as needed */
                color: #333;
                margin: 0; /* Remove default margins */
                position: absolute;
            top: 150px;
            }

            #news-video {
                height: 180px; /* Adjust height as needed */
                width: 100%; /* Ensure video takes full width */
                object-fit: cover;



            }
            #news-board {
                position: absolute;
                top:230px;
                 width: 89%;
            }

            #mute-unmute {
                display: block; /* Ensure button is displayed */
                position: absolute;
               top: 440px;
                background-color: #ff69b4; /* Pink background */
                border: none;
                color: white;
                padding: 10px;
                cursor: pointer;
                width: 89%;
                font-weight: bold;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }


            #logoutButton {
                margin: 5px;
                width: 89%;
                position: absolute;
                top: 200px;
            }

            .control-button {
                width: 100%;
            }
          
            .chat-container {
                
              position: fixed;
              bottom: 0;
                 right: -100%; /* Start off-screen to the right */
              width: 100%;
              max-width: 400px;
              height: 100%;
              background-color: #fff;
              border: 1px solid #ddd;
              box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
              overflow: hidden;
              transition: right 0.3s ease; /* Smooth sliding effect */
            }

            .chat-container.open {
              right: 0; /* Slide into view from the right */
                width: 100%; /* Take full screen width */
                height: 100vh
            }
           

            .sidebar.hidden {
                display: none;
              transform: translateX(-100%); /* Slide out of view */
            }
.chat-header {
    display: flex;
   align-items: center;
    margin-top: 55px;
   text-align: center;
    height: 50px;
    
}
            

                #chatbtn {
                    display: block;
                    position: absolute;
                    top: 550px;
                    background-color: #ff69b4;
                    border: none;
                    color: white;
                    padding: 10px;
                    cursor: pointer;
                    font-weight: bold;
                    border-radius: 5px;
                    transition: background-color 0.3s ease;
                    z-index: 3; /* Ensure button is above other content */
                }

            .arrow{
                margin: 4px;
                margin-left: 0;
                display: block;
                width: 50px; /* Adjust size as needed */
                height: 50px; /* Adjust size as needed */
                background-color: #ff1493; /* Pink background color */
                color: white; /* White color for the plus sign */
                font-size: 24px; /* Size of the plus sign */
                font-weight: bold;
                text-align: center; /* Center the plus sign horizontally */
            
                border-radius: 50%; /* Make the div a circle */
                cursor: pointer;
                transition: background-color 0.3s ease; /* Smooth hover effect */
            }

         

            .arrow::before {
                content: '⬅'; /* Left arrow */
                color: white;
                font-size: 30px;
            }

            .arrow:hover {
                background-color: #ff69b4; /* Hover color */
            }

        } /* end of media */

        #logoutButton {
            margin-buttom: 10px;
            padding: 8px 20px;
            background-color: #ff69b4;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-weight: bold;

        }
        #logoutButton:hover {
            background-color: #ff1493;
        }
        .time {
            font-size: 12px; /* Smaller font size */
            color: gray;
        }
        .text {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div id="profile-container">
            <img id="profile-picture" src="<?php echo htmlspecialchars($profile_pic_path); ?>" alt="Profile Picture">
            <p><?php echo htmlspecialchars($username); ?></p>

        </div> <button id="logoutButton">Log Out</button> 
        <div id="news-board">
            <video id="news-video" src="Intro.mp4" autoplay loop playsinline></video>

        </div>
        <div id="controls">
            <button class="control-button" id="mute-unmute">Mute</button>
            <button class="chat-btn" id="chatbtn">Chat</button>
        </div>
    </div> 

    <form id="upload-form" method="post" enctype="multipart/form-data" style="display: none;">
        <input type="file" name="profile_pic" id="file-input" accept="image/*">
    </form>
   
    <div class="chat-container" id="chatcon">
        <div class="chat-header">
            <button class="arrow" id="arrow" onclick="toggleSidebar()"></button>
                
            <h2>Talk Room</h2>
        </div>
        <div class="chat-box"id="chat-box">
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg): ?>
                    <?php 
                    list($msg_username, $msg_content) = explode('; ', $msg, 2);
                    $msg_profile_pic = 'profile_pictures/' . $msg_username . '.jpg';
                    if (!file_exists($msg_profile_pic)) {
                        $msg_profile_pic = 'default_profile.jpg';
                    }
                    ?>
                    <div class="chat-message">
                        <img src="<?php echo htmlspecialchars($msg_profile_pic); ?>" alt="Profile Picture">
                        <div>
                            <p class="username"><?php echo htmlspecialchars($msg_username); ?></p>
                            <p class="text"><?php echo htmlspecialchars($msg_content); ?></p>
                            <p class="time"></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="message-entry">  <div id="plusIcon">
                +
            </div> 
            <form method="post">
                <input type="text" id="message-input" name="message" autocomplete="off" placeholder="Type your message here...">
                <button id="send-button" type="submit">Send</button>
            </form>
        </div>
    </div>
<script>
    function formatAMPM(date) {
        let hours = date.getHours();
        let minutes = date.getMinutes();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // The hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        const strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }

    function appendTimeToMessages() {
        const messages = document.querySelectorAll('.chat-message');

        messages.forEach((message) => {
            const currentTime = new Date();
            const localTime = formatAMPM(currentTime);
            message.querySelector('.time').innerText = localTime;
        });
    }
    function scrollToBottom() {
        var chatBox = document.querySelector('#chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Initial scroll to bottom
    scrollToBottom();
    // Get references to the video and button elements
    const video = document.getElementById('news-video');
    const muteBtn = document.getElementById('mute-unmute');

    // Automatically start the video muted
    video.muted = true;
    video.play();

    // Play sound when the user interacts with the page
    document.body.addEventListener('click', () => {
        if (video.muted) {
            video.muted = false;
            video.play();
        }
    }, { once: true });  // Trigger only once on the first click

    // Toggle mute/unmute when clicking the mute button
    muteBtn.addEventListener('click', () => {
        if (video.muted) {
            video.muted = false;
            muteBtn.textContent = 'Mute';
        } else {
            video.muted = true;
            muteBtn.textContent = 'Unmute';
        }
    });
    const sidebar = document.querySelector('.sidebar');
    const chatContainer = document.querySelector('.chat-container');

    // add event listener to the chat button
    document.getElementById('chatbtn').addEventListener('click', () => {
      sidebar.classList.toggle('open');
      chatContainer.classList.toggle('open');
    });

    // add event listener to the back button on mobile devices
    document.addEventListener('backbutton', () => {
      if (sidebar.classList.contains('open')) {
        sidebar.classList.remove('open');
        chatContainer.classList.remove('open');
      }
    });


    // Profile picture upload handling
    document.getElementById('profile-picture').addEventListener('click', function () {
        document.getElementById('file-input').click();
    });

    document.getElementById('file-input').addEventListener('change', function () {
        document.getElementById('upload-form').submit();
    });
   

    // Handle message sending
    document.querySelector('.message-entry form').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        const messageInput = document.getElementById('message-input');
        const message = messageInput.value.trim();

        if (message) {
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({ message: message })
            }).then(response => response.text())
              .then(() => {
                  messageInput.value = ''; // Clear the input after sending
                  fetchMessages(); // Fetch messages to update the chat box
              });
        }
    });

    // Function to fetch and display messages
    function fetchMessages() {
        fetch('fetch_messages.php')
            .then(response => response.text())
            .then(html => {
                const chatBox = document.querySelector('.chat-box');
                const isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 1;

                chatBox.innerHTML = html;

                // Scroll to the bottom if previously scrolled to bottom
                if (isScrolledToBottom) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
    }

    // Refresh messages every 2 seconds
    setInterval(fetchMessages, 2000);

    // Initial message fetch
    fetchMessages();

    document.getElementById("logoutButton").addEventListener("click", function() {
        let confirmation = confirm("Are you sure you want to log out?");
        if (confirmation) {
            // Deleting cookies by setting expiry date to the past
            document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "password=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            // Redirect to login page or index
            window.location.href = "index.html";
        }
    });

    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const chatContainer = document.querySelector('.chat-container');
        const chatBtn = document.getElementById('chatbtn');
        const arrow = document.querySelector('.arrow');

        sidebar.classList.toggle('open');
        chatContainer.classList.toggle('open');

        if (sidebar.classList.contains('hidden')) {
            chatBtn.style.display = 'block';
            arrow.style.display = 'block';
        } else {
            chatBtn.style.display = 'block';
            arrow.style.display = 'block';
        }
    }
                </script>

</body>
</html>
