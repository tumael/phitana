var Behaviors = new (function() {
    this.count_users = 0
    this.prompt = function() {
        var nick = prompt('Ingrese su nombre de usuario', 'Nombre:')
        Socket.nickChange(nick)
    }
    this.cleanUsers = function() {
        $('.list-gamers ul').html('')
        Behaviors.setCountUsers(0)
    }
    this.renderUsers = function(users) {
        for (var i in users) {
            $('.list-gamers ul').append(
                '<li id="' + users[i].id + '">'+
                '<span>' + users[i].nick + '</span>'+
                '<a class="hide" href="">Retar</></li>')
        }
        $('.list-gamers > ul > li').click(function() {
            $('.list-gamers > ul > li').removeClass()
            $('.list-gamers > ul > li > a').removeClass().addClass('hide')
            $(this).removeClass().addClass('challenger')
            $(this).children('a').removeClass().click(function() {
                Socket.challengeUser($(this).parent().attr('id'))
                return false
            })
        })

        Behaviors.setCountUsers(users.length)
    }
    this.addUser = function(user) {
        $('.list-gamers ul').append(
                '<li id="' + user.id + '"><span>' + user.nick + '</span></li>')
        Behaviors.setCountUsers(Behaviors.count_users + 1)
    }
    this.removeUser = function(id) {
        $('.list-gamers ul li#' + id).remove()
        Behaviors.setCountUsers(Behaviors.count_users - 1)
    }
    this.setCountUsers = function(count) {
        Behaviors.count_users = count
        $('.count-gamers').html(Behaviors.count_users)
    }
    this.challengeUser = function(user) {
        $('.list-gamers ul li#' + user).removeClass()
                .addClass('play')
                .children('a').removeClass().click(function() {
            //Socket.challengeUser($(this).parent().attr('id'))
            return false
        })
    }
})()

var Socket = new (function() {
    this.payload = {}
    this.socket = null
    this.nickChange = function(nick) {
        this.payload.action = 'user-nick'
        this.payload.data = nick
        this.socket.send(JSON.stringify(this.payload))
    }
    this.requestUsers = function() {
        this.payload.action = 'users-list'
        this.payload.data = ''
        this.socket.send(JSON.stringify(this.payload))
    }
    this.challengeUser = function(id) {
        this.payload.action = 'user-challenge'
        this.payload.data = id
        this.socket.send(JSON.stringify(this.payload))
    }
})()

$(document).ready(function() {
    var url = 'ws://127.0.0.1:8000/tictactoe'
    var socket
    if (window.MozWebSocket) {
        socket = new MozWebSocket(url)
    } else if (window.WebSocket) {
        socket = new WebSocket(url)
    }
    Socket.socket = socket
    socket.onopen = function() {
        Behaviors.prompt()
    }
    socket.onmessage = function(msg) {
        var response = JSON.parse(msg.data)
        var action = response.action
        var data = response.data

        console.log(action)

        switch (action) {
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
            case 'user-challenge':
                Behaviors.challengeUser(data)
                break;
        }
    }
    socket.onclose = function(msg) {
        Behaviors.cleanUsers()
    }
})
