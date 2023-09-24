<x-app-layout>
    <style>
        /* Style the message container */
#msgs {
    max-width: 800px; /* Adjust the max width as needed */
    margin: 0 auto;
    padding: 10px;
    max-height: 600px; /* Adjust the maximum height as needed */
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
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

.message-left,
.message-right {
  list-style: none;
  padding: 8px 12px;
  margin: 12px;
  max-width: 250px;
  font-size: 18px;
  word-wrap: break-word;
}

.message-left {
  color:black;
  border-radius: 20px 20px 20px 0px;
  align-self: flex-start;
  background-color: white;
  box-shadow: -2px 2px 4px #dcdcdc;
}

.message-right {
  border-radius: 20px 20px 0px 20px;
  align-self: flex-end;
  color:black;
  background-color: white;
  box-shadow: 2px 2px 4px #dcdcdc;
}

.message-left > p > span,
.message-right > p > span {
  display: block;
  font-style: italic;
  font-size: 12px;
  margin-top: 4px;
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
            <!-- {{ __('Dashboard') }} -->
            {{Auth::user()->name}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                <ul id="msgs">
                </ul>

                <div id="send-container">
                <x-my-input type="text" id="msg" placeholder="Enter Your Message"/>
                <x-primary-button id="send-btn">Send Message</x-primary-button>
                </div>
                </div>
            </div>
        </div>
    </div>
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.28.0/moment.min.js"
      integrity="sha512-Q1f3TS3vSt1jQ8AwP2OuenztnLU6LwxgyyYOG1jgMW/cbEMHps/3wjvnl1P3WTrF3chJUWEoxDUEjMxDV8pujg=="
      crossorigin="anonymous"
    ></script>
    <script>
        // Create a WebSocket connection to the server
        var conn = new WebSocket('ws://127.0.0.1:8090/?token={{auth()->user()->token}}');
        const dateTime = new Date();
        function scrollContainerToBottom() {
    const msgsContainer = document.getElementById('msgs');
    msgsContainer.scrollTop = msgsContainer.scrollHeight;
}
        // Handle the connection being opened
        conn.onopen = function (e) {
            var data={
                type:"new",
                user:"{{Auth::user()->name}}",
                msg:' {{Auth::user()->name}} has entered the room',
                img:'{{Auth::user()->user_image}}',
            }
            conn.send(JSON.stringify(data));
            console.log("Connection established!");
        }

        // Listen for incoming messages from the server
        conn.addEventListener('message', event => {
            const message = event.data;
            var data = JSON.parse(message);
            // Display the received message in the 'msgs' div
            const msgsDiv = document.getElementById('msgs');
            if(data.type=='msg'){
                const element = `
                <li class='message-left'>
                    <p class="message">
                    ${data.msg}
                    <span>${data.user} ● ${moment(data.dateTime).fromNow()}</span>
                    </p>
                </li>
                `;
            msgsDiv.innerHTML += `<div class='user-message flex flex-row'><img class="w-16 h-16 rounded-full" src='storage/${data.img}' />&nbsp;&nbsp;&nbsp; ${element} </div>`;
            scrollContainerToBottom();
            }
        });

        // Handle sending a message when the button is clicked
        const sendMessageButton = document.getElementById('send-btn');
        sendMessageButton.addEventListener('click', () => {
            let message = document.getElementById('msg');

            var data={
                type:"msg",
                user:"{{Auth::user()->name}}",
                msg:message.value,
                img:"{{Auth::user()->user_image}}",
                dateTime: dateTime.toISOString(),
            }
            // Send the message to the server
            conn.send(JSON.stringify(data));
            message.value='';
            const msgsDiv = document.getElementById('msgs');
            const element = `
                <li class='message-right'>
                    <p class="message">
                    ${data.msg}
                    <span>${data.user} ● ${moment(data.dateTime).fromNow()}</span>
                    </p>
                </li>
                `;
            msgsDiv.innerHTML += `<div class='current-user-message flex flex-row-reverse'>&nbsp;&nbsp;&nbsp;<img class="w-16 h-16 rounded-full" src={{ '/storage/' . auth()->user()->user_image}} /> &nbsp;&nbsp;&nbsp;${element}&nbsp;&nbsp; </div>`;
            scrollContainerToBottom();

        });
    </script>
</x-app-layout>
