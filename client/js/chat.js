(function() {
  $(document).ready(function(){
    var serverUrl
    var socket
    serverUrl='ws://10.0.0.8:8000/chat'
    if(window.MozWebSocket){
      socket=new MozWebSocket(serverUrl);
    } else if(window.WebSocket){
      socket=new WebSocket(serverUrl);
    }
    socket.onopen=function(msg){
        return $('#status').removeClass().addClass('online')
    }
    socket.onmessage=function(msg){
        var response=JSON.parse(msg.data)
        var date=new Date()
        return $('#messages').append('<p>'+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+' - '+response.data+'</p>')
    }
    socket.onclose=function(msg){
        return $('#status').removeClass().addClass('offline')
    }
    $('input[type="text"]').focus()
    $('form').submit(function(){
        var msg = $('input[name="msg"]').val()
        var payload
        payload=new Object()
        payload.action="message"
        payload.data=msg
        socket.send(JSON.stringify(payload))
        $('input[name="msg"]').val('')
        return false
    })
  })
}).call(this);
