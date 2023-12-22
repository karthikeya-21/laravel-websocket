<x-app-layout>
    <style>

        /* Style the message container */
#msgs {
    max-width: 800px; /* Adjust the max width as needed */
    margin: 0 auto;
    padding: 10px;
    max-height: 400px; /* Adjust the maximum height as needed */
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    /* border: 1px solid #ccc; */
}
#msgs::-webkit-scrollbar {
  width: 0.1em; /* Width of the scrollbar */
}

#msgs::-webkit-scrollbar-track {
  background-color: transparent; /* Background color of the track */
}

#msgs::-webkit-scrollbar-thumb {
  background-color: transparent; /* Color of the thumb (draggable part) */
}
body::-webkit-scrollbar {
  width: 0.0001em; /* Width of the scrollbar */
}

body::-webkit-scrollbar-track {
  background-color: transparent; /* Background color of the track */
}

body::-webkit-scrollbar-thumb {
  background-color: transparent; /* Color of the thumb (draggable part) */
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

li{
    border:none !important;
}
.message-left,
.message-right {
  list-style: none;
  padding: 8px 12px;
  margin: 12px;
  max-width: 250px;
  font-size: 15px;
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

#chat_area{
    background-image:url(storage/wtsapp_bg.jpeg);
    /* height: 400px; */
}
.empty-chat{
    display: flex;
    justify-content: center;
    align-items: center;
    height: 400px;

}

#send-container {
            /* display: flex;
            justify-content: space-between; */
            align-items: center;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px
        }
        .container-row {
            display: flex;
            justify-content: space-between;
        }

        /* .col-lg-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }

        .col-lg-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
        } */
                /* User Tile Styles */
                .user-tile {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .user-avatar {
            width: 50px; /* Adjust the size as needed */
            height: 50px; /* Adjust the size as needed */
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-weight: bold;
        }

        .user-status {
            font-size: 14px;
            color: #666;
        }
        .dark-mode {
            background-color: #333; /* Dark card background color */
            color: #ffffff; /* Text color in dark mode */
        }
        .smallbox{
            max-height:200px;
            overflow-y:auto;
        }
        .smallbox::-webkit-scrollbar {
  width: 0.1em; /* Width of the scrollbar */
}

.smallbox::-webkit-scrollbar-track {
  background-color: transparent; /* Background color of the track */
}

.smallbox::-webkit-scrollbar-thumb {
  background-color: transparent; /* Color of the thumb (draggable part) */
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
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6  text-gray-900 dark:text-gray-100">
                <div class="container">
                        <div class="container-row">
                            <!-- Users List Column (col-lg-4) -->
                            <div class="col-lg-3">
                                <div class="users-list" id="users-list">
                                    <!-- Your user list content goes here -->
                                </div>
                            </div>

                            <!-- Messages Column (col-lg-8) -->
                            <div class="col-lg-6">
                                <div class="card dark-mode">
                                    <div class="card-header dark-mode">
                                        <div class="row">
                                            <div class="col col-md-6" id="chat_header"><b>Chat Area</b></div>
                                            <div class="col col-md-6" id="close_chat_area"></div>
                                        </div>
                                    </div>
                                    <div class="card-body" id="chat_area">

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                            <div class="container mt-5">
        <div class="card dark-mode">
            <div class="card-header">
                <h5 class="card-title">User List</h5>
            </div>
            <div class="card-body smallbox" >
                <ul class="list-group dark-mode" id="unconnected_users">
                </ul>
            </div>
        </div>
        <div class="card dark-mode mt-4">
            <div class="card-header">
                <h5 class="card-title">Requests</h5>
            </div>
            <div class="card-body smallbox">
                <ul class="list-group dark-mode" id="requests">

                </ul>
            </div>
        </div>
    </div>
                            </div>
                        </div>
                    </div>

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
        console.log(conn);
        empty_chat();
        const dateTime = new Date();
        var from_user_id={{Auth::user()->id}};
        var to_user_id='';
        function scrollContainerToBottom() {
                const msgsContainer = document.getElementById('msgs');
                msgsContainer.scrollTop = msgsContainer.scrollHeight;
            }
        // Handle the connection being opened
        conn.onopen = function (e) {
            var data={
                type:"re-load",
            }
            conn.send(JSON.stringify(data));
            load_all_users(from_user_id);
            load_unread_notification(from_user_id);
            load_friends(from_user_id);
        }

        // Listen for incoming messages from the server
        conn.addEventListener('message', event => {
    const message = event.data;
    var data = JSON.parse(message);
    if(data.type=='image_link'){
        console.log(data.data);
        document.getElementById('message_area').innerHTML = `<img style="border: none;" src="{{ asset('images/`+data.data+`') }}" class="img-thumbnail img-fluid" />`;
    }
    if(data.type=='reload'){
        load_unread_notification(from_user_id);
        load_friends(from_user_id);
    }

    // Display the received message in the 'msgs' div
    const msgsDiv = document.getElementById('msgs');
    if (data.type == 'msg') {
            add_message_to_chat(data);
    }
    if (data.type == 'load_connected_users') {
        const users_list = document.getElementById('users-list');
        users_list.innerHTML = '';
        var Userdata = data.data;

        Userdata.forEach(user => {
            const userTileButton = document.createElement("button");
            userTileButton.classList.add("user-tile");

            // Create user avatar (profile picture)
            const userAvatar = document.createElement("img");
            userAvatar.classList.add("user-avatar");
            userAvatar.src = `/storage/${user.user_image}`; // Set the source of the profile picture

            // Create user info container
            const userInfo = document.createElement("div");
            userInfo.classList.add("user-info");

            // Create user name element
            const userName = document.createElement("div");
            userName.classList.add("user-name");
            userName.textContent = user.name; // Set the user's name

            // Create user status element
            const userStatus = document.createElement("div");
            userStatus.classList.add("user-status");
            if(user.user_status=='Online'){
            userStatus.textContent = user.user_status;
            }else{
                userStatus.textContent = user.last_seen;
            } // Set the user's status

            // Append elements to user tile container
            userInfo.appendChild(userName);
            userInfo.appendChild(userStatus);
            userTileButton.appendChild(userAvatar);
            userTileButton.appendChild(userInfo);
            userTileButton.addEventListener("click", () => {
                to_user_id = user.id;
                make_chat_area(user.id,user.name);
                load_old_messages(from_user_id,user.id);
                // You can now use "to_user" to send messages to this user
                console.log(`Message to_user: ${to_user_id}`);
            });

            // Append user tile to users list
            users_list.appendChild(userTileButton);
        });
    }
    if (data.type == 'load_all_users') {
        const users_list = document.getElementById('unconnected_users');
        var userData = data.data;

        function createUserListItem(user) {
            const listItem = document.createElement("li");
            listItem.className = "list-group-item d-flex justify-content-between align-items-center dark-mode";
            listItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <img src="/storage/${user.user_image}" alt="${user.name}" class="user-avatar">
                    <span>${user.name}</span>
                </div>
                <button class="btn btn-primary" onclick="send_request(this, ${from_user_id}, ${user.id})"><i class="fas fa-paper-plane"></i></button>
            `;
            return listItem;
        }

        users_list.innerHTML = '';
        if (userData.length === 0){
            let noUsersText=document.createTextNode("No New Users Found!");
            users_list.appendChild(noUsersText);
        };
        userData.forEach(user => {
            const listItem = createUserListItem(user);
            users_list.appendChild(listItem);
        });
    }

    if (data.type == 'response_load_notification') {
        console.log(data);
        load_all_users(from_user_id);
        const users_list = document.getElementById('requests');
        users_list.innerHTML = '';
        var userData = data.data;
        if (userData.length === 0){
            let noUsersText=document.createTextNode("No Requests Found!");
            users_list.appendChild(noUsersText);
        }
        else{
        function createUserListItem(user) {
            console.log(user);
            const listItem = document.createElement("li");
            listItem.className = "list-group-item d-flex justify-content-between align-items-center dark-mode";

            const nameDiv = document.createElement("div");
            nameDiv.className = "d-flex align-items-center";
            const userName = document.createElement("span");
            userName.textContent = user.name;

            nameDiv.appendChild(userName);

            // Determine which buttons to add based on notification_type
            let tail='';
            if (user.notification_type === 'Receive Request') {
                tail += '<button class="btn btn-primary" onclick="process_request(' + user.id + ',' + user.from_user_id + ',' + user.to_user_id + ', `Approve`)"><i class="fa fa-check"></i></button>';
                tail += '<button class="btn btn-danger" onclick="process_request(' + user.id + ',' + user.from_user_id + ',' + user.to_user_id + ', `Reject`)"><i class="fa fa-times"></i></button>';
            } else {
                tail = `<button class="btn btn-info">Request sent</button>`;
            }

            listItem.appendChild(nameDiv);
            listItem.innerHTML += tail; // Use innerHTML to add the tail content

            return listItem;
        }

        userData.forEach(user => {
            const listItem = createUserListItem(user);
            users_list.appendChild(listItem);
        });
    }
    }
    if (data.type == 'updateUI') {
        load_all_users(from_user_id);
        load_unread_notification(from_user_id);
    }
    if (data.type == 'response_process_chat_request') {
        load_unread_notification(data.user_id);
        load_friends(data.user_id);
    }
    if(data.type=='chat_history'){
        var Chats=data.data;
        const msgsDiv = document.getElementById('msgs');
        Chats.forEach(msg=>{
            add_message_to_chat(msg);
        })
    }
});




        load_all_users=function(){
            var data={
                'from_user_id':`${from_user_id}`,
                type:'load_all_users',
                search_query:'',
            }
            conn.send(JSON.stringify(data));
        }
        function send_request(element, from_user_id, to_user_id)
        {
            var data = {
                from_user_id : from_user_id,
                to_user_id : to_user_id,
                type : 'request_chat_user'
            };

            element.disabled = true;

            conn.send(JSON.stringify(data));
        }
        function load_unread_notification(user_id)
        {
            var data = {
                user_id : user_id,
                type : 'load_notifications'
            };
            conn.send(JSON.stringify(data));
        }
        function load_friends(user_id){
            var data = {
		    from_user_id : from_user_id,
            type : 'request_connected_chat_user'
        };

        conn.send(JSON.stringify(data));
        }

        function process_request(chat_id,from_user,to_user,action){
            var data = {
                chat_request_id : chat_id,
                from_user_id : from_user,
                to_user_id : to_user,
                action : action,
                type : 'request_process_chat_request'
            };

            conn.send(JSON.stringify(data));
        }
        function make_chat_area(user_id, to_user_name)
        {
            var html = `
            <ul id="msgs"></ul>
            <div id="chat_history"></div>
            <div class="input-group mb-3">
                <div id="send-container" class="d-flex align-items-center">
                <!-- Add the upload image icon button -->
                <label for="upload-image" class="btn btn-warning" style="margin: 10px; color: black;">
                    <i class="fas fa-image"></i>
                </label>
                <!-- Hidden input for uploading image (you can style it as needed) -->
                <input type="file" id="upload-image" style="display: none;" accept="image/*" onchange="upload_image()">
                <!-- Add the emoji picker button using inline style for demonstration -->
                <button class="btn btn-info" style="margin: 10px;" id="emoji-button"">
                    <i class="far fa-smile"></i>
                </button>

                <!-- Emoji Picker Container (Initially Hidden) -->
                <div id="emoji-picker" style="display: block;"></div>
                <x-my-div id="message_area" style="width:200px;">
                </x-my-div>

                    <button class="btn btn-primary" style="margin-left: 10px;" id="send-btn" onclick="send_message()">Send Message</button>
                </div>
            </div>
            `;

            document.getElementById('chat_area').innerHTML= html;

            document.getElementById('chat_header').innerHTML = 'Chat with <b>'+to_user_name+'</b>';

            document.getElementById('close_chat_area').innerHTML = '<button type="button" id="close_chat"  class="btn btn-danger btn-sm float-end " onclick="close_chat();"><i class="fas fa-times"></i></button>';

            to_user_id = user_id;
    }

        function close_chat()
        {
            document.getElementById('chat_header').innerHTML = 'Chat Area';

            document.getElementById('close_chat_area').innerHTML = '';

            document.getElementById('chat_area').innerHTML = '';

            to_user_id = '';
            empty_chat();
        }
        function send_message(){
            let message = document.getElementById('message_area');
            var mess = document.getElementById('message_area').innerHTML.trim();
            if(mess.length>0){
            var data={
                type:'msg',
                name:"{{Auth::user()->name}}",
                chat_message:mess,
                from_user_id:from_user_id,
                to_user_id:to_user_id,
                user_image:"{{Auth::user()->user_image}}",
                created_at: dateTime.toISOString(),
            }
            // Send the message to the server
            conn.send(JSON.stringify(data));
            message.innerHTML='';
        }
    }

    function empty_chat(){
        var html=`<div class="empty-chat">
            <img src="storage/chat_bg.png" alt="" width=300 height=350>
        </div>`;
        document.getElementById("chat_area").innerHTML=html;
    }

        function add_message_to_chat(data){
            const msgsDiv = document.getElementById('msgs');
            if(data.from_user_id==from_user_id){
            const element = `
                <li class='message-right'>
                    <p class="message">
                    ${data.chat_message}
                    <span>${data.name} ● ${moment(data.created_at).fromNow()}</span>
                    </p>
                </li>
                `;
            msgsDiv.innerHTML += `<div class='current-user-message flex flex-row-reverse'>&nbsp;&nbsp;&nbsp;<img class="w-16 h-16 rounded-full mt-4" src={{ '/storage/' . auth()->user()->user_image}} /> &nbsp;&nbsp;&nbsp;${element}&nbsp;&nbsp; </div>`;
            }
            else{
                const element = `
        <li class='message-left'>
            <p class="message">
            ${data.chat_message}
            <span>${data.name} ● ${moment(data.created_at).fromNow()}</span>
            </p>
        </li>
        `;
        msgsDiv.innerHTML += `<div class='user-message flex flex-row'><img class="w-16 h-16 rounded-full mt-4" src='storage/${data.user_image}' />&nbsp;&nbsp;&nbsp; ${element} </div>`;
            }
            scrollContainerToBottom();
        }

        function load_old_messages(from_user_id,to_user_id){
            var data={
                type : 'loadOldMsg',
                from_user_id:from_user_id,
                to_user_id:to_user_id,
            }
            conn.send(JSON.stringify(data));
        }

        function sendImage(image){
            var data={
                type:'imagedata',
                from_user_id:from_user_id,
                to_user_id:to_user_id,
                image: image,
            }
            console.log(data);
        }

        function upload_image()
        {
            var file_element = document.getElementById('upload-image').files[0];

            var file_name = file_element.name;

            var file_extension = file_name.split('.').pop().toLowerCase();

            var allowed_extension = ['png', 'jpg','jpeg'];

            if(allowed_extension.indexOf(file_extension) == -1)
            {
                alert("Invalid Image File");

                return false;
            }

            var file_reader = new FileReader();

            var file_raw_data = new ArrayBuffer();

            file_reader.loadend = function()
            {

            }

            file_reader.onload = function(event){

                file_raw_data = event.target.result;

                conn.send(file_raw_data);
            }

            file_reader.readAsArrayBuffer(file_element);
        }

        const picker = new EmojiButton();

        // Add emoji picker to the emoji button
        const emojiButton = document.getElementById('emoji-button');
        emojiButton.addEventListener('click', () => {
        picker.togglePicker(emojiButton);
        });

        // Close emoji picker when clicking outside of it
        document.addEventListener('click', (event) => {
        if (!emojiButton.contains(event.target)) {
            picker.hidePicker();
        }
        });
    </script>
</x-app-layout>
