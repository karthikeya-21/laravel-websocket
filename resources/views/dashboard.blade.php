<x-app-layout>
    <style>
        /* Style the message container */
#msgs {
    max-width: 800px; /* Adjust the max width as needed */
    margin: 0 auto;
    padding: 10px;
    max-height: 200px; /* Adjust the maximum height as needed */
    overflow-y: auto;
    /* border: 1px solid #ccc; */
}

/* Style user messages (sent by others) */
.user-message {
    /* background-color: #e0e0e0; */
    padding: 5px;
    margin: 5px 0;
    border-radius: 5px;
}

/* Style current user messages */
.current-user-message {
    /* background-color: #4CAF50; */
    color: white;
    padding: 5px;
    margin: 5px 0;
    border-radius: 5px;
    text-align: right;
}

#send-container {
            /* display: flex;
            justify-content: space-between; */
            align-items: center;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px
        }

    </style>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                <div id="msgs"></div>

                <div id="send-container">
                <x-my-input type="text" id="msg" placeholder="Enter Your Message"/>
                <x-primary-button id="send-btn">Send Message</x-primary-button>
                </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Create a WebSocket connection to the server
        var conn = new WebSocket('ws://127.0.0.1:8090/?token={{auth()->user()->token}}');
        function scrollContainerToBottom() {
    const msgsContainer = document.getElementById('msgs');
    msgsContainer.scrollTop = msgsContainer.scrollHeight;
}
        // Handle the connection being opened
        conn.onopen = function (e) {
            var data={
                user:"{{Auth::user()->name}}",
                msg:'has entered the room',
            }
            conn.send(JSON.stringify(data));
            console.log("Connection established!");
        }

        // Listen for incoming messages from the server
        conn.addEventListener('message', event => {
            const message = event.data;
            var data = JSON.parse(message);
            console.log(`Server says: ${message}`);
            console.log(data.user);
            // Display the received message in the 'msgs' div
            const msgsDiv = document.getElementById('msgs');
            msgsDiv.innerHTML += `<p class='user-message'>${data.user} : ${data.msg}</p>`;
            scrollContainerToBottom();
        });

        // Handle sending a message when the button is clicked
        const sendMessageButton = document.getElementById('send-btn');
        sendMessageButton.addEventListener('click', () => {
            let message = document.getElementById('msg');
            
            var data={
                user:"{{Auth::user()->name}}",
                msg:message.value,
            }
            // Send the message to the server
            conn.send(JSON.stringify(data));
            console.log('Message sent:', message.value);
            message.value='';
            const msgsDiv = document.getElementById('msgs');
            msgsDiv.innerHTML += `<p class='current-user-message'>You : ${data.msg}</p>`;
            scrollContainerToBottom();

        });
    </script>
</x-app-layout>
