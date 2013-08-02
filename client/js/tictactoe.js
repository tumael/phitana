var Behaviors=new(function(){
    this.prompt=function(){
        var nick=prompt('Ingrese su nombre de usuario','Nombre:')
        Socket.nickChange(nick)
    }
    this.cleanUsers=function(){
        $('.list-gamers ul').html('')
    }
    this.renderUsers=function(users){
        for (var i in users) {
            $('.list-gamers ul').append(
                '<li id="'+users[i].id+'"><span>'+users[i].nick+'</span></li>')
        }
        $('.count-gamers').html(users.length)
    }
    this.addUser=function(user){
        $('.list-gamers ul').append(
            '<li id="'+user.id+'"><span>'+user.nick+'</span></li>')
    }
    this.removeUser=function(id){
        $('.list-gamers ul li#'+id).remove()
    }
})()

var Socket=new(function(){
    this.socket=null
    this.nickChange=function(nick){
        var payload
        payload=new Object()
        payload.action='user-nick'
        payload.data=nick
        this.socket.send(JSON.stringify(payload))
    }
    this.requestUsers=function(){
        var payload
        payload=new Object()
        payload.action='users-list'
        payload.data=''
        this.socket.send(JSON.stringify(payload))
    }
})()

$(document).ready(function(){
    var url='ws://10.0.0.8:8000/tictactoe'
    var socket
    if(window.MozWebSocket){
      socket=new MozWebSocket(url)
    } else if(window.WebSocket){
      socket=new WebSocket(url)
    }
    Socket.socket=socket
    socket.onopen=function() {
        Behaviors.prompt()
    }
    socket.onmessage=function(msg){
        var response=JSON.parse(msg.data)
        var action=response.action
        var data=response.data

        console.log(action)

        switch(action){
            case 'connect':
                Socket.requestUsers()
                break;
            case 'user-new':
                Behaviors.addUser(data)
                break;
            case 'user-delete':
                Behaviors.removeUser(data)
                break;
            case 'users-list':
                Behaviors.renderUsers(data)
                break;
        }
    }
    socket.onclose=function(msg){
        Behaviors.cleanUsers()
    }
})
