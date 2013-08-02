var link = {
    data : null,
    action : null,
    nick : null,
    url : 'ws://10.0.0.8:8000/chat',
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
        link.nick= prompt("Please enter your name:","Your name")
        link.request("user-nick",link.nick)
        console.log("entro men")
    },
    stopConnection : function(msg){
        
    },
    processMessage : function(msg){
        link.data=JSON.parse(msg.data);
        console.log(link.data);
    },
    request : function(action,data){
        payload=new Object();
        payload.action=action
        payload.data=data
        link.socket.send(JSON.stringify(payload))
    }
};
var userlist={
    data : null,
    init : function(){
        link.request("users-list","")
        //userlist.data = link.data;
        return link.data;
    },
} 