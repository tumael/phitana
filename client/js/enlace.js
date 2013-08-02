var enlace = {

    direccion : 'ws://10.0.0.8:8000/chat',
    socket : null,
    init: function (info){

        try{
            socket = new WebSocket(enlace.direccion);
            socket.onopen    = enlace.detenerConexion;
            socket.onmessage = enlace.procesarMensaje;
            socket.onclose   = enlace.iniciarConexion;
        }catch( error){
            console.log(error);
        }
    },
    iniciarConexion: function(info){
        
    },
    detenerConexion: function(info){

    },
    procesarMensaje:function(info){

    },
};