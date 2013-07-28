(function() {
  $(document).ready(function() {
    var log, serverUrl, socket;
    log = function(msg) {
      return $('#log').append("" + msg + "<br />");
    };
    serverUrl = 'ws://10.0.0.9:8000/chat';
    if (window.MozWebSocket) {
      socket = new MozWebSocket(serverUrl);
    } else if (window.WebSocket) {
      socket = new WebSocket(serverUrl);
    }
    socket.onopen = function(msg) {
      return $('#status').html('online');
    };
    socket.onmessage = function(msg) {
      var response = JSON.parse(msg.data)
      return $('#log').append('<p>'+response.data+'</p>');
    }
    socket.onclose = function(msg) {
      return $('#status').html('offline');
    };
    $('form').submit(function(){
        var msg = $('input[name="msg"]').val()
        var payload
        payload=new Object()
        payload.action="message"
        payload.data=msg
        socket.send(JSON.stringify(payload))
        return false
    })
  });
}).call(this);
