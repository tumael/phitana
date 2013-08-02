var link = {
    info : null,
    action : null,
    url : 'ws://10.0.0.8:8000/tictactoe',
    socket : null,
    init: function (msg){
        try{
            link.socket = new WebSocket(link.url);
            link.socket.onopen    = link.initConnection;
            link.socket.onmessage = link.processMessage;
            link.socket.onclose   = link.stopConnection;
        }catch( error){
            console.log( "Init:" + error);
        }
    },
    initConnection: function(msg){
        nick = prompt("Please enter your name:","Your name");
        link.request("user-nick",nick);
    },
    stopConnection : function(msg){
        alert("Logout.");
    },
    processMessage : function(msg){
        if(msg){
            link.info=JSON.parse(msg.data);
        }
    },
    request : function(action,data){
        payload=new Object();
        payload.action=action
        payload.data=data
        link.socket.send(JSON.stringify(payload))
    },
};


var UserInfo = {

    data : null,
    init : function( dataUser){
        UserInfo.data =  dataUser;
    },
    listUser : function(){
        link.request("user-list",'');
        return link.info.data;
    },
    changeNameUser :function( newNickName){
        link.request("user-nick", newNickName);
    },
    challengeUser : function(idChallengeUser){
        link.request("user-challenge", idChallengeUser);
    },
    acceptChallangeUser :function (idAcceptChallangeUser){
        link.request("user-accept", idAcceptChallangeUser);
    },
    logoutUser : function(){
        link.request("user-delete", link.nick);
    },
}

window.addEventListener('load',link.init,false)
            
function listUser(){
    list = UserInfo.listUser();
    console.log(list);
}