<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web-Sockets</title>
</head>
<body>
    <input type="text" id="msg" placeholder="Enter Your Message"/>
    <button id="send-btn">Send Message</button>
    <div id="msgs"></div>
    <script>
        // Create a WebSocket connection to the server
        var conn = new WebSocket('ws://10.9.28.248:8090/');

        // Handle the connection being opened
        conn.onopen = function (e) {
            console.log("Connection established!");
        }

        // Listen for incoming messages from the server
        conn.addEventListener('message', event => {
            const message = event.data;
            console.log(`Server says: ${message}`);
            
            // Display the received message in the 'msgs' div
            const msgsDiv = document.getElementById('msgs');
            msgsDiv.innerHTML += `<p>${message}</p>`;
        });

        // Handle sending a message when the button is clicked
        const sendMessageButton = document.getElementById('send-btn');
        sendMessageButton.addEventListener('click', () => {
            let message = document.getElementById('msg');
            
            // Send the message to the server
            conn.send(message.value);
            message.value='';
            console.log('Message sent:', message);
        });
    </script>
</body>
</html>
