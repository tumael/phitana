function iniciar () {
	var socket = new WebSocket("ws:10.0.0.9:8000/phitana");

	socket.onopen=function() {
		var user;
		user=new Object();
		user.action="auth";
		user.data=document.getElementById('nick').value;

		socket.send(JSON.stringify(user));
	}

	socket.onmessage=function (datos) {
		recibido("Lista: " + datos.data);
		console.log(datos.data);
	}
}

function recibido (datos) {
	
}

$(document).ready(function(){
    $(".oculto").hide();             
    $(".MO").click(function(){
          var nodo = $(this).attr("href"); 
 
          if ($(nodo).is(":visible")){
               $(nodo).hide();
               return false;
          }else{
        $(".oculto").hide();                            
        $(nodo).fadeToggle( "slow" );
        return false;
          }
    });
});