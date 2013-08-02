var link=new(function(){$(document).ready(function(){
//    var data=null
    var action=null
    var nick=null
    var url='ws://10.0.0.8:8000/tictactoe'
    var socket
    if(window.MozWebSocket){
      socket=new MozWebSocket(url);
    } else if(window.WebSocket){
      socket=new WebSocket(url);
    }
    socket.onopen=function(msg){
        nick= prompt("Please enter your name:","Your name")
        registrar('users-list','')
        registrar('user-nick',nick)
        console.log(nick)
    }
    socket.onmessage=function(msg){
        var response=JSON.parse(msg.data)
        evaluar(response)
//        var date=new Date()
//        return $('#messages').append('<p>'+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+' - '+response.data+'</p>')
    }
    socket.onclose=function(msg){
//        return $('#status').removeClass().addClass('offline')
    }
//    $('input[type="text"]').focus()
//    $('form').submit(function(){
//        var msg = $('input[name="msg"]').val()
//        var payload
//        payload=new Object()
//        payload.action="message"
//        payload.data=msg
//        socket.send(JSON.stringify(payload))
//        $('input[name="msg"]').val('')
//        return false
//    })

var registrar=function(action,data){
    var payload
    payload=new Object()
    payload.action=action
    payload.data=data
    socket.send(JSON.stringify(payload))
}
var enviar=function(){
    
        
}
var evaluar=function(responce){
//    console.log(responce.data)
    var data=responce.data
        console.log(data)
//        var test = 0
    switch(responce.action){
        case 'users-list':
            for (var da in data) {
//                console.log(data[da].nick)
                $('.list-gamers ul').append('<li id="'+da+'"><span>'+data[da].nick+'</span></li>')
            }
            $('.count-gamers').html(data.length)
            $('.list-gamers ul li span').click(function(){
                    var test = $(this).parent().attr('id')
                    console.log(data[test])
                })
            
            break
        case 'aa':
            break
    }
}
var listar=function(lista){
    $.each(lista,function(id){
        console.log(id)
    })
}
  })
})()