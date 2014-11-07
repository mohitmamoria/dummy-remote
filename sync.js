var io = require('socket.io')(8080);
var redis = require('redis');

var prepare = function() {
	io.use(function(socket, next) {
		socket.handshake.remote = {
			target: socket.handshake.query.target,
			salt: socket.handshake.query.salt
		};
		next();
	});
}


prepare();

io.on('connection', function(socket) {
	var redisSub = redis.createClient('6379', '127.0.0.1');
	var redisPub = redis.createClient('6379', '127.0.0.1');
	var prefix = 'remote:order';
	
	redisSub.on('subscribe', function(channel, count) {
		console.log('Successfully subscribed to ' + channel + ' (total ' + count + ' connections)');
	});
	// connecting to "remote:order:computer:aBc"
	redisSub.subscribe(prefix + ':' + socket.handshake.remote.target + ':' + socket.handshake.remote.salt);

	redisSub.on('message', function(channel, data) {
		socket.emit('order', JSON.parse(data));
	});

	socket.on('order', function(order) {
		redisPub.publish(prefix + ':' + order.target + ':' + order.salt, JSON.stringify(order));
	});
});