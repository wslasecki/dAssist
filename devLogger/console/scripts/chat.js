var Chat = {

// Parameters.
session: gup("session"),
// Round state is stored internally now to prevent user/worker tampering
round: gup("round") ? gup("round") : 0,
role: "user",
worker: gup('workerId') ? gup('workerId') : "DEFAULT_WORKER",
turn: "other",
numQuestionsAsked: 0,

// Last message ID retrieved from server.
lastid: -1,
active: true,
wasActive: true,

fetchNew: function() {
    $.ajax({
        url: "../common_php/conv_chat_fetch.php?session="+Chat.session+"&lastid="+Chat.lastid+"&round="+Chat.round,
        type: "POST",
        dataType: "json",
        success: function(d) {
            // Set the current number of chat messages
            Chat.numQuestionsAsked = parseInt(d.qCount);
            $('#num-questions-count').text(Chat.numQuestionsAsked);

            var chats = d.chats;

            for( var i = 0; i < chats.length; i++ ) {
                var curRole = chats[i].role == "user" ? "you" : chats[i].role;

                // Prevent repeated posts
                if( $('#chat_'+chats[i].id).length > 0 ) {
                    console.log("Fetch redundant chat: #", chats[i].id);
                    continue;
                }

                var chatLine = $("<li class='chat_messages " + curRole + "' chat_id='" + chats[i].id + "'id='chat_" + chats[i].id + "'><span class='chat_role'>" + (curRole == "crowd" ? "bot-or-not" : curRole) + "</span><span class='chat_username'>" + chats[i].user + "</span>: <span class='chat_message'>" + chats[i].chat + "</span> <span class='chat_time'>" + chats[i].time + "</span></li>");
                $("#chat_area").append(chatLine);

                // Alert the user if they are on a different tab / window
                if( !Alert.isActive && chats[i].role != Chat.role ) {
                    //alert("You've recieved a message!");
                }


                if(chats[i].id > Chat.lastid) {
                    Chat.lastid = chats[i].id;
                }

                if(chats[i].role == "user") {
                    Chat.active = false
                } else {
                    Chat.active = true

                    Chat.turn = "other";
                    Chat.round++;
                }
            }

            if( Chat.active == false ) {
                $('#chat_box').attr('disabled', 'disabled');
                $('#chat_box').addClass('chat_disabled');
                $('#chat_area').css('background', '#EEE');
                if( Chat.wasActive ) {
                    $('#chat_box').val('Great question! Please wait for a response =)');
                    Chat.wasActive = false;
                }
            }
            else {
                $('#chat_box').removeAttr('disabled');
                $('#chat_box').removeClass('chat_disabled');
                $('#chat_area').css('background', '#FFFFEF');
                if( !Chat.wasActive ) {
                    $('#chat_box').val($('#chat_box').attr('title'));
                    //alert("You have recieved a response from the crowd.");
                    Chat.wasActive = true;

                }

            }

        if( Chat.startOfRound < 0 ) {
        Chat.startOfRound = Chat.lastid;
        }

            if( chats.length > 0 ) {
                $("#chat_area").animate({ scrollTop: $("#chat_area").prop("scrollHeight") }, 3000);
            }

            var valids = d.valid_ids;
            $(".user").addClass("set");

        },
    error: function(e){
    }
 });
},



init: function() {
    $('#chat_box').keypress(function(e) {
        if(e.which == 13){
            e.preventDefault();
            var curRole = "user"
            // Use this for multiple-chat-per-round mode
            if( Chat.turn != "self" ) {
                Chat.round++
            }

            Chat.turn = "self"
            $.ajax({
                type: "POST",
                url: "../common_php/conv_chat_post.php",
                data: {session: Chat.session, chat: $("#chat_box").val(), worker: Chat.worker, role: curRole, round: Chat.round, type: "user_chat_input"},
                dataType: "text",
                success: function(d) {
                    // TODO_mist: Either make this stateful, or remove state from the rest of the process (e.g., ALWAYS route to new session on refresh -- and add warning on refresh)
                    //$('#num-questions-count').text(parseInt($('#num-questions-count').text().trim())+1);
                    Chat.numQuestionsAsked = parseInt(d);
                    $('#num-questions-count').text(Chat.numQuestionsAsked);
                }
            });

            // Use this round increment for single-chat-per-round mode
            //Chat.round++;
            $('#chat_box').val('');
        }
        else{
          if( $('#chat_box').val().length() >= 100 ) {
            return false;
          }
    });

    $("#chat_area").mousemove(function() {
        $("#chat_area").stop();
    });

    $(".chat_defaultText").focus(function(srcc) {
        if ($(this).val() == $(this)[0].title) {
            $(this).removeClass("chat_defaultTextActive");
            $(this).val("");
        }
    });
    
    $(".chat_defaultText").blur(function() {
        if ($(this).val() == "") {
            $(this).addClass("chat_defaultTextActive");
            $(this).val($(this)[0].title);
        }
    });

    $(".chat_defaultText").blur();


    // set updaters
    Chat.fetchNew();
    setInterval(Chat.fetchNew, 1500);



}

$(document).ready( function() {
    Chat.init()
});

