var Templates=new(function(){
    this.user_item='<li id="{0}"><img src="img/avatar-small.png" />'
        +'<h2>{1}</h2><ul><li>{2}</li></ul>'
        +'<div class="clean"></div></li>'
    this.chat_footer='<form class="message"><img src="img/avatar-small.png" />'
        +'<div><input type="text" name="message" />'
        +'<input type="submit" value="enviar" /></div></form>'
})()

var Events=new(function(){
    this.clickUser=function(){
        $('ul#list > li').removeClass('focus')
        $(this).addClass('focus')
    }
    this.chatUser=function(){
        console.log('to chat...')
    }
})

var Behaviors=new(function(){
    this.count_users=0
    this.prompt=function(){
        var nick=prompt('Ingrese su nombre de usuario','')
        if (nick){
            Listener.nickChange(nick)
            $('header h1').html(nick)
            $('input[type="text"]').focus()
            return true
        }
        return false
    }
    this.cleanUsers=function(){
        $('#list').html('')
        this.setTotal(0)
    }
    this.setStatus=function(status){
        $('header div.status').removeClass().addClass('status').addClass(status)
    }
    this.renderUsers=function(users){
        this.cleanUsers()
        for(var i in users){
            $('#list').append(
                Templates.user_item.format(
                    users[i].id,users[i].nick,users[i].status))
        }
        this.setTotal(users.length)
        $('ul#list > li').click(Events.clickUser)
        $('ul#list > li').dblclick(Events.chatUser)
    }
    this.setTotal=function(count){
        Behaviors.count_users=count
        $('header div.counters span:nth-child(1)').html('('+count+')')
    }
    this.addUser=function(user){
        $('#list').append(
            Templates.user_item.format(user.id,user.nick,user.status))
        $('#'+user.id).click(Events.clickUser)
        Behaviors.setTotal(Behaviors.count_users+1)
    }
    this.removeUser=function(id){
        $('#'+id).remove()
        Behaviors.setTotal(Behaviors.count_users-1)
    }
    this.setNick=function(user){
        $('#'+user.id+' h2').html(user.nick)
    }
})()

var Listener=new(function(){
    this.payload={}
    this.nickChange=function(nick){
        this.payload.action='user-nick'
        this.payload.data=nick
        Socket.send(this.payload)
    }
    this.requestUsers=function(){
        this.payload.action='users-list'
        this.payload.data=''
        Socket.send(this.payload)
    }
})()

var Socket=new(function(){
    this.socket=null
    this.init=function(url){
        if(window.MozWebSocket){
            this.socket=new MozWebSocket(url)
        }else if(window.WebSocket){
            this.socket=new WebSocket(url)
        }
        this.socket.onopen=this.onopen
        this.socket.onclose=this.onclose
        this.socket.onmessage=this.onmessage
    }
    this.send=function(payload){
        this.socket.send(JSON.stringify(payload))
    }
    this.onopen=function(){
        if(Behaviors.prompt()){
            Behaviors.setStatus('available')
        }else{
            //TODO Disconnect
        }
        console.log('open')
    }
    this.onclose=function(){
        Behaviors.cleanUsers()
        Behaviors.setStatus('disconnect')
        console.log('close')
    }
    this.onmessage=function(msg){
        var response=JSON.parse(msg.data)
        var action=response.action
        var data=response.data

        switch(action){
            case 'connect':
                Listener.requestUsers()
                break;
            case 'users-list':
                Behaviors.renderUsers(data)
                break;
            case 'user-new':
                Behaviors.addUser(data)
                break;
            case 'user-delete':
                Behaviors.removeUser(data)
                break;
            case 'user-nick':
                Behaviors.setNick(data)
                break;
        }
        console.log(action)
    }
})()

$(document).ready(function(){
    jQuery.fn.exists = function(){return this.length>0}
    Socket.init('ws://127.0.0.1:8000/whatchat')
    
    $('header h1').click(function(){
        Behaviors.prompt()
    })
})
