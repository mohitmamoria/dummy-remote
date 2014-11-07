<!DOCTYPE html>
<html>
<head>
	<title>Remote</title>
</head>
<body>
	<button>Blink!</button>

	<script src="http://localhost:8080/socket.io/socket.io.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script>
	var socket = io('http://localhost:8080', {query: 'target=computer'});
	
	function getRandomColor() {
		var letters = '0123456789ABCDEF'.split('');
		var color = '#';
		for (var i = 0; i < 6; i++ ) {
			color += letters[Math.floor(Math.random() * 16)];
		}
		return color;
	}

	function changeBackground(color) {
		$('body').css('background', color || getRandomColor());
	}

	socket.on('connect', function() {
		console.info('Paired successfully!');
	});

	socket.on('order', function(order) {
		// order is an object with two keys: doer (required) and the thing (optional)
		console.log('Got an order: ', order);
		window[order.doer](order.thing);
	});

	
	$('button').click(function(e) {
		socket.emit('order', {target: 'mobile', doer: 'changeBackground'});
	});
	</script>
</body>
</html>