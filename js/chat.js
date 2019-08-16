// Chat client code
// Some of this code is modified with thanks from code provided by Anant Garg
// Which is Copyright (c) 2009 Anant Garg (anantgarg.com | inscripts.com)
// Used under single domain licence for torn.com

// Other parts of this code are based on code provided by Friendfeed
// Which is licences under the Apache 2.0 licence

// Licensed under the Apache License, Version 2.0 (the "License"); you may
// not use this file except in compliance with the License. You may obtain
// a copy of the License at
//
//	   http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
// WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
// License for the specific language governing permissions and limitations
// under the License.

// Finally, much of this code relies on jQuery, which is GPL (and ace).
// Donation has been made to jQuery.


var chat = {};

(function () {
    // if we got a parent with the real chat in there, just override
    // our local copy with that
    if (top.chat && window != top) {
        console.log("Warning: chat.js included twice (in top and in iframe)");
        chat = top.chat;
        return;
    }

    // local shortcut for convenience
    var channels = longpoll.channels;
        
    // Each room is represented by some Javascript state and a DOM
    // element.
    var rooms = {};

    // setup the chat API
    chat.start = startChat;
    chat.displayWelcome = displayWelcome;
    chat.stop = stopChat;
    chat.addRoom = addChatRoom;
    chat.setRoomStatus = setChatRoomStatus;
    chat.rooms = rooms;
    chat.users = {}; // user id -> our local idea of online status
    chat.ownUserId = null; // global variable set by PHP script that includes us
    chat.USER_STATUS_UNKNOWN = -1;
    chat.USER_STATUS_OFFLINE = 0;
    chat.USER_STATUS_ONLINE = 1;
    chat.running = false;
    chat.stopWithButton = function () {
        chat.stop();
        setupReenableChatButton();
    };
    chat.setupRoom = function (userids) {
        var data = { users: JSON.stringify(jQuery.makeArray(userids)) };
        try {
            if (channels[statusServerId].ajax)
                channels[statusServerId].ajax({
                    type: "POST",
                    url: "/torncity/chat/status/setuproom",
                    data: data,
                    timeout: 10 * 1000
                });
        }
        catch (err) {
        }
    };
    chat.scheduleUserStatusPoll = function () {
        if (chat.running) {
            pollStatusServer(statusServerId);
        }
    }; 
    // these callbacks are one-shot, receiver must reconnect upon
    // being called (helps with garbage collection)
    chat.listenForRoomChanges = function (key, cb) {
        callbacks.rooms[key] = { callback: cb };
    };
    chat.listenForUserChanges = function (key, users, cb) {
        callbacks.users[key] = {
            users: users,
            callback: cb
        };
    };
    chat.removeCallback = function (type, key) {
        delete callbacks[type][key];
    };
    
    // and the implementation
    
    var maxMessagesPerRoom = 40;
    var statusServerId = "status";
    var holdOffChatRoomRestructuring = 0; // counting semaphore, > 0 means true
    var restoringStateFromLocalStorage = 0; // counting semaphore, > 0 means true
    var callbacks = {
        rooms: {},
        users: {}
    };

    var statusMap = { 0: 'open', 1: 'minimized', 2: 'closed' };
    var pendingRoomChanges = {};

    function callCallbacks(type, filter) {
        var tmp = callbacks[type];
        jQuery.each(tmp, function (k, o) {
            if (filter && !filter(o))
                return;

            // clear it out before we call callback
            delete callbacks[type][k];
            
            o.callback();
        });
    }

    function setupStatusPoller(poll) {
        // we are gentle with the status server, when it returns a new
        // room, it can take some time before we have it setup, thus
        // with a short timeout, we can bug it a couple of times
        // before the state is up to date; besides status updates are
        // less time critical than new messages
        poll.successWaitTime = 2500;
        
        poll.callbacks.args = function () {
            if (!chat.running)
                return null;
            
            var data = {};

            // rooms
            var roomStatus = {};
            jQuery.each(rooms, function (id, r) {
                if (r.status == "open")
                    roomStatus[id] = 0;
                else if (r.status == "minimized")
                    roomStatus[id] = 1;
                else 
                    roomStatus[id] = 2;
            });
            
            data.rooms = JSON.stringify(roomStatus);

            // online users
            var userStatus = {};
            jQuery.each(callbacks.users, function(j, o) {
                for (var i = 0; i < o.users.length; ++i) {
                    var u = o.users[i], s = chat.users[u];
                    if (s == null)
                        s = chat.USER_STATUS_UNKNOWN;
                    userStatus[u] = s;
                }
            });
            data.users = JSON.stringify(userStatus);

            return {
                data: data,
                url: "/torncity/chat/status/updates",
                type: "POST",
                dataType: "json"
            };
        };

        poll.callbacks.success = gotStatus;
    }
    
    function pollStatusServer(channelId) {
        longpoll.whenChannelUp(channelId, function () {
            if (!channels[channelId])
                return;

            if (!channels[channelId].poll.callbacks.args)
                setupStatusPoller(channels[channelId].poll);
            
            channels[channelId].poll.schedule(300);
        });
    }

    function setupRoomPoller(poll, serverId) {
        poll.successWaitTime = 1500;
        
        poll.callbacks.args = function () {
            if (!chat.running)
                return null;
            
            var rs = getRoomsForServer(serverId);
            // we only want active rooms
            rs = jQuery.grep(rs, function (r) {
                // special-case global rooms
                if ((r.id == "Global" || r.id == "Trade" || r.id == "New_Players")
                    && r.status == "minimized")
                    return false;
                return r.status != "closed";
            });
            if (rs.length == 0)
                return null;

            var cursors = {};
            jQuery.each(rs, function (i, r) {
                cursors[r.id] = r.cursor;
            });

            var data = {
                cursors: JSON.stringify(cursors)
            };

            return {
                data: data,
                url: "/torncity/chat/message/" + serverId + "/updates",
                type: "POST",
                dataType: "json"
            };
        };

        poll.callbacks.success = gotMessages;

        poll.callbacks.failure = function(failed) {
            var text = "", disabled = "";
            if (failed) {
                text = "Lost server connection";
                disabled = "disabled";
            }
            
            jQuery.each(getRoomsForServer(serverId), function (i, r) {
                r.chatbox.find('.chatboxnotice').text(text);
				//alert(r.chatbox.find('.chatboxtextarea').is(":disabled"));
                //r.chatbox.find('.chatboxtextarea').attr('disabled', disabled);
				r.chatbox.find('.chatboxtextarea').prop("disabled", false);
            });
        };
    }
    
    function pollRoomServer(serverId) {
        longpoll.whenChannelUp(serverId, function () {
            if (!channels[serverId])
                return;

            if (!channels[serverId].poll.callbacks.args)
                setupRoomPoller(channels[serverId].poll, serverId);
            
            channels[serverId].poll.schedule(300);
        });
    }
    
    // a couple of helpers for querying the servers/rooms dicts

    function getRoomsWithStatus(status) {
        var res = [];
        for (var id in rooms) {
            if (rooms[id].status == status)
                res.push(rooms[id]);
        }
        return res;
    }

    function getRoomsForServer(serverId) {
        var res = [];
        for (var id in rooms) {
            if (rooms[id].server == serverId)
                res.push(rooms[id]);
        }
        return res;
    }

    function getUserIdsInRoom(roomId) {
        var res = [];
        for (var uid in rooms[roomId].participants)
            res.push(uid);
        return res;
    }

    function getUserNamesInRoom(roomId, userIds) {
        var res = [];
        for (var uid in rooms[roomId].participants)
            res.push(rooms[roomId].participants[uid]);
        return res;
    }
    
    // chatbox DOM manipulation

    // take list of names, return "name1, name2 and 5 more"
    function abbreviatedList(users, maxNames) {
        var tmp = users.slice(0, maxNames);

        var missing = users.length - tmp.length;
        if (missing > 0)
            tmp.push(missing + " more");

        var res = "";
        for (var i = 0; i < tmp.length; ++i) {
            if (i > 0) {
                if (i == tmp.length - 1)
                    res += " and ";
                else
                    res += ", ";
            }
            
            res += tmp[i];
        }
        return res;
    }
    
    function restructureChatBoxes() {
        console.log ("Restructuring chat boxes");
        
        // counters for right margins
        var open = getRoomsWithStatus("open");
        var minimized = getRoomsWithStatus("minimized");
        
        var openCount = open.length - 1;
        var minCount = minimized.length - 1;

        var adhocRooms = [], otherRooms = [];
        jQuery.each(rooms, function (i, room) {
            if (room.participants)
                adhocRooms.push(room);
            else
                otherRooms.push(room);
        });

        function sortByName(a, b) {
            if (a.name < b.name)
                return -1;
            else if (a.name > b.name)
                return 1;
            return 0;
        }
        
        adhocRooms.sort(sortByName);
        otherRooms.sort(sortByName);

        jQuery.each(otherRooms.concat(adhocRooms), function(i, room) {
            var offset, bottom, showTime = 0; 

            if (room.status == "open") {
                offset = 7;
                if (openCount > 0) {
                    offset += openCount*(225+7);
                    openCount--;
                }

                bottom = 0;
                if (minimized.length > 0)
                    bottom = 38;

                room.chatbox
                    .css('right', offset)
                    .css('bottom', bottom)
                    .css('width','225px');

                room.chatbox.find('.chatboxcontent')
                    .css('display','block')
                    .css('width','209px');

                room.chatbox.find('.chatboxinput').css('display', 'block');
                room.chatbox.find('.chatboxhead').css('width', '209px');
                room.chatbox.find('.chatboxoptions').show();
                room.chatbox.find('.chatboxtitle').css('width', '169px');
                
                if (jQuery.browser.msie) {
                    // compensate for different box model in IE quirks mode
                    room.chatbox.find('.chatboxhead').css('width', '225px');
                    room.chatbox.find('.chatboxcontent').css('width','225px');
                }
                if (room.chatbox.is(':hidden'))
                    room.chatbox.fadeIn(showTime);
                
                // scroll to bottom (after showing to ensure we got a scroll height)
                if (room.scrolledToBottom)
                    room.chatbox.find(".chatboxcontent").scrollTop(room.chatbox.find(".chatboxcontent").get(0).scrollHeight);
            }
            else if (room.status == "minimized") {
                offset = 7;
                if (minCount > 0) {
                    offset += minCount*(116+7);
                    minCount--;
                }
                
                room.chatbox
                    .css('width','116px')
                    .css('right', offset)
                    .css('bottom', '0px');
                
                room.chatbox.find('.chatboxcontent').hide();
                room.chatbox.find('.chatboxinput').hide();
                
                room.chatbox.find('.chatboxhead').css('width', '100px');
                room.chatbox.find('.chatboxoptions').hide();
                room.chatbox.find('.chatboxtitle').css('width', '100px');
                
                if (jQuery.browser.msie) {
                    // compensate for different box model in IE quirks mode
                    room.chatbox.find('.chatboxhead').css('width', '116px');
                }

                if (room.chatbox.is(':hidden'))
                    room.chatbox.fadeIn(showTime);
            }
            else { // closed
                room.chatbox.fadeOut(200, function () { jQuery(this).hide(); });
            }
        });
    }

    function createChatBox(roomId) {
        console.log("Creating chat box for", roomId);
        
        // create DOM elements
        var chatbox = jQuery("<div />" ).attr("id","chatbox_"+roomId)
            .addClass("chatbox")
            .css('display', 'none') // start off hidden, restructuring will show it
            .html('<div id="bh_'+roomId+'" class="chatboxhead"><div class="chatboxtitle" title="'+rooms[roomId].name+'">'+rooms[roomId].name+'</div><div class="chatboxoptions"><a class="minimize" href=""><span></span></a><a class="close" href="">X</a></div><div clear="all"></div></div><div class="chatboxcontent"></div><div class="chatboxinput"><div class="chatboxnotice"></div><textarea class="chatboxtextarea"></textarea></div>')
            .appendTo(jQuery("body"));

        rooms[roomId].chatbox = chatbox;
        
        // setup event handling

        // clicking anywhere within takes us to textarea
        chatbox.click(function() {
            if (rooms[roomId].status == "open") {
                chatbox.find(".chatboxtextarea").focus();
            }
        });

        // minimize/maximize
        chatbox.find(".chatboxhead").click(function() {
            var room = rooms[roomId];
            if (!room)
                return;

            var newStatus = room.status == "minimized" ? "open" : "minimized";
            if (newStatus == "open") {    
                //room.chatbox.find(".chatboxhead").css("background-color","#999999");
                //jQuery(".chatboxtextarea").removeClass("chatboxtextareaselected");
                room.chatbox.find(".chatboxtextarea").focus();
            }

            setChatRoomStatus(roomId, newStatus);
        });

        if (!rooms[roomId].participants)
            chatbox.find(".chatboxhead .close").hide();

        // minimize button
        chatbox.find(".chatboxhead .minimize").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            setChatRoomStatus(roomId, "minimized");
        });
        
        // close button
        chatbox.find(".chatboxhead .close").click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            setChatRoomStatus(roomId, "closed");
        });
        
        // textarea events
        chatbox.find(".chatboxtextarea").blur(function(){
            if (!rooms[roomId])
                return;
            rooms[roomId].focused = false;
            chatbox.find(".chatboxtextarea").removeClass('chatboxtextareaselected');
            //chatbox.find(".chatboxhead").css('background-color', '#999999');
        }).focus(function() {
            if (!rooms[roomId])
                return;
            rooms[roomId].focused = true;
            chatbox.find('.chatboxhead').removeClass('chatboxblink');
            chatbox.find(".chatboxtextarea").addClass('chatboxtextareaselected');
            //chatbox.find(".chatboxhead").css('background-color', '#999999');
        }).keypress(function() {
            if (!rooms[roomId])
                return;
            
            var t = "";
            if (rooms[roomId].initializedParticipantStatus && rooms[roomId].participants) {
                var foundOnline = false;
                for (var uid in rooms[roomId].participantStatus)
                    if (rooms[roomId].participantStatus[uid] == chat.USER_STATUS_ONLINE) {
                        foundOnline = true;
                        break;
                    }

                if (!foundOnline)
                    t = "Warning: nobody else online in room";
            }
            chatbox.find('.chatboxnotice').text(t);
            
            chatbox.find(".chatboxhead").css('background-color', '#2d2d2d');
        }).keydown(function (event) {
            if (!rooms[roomId])
                return;
            
            if (event.which == 13 && event.shiftKey == 0) {
                event.preventDefault();

                var textarea = jQuery(this);
                
                var message = textarea.val();
                message = message.replace(/^\s+|\s+$/g,"");

                textarea
                    .val("")
                    .css('height','44px')
                    .css('overflow','hidden');

                if (message != '')
                    newMessage(message, roomId);
            }
        }).keyup(function (event) {
            if (!rooms[roomId])
                return;
            
            possiblyEnlargeChatBoxTextArea(jQuery(this));
        });

        chatbox.find(".chatboxcontent").scroll(function () {
            if (!rooms[roomId])
                return;
            
            rooms[roomId].scrolledToBottom
                = this.clientHeight + jQuery(this).scrollTop() >= this.scrollHeight - 5;
        });
        
        jQuery(".chatboxtextarea").removeClass('chatboxtextareaselected');
    }

    function possiblyEnlargeChatBoxTextArea(textarea) {
        var maxHeight = 94;

        var h = textarea.get(0).clientHeight,
            scrollHeight = textarea.get(0).scrollHeight;

        if (scrollHeight && scrollHeight > h && h < maxHeight) {
            h = scrollHeight;
            if (h > maxHeight) {
                textarea.css('overflow','auto');
                h = maxHeight;
            }

            // need some extra to account for border and padding
            h += 10;
            
            textarea.height(h);
        }
    }

    function addHtmlToChatBox(roomId, html) {
        var c = rooms[roomId].chatbox.find(".chatboxcontent");

        var node = jQuery(html).appendTo(c);

        // make sure we haven't got too many messages
        c.children().slice(0, -maxMessagesPerRoom).remove();

        if (rooms[roomId].scrolledToBottom)
            c.scrollTop(c.get(0).scrollHeight);
        
        return node;
    }

    // attract attention to box
    function newsInChatBox(roomId) {
        if (roomId != 'Trade' && roomId != 'Global' && roomId != 'New_Players'
            && rooms[roomId].status != "closed") {
            rooms[roomId].chatbox.find(".chatboxhead").css('background-color', '#0033CC'); //New Message?
        }
    }

    function showMessageInChatBox(roomId, message, alreadyUpdated) {
        var room = rooms[roomId];
        var chatboxcontent = room.chatbox.find('.chatboxcontent');
        
        var existing = chatboxcontent.find("#m" + message.id);
        if (existing.length > 0)
            return;

        chatboxcontent.find('.timestamp').remove();

        var html = message.html;
        if (message.date)
            html += "<div class=\"timestamp\" style=\"color:#666666; display:block; margin:0\"><i>Last message:</i> " + message.date + " - TCT</div>";
        
        var node = addHtmlToChatBox(roomId, html);

        // add timestamp of the last message
        if (message.date)
            node.eq(0).attr('title', message.date);

        if (message.fromid != chat.ownUserId && !alreadyUpdated)
            newsInChatBox(roomId);
    }

    
    // server communication

    function updateServerSideRoomStatus(room, loadedRoomChange) {
        var status;
        if (room.status == "open")
            status = 0;
        else if (room.status == "minimized")
            status = 1;
        else // closed
            status = 2;

        var data = {
            room: room.id,
            _xsrf: getCookie("_xsrf"),
            status: status
        };

        var expireSeconds = 5 * 60;

        if (loadedRoomChange) {
            pendingRoomChanges[room.id] = loadedRoomChange;
        }
        else {
            pendingRoomChanges[room.id] = {
                status: status,
                expires: (new Date).getTime() / 1000 + expireSeconds
            };
        }

        var requestExpires = pendingRoomChanges[room.id].expires;

        try {
            if (channels[statusServerId].ajax)
                channels[statusServerId].ajax({
                    type: "POST",
                    url: "/torncity/chat/status/updateroom",
                    data: data,
                    timeout: 10 * 1000,
                    success: function () {
                    // check that the stored change is from this request
                        if (pendingRoomChanges[room.id] && pendingRoomChanges[room.id].expires == requestExpires)
                            pendingRoomChanges[room.id].done = true;
                    }
                });
        }
        catch (err) {
        }
    }
    
    function gotMessages(data) {
        if (typeof data.reconnect != 'undefined') {
            console.log ("Recieved request to gracefully reconnect");
            return;
        }
        var scheduled = {};
        var roomsToSave = {};
        
        var msgs = data.messages, updated = data.updated_rooms;
        for (var i = 0; i < msgs.length; i++) {
            var roomId = msgs[i].room, m = msgs[i];

            if (!rooms[roomId])
                continue;

            rooms[roomId].cursor = m.id;

            if (!rooms[roomId].initializedParticipantStatus && !scheduled[roomId]) {
                // got the messages for room, so we can now fetch the
                // online statuses of the users
                addUserStatusListener(roomId);
                chat.scheduleUserStatusPoll();
                scheduled[roomId] = true;
            }

            rooms[roomId].lastMessages.push(m);
            while (rooms[roomId].lastMessages.length > maxMessagesPerRoom)
                rooms[roomId].lastMessages.shift();
            roomsToSave[roomId] = true;
            
            showMessageInChatBox(m.room, m, jQuery.inArray(m.room, updated) != -1);
        }

        if (!restoringStateFromLocalStorage)
            for (var roomId in roomsToSave)
                jQuery.jStorage.set('roommessages' + roomId, rooms[roomId].lastMessages);
    }

    function gotStatus(data) {
        console.log ("Calling gotStatus with results from callback");
        if (data.rooms != null) {
            jQuery.each(data.rooms, function(i, room) {
                // we don't necessarily trust server to have updated info
                if (pendingRoomChanges[room.id]) {
                    // this is not a perfect way to detect that this
                    // change is now synced, the info we received
                    // could be from an old status change that looks
                    // similar to the latest; but it catches the
                    // common case
                    if (room.status == pendingRoomChanges[room.id].status)
                        pendingRoomChanges[room.id].synced = true;
                    
                    if (!pendingRoomChanges[room.id].done)
                        room.status = pendingRoomChanges[room.id].status;
                }
            });
                        
            if (!restoringStateFromLocalStorage)
                jQuery.jStorage.set('statusrooms', data.rooms);
            
            ++holdOffChatRoomRestructuring;
            jQuery.each(data.rooms, function(i, room) {
                var id = room.id, status = statusMap[+room.status];

                if (!rooms[id]) {
                    // maybe we got a new room
                    addChatRoom(id, room.server, room.name, status, room.participants);
                    return;
                }

                if (status != rooms[id].status) {
                    // or new status
                    setChatRoomStatus(id, status, true);
                }
                    
                if (room.server != rooms[id].server) {
                    // or new server
                    var oldServerId = rooms[id].server;
                    rooms[id].server = room.server;
                    if (channelUnused(oldServerId))
                        longpoll.removeChannel(oldServerId);

                    pollRoomServer(rooms[id].server);
                }
            });

            // check that we didn't delete some rooms
            jQuery.each(rooms, function (i, room) {
                var found = false;
                for (var j = 0; j < data.rooms.length; ++j) {
                    if (data.rooms[j].id == room.id) {
                        found = true;
                        break;
                    }
                }

                if (!found) {
                    removeChatRoom(room.id);
                    jQuery.jStorage.deleteKey('roommessages' + room.id);
                }
            });
            
            --holdOffChatRoomRestructuring;
            restructureChatBoxes();
            
            callCallbacks("rooms");

            // fix wierd problem in IE6 where the chat box doesn't
            // show up immediately
            jQuery("body").focus();
        }

        if (data.users != null) {
            if (!restoringStateFromLocalStorage)
                jQuery.jStorage.set('statususers', data.users);
            for (var u in data.users)
                chat.users[u] = data.users[u];

            // call callbacks of subscribers to the users we've got
            // news about
            callCallbacks("users", function (o) {
                for (var u in data.users)
                    if (jQuery.inArray(u, o.users) != -1)
                        return true;
                
                return false;
            });
        }
    }
    
    function newMessage(message, roomId) {
        if (!rooms[roomId])
            return;
            
        var serverId = rooms[roomId].server;
        if (!channels[serverId])
            return;
        
        //rooms[roomId].chatbox.find(".chatboxtextarea").attr('disabled','disabled');
        rooms[roomId].chatbox.find(".chatboxtextarea").prop("disabled", true);
		
        if (message == '')
            message = ' ';

        function onError(xhr, status, error) {
            if (!rooms[roomId])
                return;
                
            console.log ("Error trying to submit");
            addHtmlToChatBox(roomId, '<div class="error">Sorry, failed to send message...</div>');
            // add back the text so it doesn't disappear completely
			
            //rooms[roomId].chatbox.find(".chatboxtextarea").val(message).attr('disabled','').focus();
			rooms[roomId].chatbox.find(".chatboxtextarea").val(message).prop("disabled", false).focus();
            possiblyEnlargeChatBoxTextArea(rooms[roomId].chatbox.find(".chatboxtextarea"));
        }
        
        try {
            longpoll.whenChannelUp(serverId, function () {
                channels[serverId].ajax({
                    type: "POST",
                    url: "/torncity/chat/message/" + serverId + "/new",
                    data: { body: message, room: roomId },
                    dataType: "json",
                    timeout: 10 * 1000,
                    success: function(msg) {
                        if (!rooms[roomId])
                            return;
                        
                        //rooms[roomId].chatbox.find(".chatboxtextarea").attr('disabled','').focus();
						rooms[roomId].chatbox.find(".chatboxtextarea").prop("disabled", false).focus();
						
                    },
                    error: onError
                });
            });
        }
        catch (err) {
            onError(null, "exception", err);
        }
    }

    function addChatRoom(roomId, serverId, displayName, status, participants, callback) {
        if (rooms[roomId])
            return;

        rooms[roomId] = {};
        rooms[roomId].id = roomId;
        rooms[roomId].name = displayName;
        rooms[roomId].server = serverId;
        rooms[roomId].status = status;
        rooms[roomId].cursor = "";
        rooms[roomId].lastMessages = [];
        rooms[roomId].focused = false;
        rooms[roomId].scrolledToBottom = true;
        rooms[roomId].initializedParticipantStatus = true;
        rooms[roomId].participantStatus = {};
        if (participants) {
            // make participants into a map of uid -> playername
            rooms[roomId].participants = {};
            for (var i = 0; i < participants.length; ++i)
                rooms[roomId].participants[participants[i][0]] = participants[i][1];
            rooms[roomId].initializedParticipantStatus = false;
            
            // fix up room name
            var l = abbreviatedList(getUserNamesInRoom(roomId), 3);
            if (l)
                rooms[roomId].name = l;
        }
        else
            rooms[roomId].participants = null;

        createChatBox(roomId);
        if (!holdOffChatRoomRestructuring)
	    restructureChatBoxes();
        
        if (callback)
            callback(roomId);

        callCallbacks("rooms");

        var storedMessages = jQuery.jStorage.get('roommessages' + roomId);
        if (storedMessages) {
            ++restoringStateFromLocalStorage;
            // set current room as updated as these are old messages
            gotMessages({ messages: storedMessages, updated_rooms: [ roomId ] });
            --restoringStateFromLocalStorage;
        }

        pollRoomServer(serverId);
    }

    function removeChatRoom(roomId, keepUI) {
        console.log("Removing room", roomId);
        var serverId = rooms[roomId].server;
        
        if (rooms[roomId].chatbox) {
	    if (!keepUI)
		rooms[roomId].chatbox.remove();
            rooms[roomId].chatbox = null;
        }
        delete rooms[roomId];

        if (channelUnused(serverId))
            longpoll.removeChannel(serverId);
    }

    function channelUnused(serverId) {
        return getRoomsForServer(serverId).length == 0
            && serverId != statusServerId
            && channels[serverId];
    }

    function setChatRoomStatus(roomId, status, skipServerUpdate) {
        var room = rooms[roomId];
        if (!room)
            return;

         // allow both textual and numeric status
        if (!isNaN(+status))
            status = statusMap[+status];

        var prev = room.status;
        
        room.status = status;
        if (!skipServerUpdate)
            updateServerSideRoomStatus(room);
        if (!holdOffChatRoomRestructuring && status != prev)
            restructureChatBoxes();

        // if we just close it, it's OK, the poll will just timeout
        // and stop; however, if we're opening the box, we need to
        // react; for global rooms where we cut the connection on
        // minimization, we also need to react on an open
        if (prev == "closed" && status != "closed"
            || ((roomId == "Global" || roomId == "Trade" || roomId == "New_Players")
                && prev == "minimized" && status == "open"))
            pollRoomServer(room.server);
    }

    // subscribe to changes in online status
    function addUserStatusListener(roomId) {
        if (!rooms[roomId])
            return;
        
        chat.listenForUserChanges(
            "participants:" + roomId, getUserIdsInRoom(roomId),
            function () { userStatusChangesForRoom(roomId); });
    }
    
    function userStatusChangesForRoom(roomId) {
        var room = rooms[roomId];
        if (!room)
            return;
        
        addUserStatusListener(roomId);

        var users = getUserIdsInRoom(roomId);
        
        if (!room.initializedParticipantStatus) {
            room.initializedParticipantStatus = true;

            var online =
                jQuery.grep(users, function (uid) {
                    return chat.users[uid] == chat.USER_STATUS_ONLINE;
                });

            var s = "";
            if (online.length == 0) {
                s = "You are the only one online";
            }
            else if (online.length == 1) {
                s = room.participants[online[0]] + " is online";
            }
            else {
                s = abbreviatedList(jQuery.map(online, function (uid) {
                    return room.participants[uid];
                }), 3);

                s += " are online";
            }
            addHtmlToChatBox(roomId, '<div><i>' + s + '</i></div>');
        }
        else {
            var wentOnline = [], wentOffline = [];
            
            jQuery.each(users, function (i, uid) {
                var from = room.participantStatus[uid], to = chat.users[uid];
                if (from != to) {
                    if (to == chat.USER_STATUS_OFFLINE)
                        wentOffline.push(room.participants[uid]);
                    else if (to == chat.USER_STATUS_ONLINE)
                        wentOnline.push(room.participants[uid]);
                }
            });

            var html = [];
            if (wentOffline.length > 0)
                html.push(abbreviatedList(wentOffline, 3) + " went offline");
            if (wentOnline.length > 0)
                html.push(abbreviatedList(wentOnline, 3) + " comes online");

            if (wentOffline.length + wentOnline.length > 0)
                addHtmlToChatBox(roomId, '<div><i>' + html.join(", ") + '</i></div>');
        }

        // update the rooms view
        jQuery.each(users, function (i, uid) {
            room.participantStatus[uid] = chat.users[uid];
        });
    }
    
    function savePendingRoomChanges() {
        // merge in pending room changes
        var originalPending = jQuery.jStorage.get('pendingroomchanges', null),
            pending = jQuery.extend({}, originalPending);
        
        var anythingChanged = false;
        for (id in pendingRoomChanges) {
            if (id in pending && pending[id].expires >= pendingRoomChanges[id].expires)
                continue;

            anythingChanged = true;
            pending[id] = pendingRoomChanges[id];
        }

        function isEmpty(o) {
            for (var name in o)
                return false;
            return true;
        }

        // prune old
        var now = (new Date()).getTime() / 1000;
        for (id in pending)
            if (now > pending[id].expires)
                delete pending[id];

        // make sure saved room status is up to date with our local
        // changes
        var notSynced = {};
        for (id in pending)
            if (!pending[id].synced)
                notSynced[id] = pending[id];

        if (!isEmpty(notSynced) && anythingChanged) {
            var savedRooms = jQuery.jStorage.get('statusrooms', null);
            if (savedRooms) {
                jQuery.each(savedRooms, function(i, room) {
                    if (notSynced[room.id])
                        room.status = notSynced[room.id].status;
                });
                jQuery.jStorage.set('statusrooms', savedRooms);
            }
        }


        // save local changes for next page load
        var stillPending = jQuery.extend({}, pending);
        for (id in pending)
            if (!pending[id].done)
                stillPending[id] = pending[id];

        if (!isEmpty(stillPending)) {
            if (anythingChanged) {
                jQuery.jStorage.set('pendingroomchanges', stillPending);
            }
        }
        else if (originalPending) {
            // cleanup
            jQuery.jStorage.deleteKey("pendingroomchanges");
        }
    }

    function loadPendingRoomChanges() {
        // check the saved stuff to see if we need to do something 
        var pending = jQuery.jStorage.get('pendingroomchanges', {});
        var now = (new Date()).getTime() / 1000;
        var res = {};
        for (id in pending)
            if (now <= pending[id].expires)
                res[id] = pendingRoomChanges[id] = pending[id];
        return res;
    }
    
    function stopChat(keepUI) {
        chat.running = false;

        console.log ("Stopping chat");

        var i = jQuery.inArray(chat.stopWithButton, longpoll.inactiveCallbacks);
        if (i != -1)
            longpoll.inactiveCallbacks.splice(i, 1);

        savePendingRoomChanges();
        
        var id;
        
        // whack rooms
        for (id in rooms)
            removeChatRoom(id, keepUI);

        // room servers should be gone now, so just whack status server
        longpoll.removeChannel(statusServerId);
    }

    function startChat() {
        jQuery("#reenableChatButton").remove();
        jQuery("#chatWelcomeMessage").remove();
        
        if (chat.running) {
            console.log ("Chat already running");
            return;
        }

        if (jQuery.inArray(chat.stopWithButton, longpoll.inactiveCallbacks) == -1)
            longpoll.inactiveCallbacks.push(chat.stopWithButton);
        
        
	// clean up server connections before we leave the page,
	// however keep the UI intact so the page transition appears
	// seamless
        window.onbeforeunload = function() {
            stopChat(true);
        };
        
        try {
            chat.running = true;

            var pending = loadPendingRoomChanges();
            
            var storedData = {
                rooms: jQuery.jStorage.get('statusrooms'),
                users: jQuery.jStorage.get('statususers')
            };
            
            if (storedData.rooms) {
                console.log("got status from local storage");
                ++restoringStateFromLocalStorage;
                gotStatus(storedData);
                --restoringStateFromLocalStorage;
            }
            
            longpoll.whenChannelUp(statusServerId, function (serverId) {
                for (var id in pending)
                    if (id in rooms)
                        updateServerSideRoomStatus(rooms[id], pending[id]);
                pollStatusServer(statusServerId);
            });
        } catch (err) {
            console.log("Catched exception, error", err);
            stopChat();
        }
    }

    function setupReenableChatButton() {
        if (jQuery("#reenableChatButton").length > 0)
            return;

        jQuery('<div id="reenableChatButton">Reenable chat</div>')
            .appendTo(document.body)
            .click(function () {
                jQuery("#reenableChatButton").remove();
        
                if (!chat.running)
                    chat.start();
            });
    }

    function displayWelcome() {
        if (jQuery("#chatWelcomeMessage").length > 0)
            return;
        
        jQuery('<div id="chatWelcomeMessage">This is in-game chat. Click to start. Minimize a room by clicking the title or close the rooms on <a href="/managerooms.php">the chat page</a>.</div>')
            .appendTo(document.body)
            .click(function (e) {
                if (e.target != this)
                    return;
                
                jQuery("#chatWelcomeMessage").remove();
                jQuery.cookie("hidechatwelcome", 1, { expires: 1, path: '/'});
        
                if (!chat.running)
                    chat.start();
            });
    }
})();
