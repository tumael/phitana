var enlace = {
    data : null,
    action : null,
    nick : null,
    direccion : 'ws://10.0.0.8:8000/chat',
    socket : null,
    init: function (msg){

        try{
            enlace.socket = new WebSocket(enlace.direccion);
            enlace.socket.onopen    = enlace.iniciarConexion;
            enlace.socket.onmessage = enlace.procesarMensaje;
            enlace.socket.onclose   = enlace.detenerConexion;
        }catch( error){
            console.log(error);
        }
    },
    iniciarConexion: function(msg){
        enlace.nick= prompt("Please enter your name:","Your name")
        enlace.format("user-nick",enlace.nick)
        console.log("entro men")
    },
    detenerConexion : function(msg){
        
    },
    procesarMensaje : function(msg){
        enlace.data=JSON.parse(msg.data)
        console.log(enlace.data)
    },
    format : function(action,data){
        payload=new Object()
        payload.action=action
        payload.data=data
        enlace.socket.send(JSON.stringify(payload))
    }
};
var userlist={
    userlist : null,
    init : function(){
        enlace.format("users-list","")
    },
} 