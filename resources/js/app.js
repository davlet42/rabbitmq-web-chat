window.Stomp = require('stompjs');

window.Rabbit = {
    token: null,
};

Rabbit.generateExchange = () => {
    Rabbit.token = 'TheRealRabbitTester';
};

Rabbit.messageHandler = (data) => {
    console.log(data.body);
    try {
        const json = JSON.parse(data.body);

        const methods = {
            message: () => {
                // Get message
                console.log(json.body);
            },
            reload: () => {
                if (json.body === true) document.location.reload();
            }
        };

        if (!(json.type in methods)) {
            console.log('Method not implemented: ', json);
            return;
        }

        methods[json.type]();
    } catch (e) {
        console.log('Error processing message: ', e);
    }
};

Rabbit.init = async () => {
    await Rabbit.generateExchange();

    const ws = new WebSocket('ws://localhost:15674/ws');
    const client = Stomp.over(ws);

    client.heartbeat.outgoing = 4000;
    client.heartbeat.incoming = 4000;
    client.reconnect_delay = 5000;
    client.debug = null;

    client.connect(
        'guest',
        'guest',
        (x) => {
            client.subscribe('/exchange/'+Rabbit.token, Rabbit.messageHandler);
        },
        () => {
            console.log('Error connecting to STOMP server, the exchange may not have been created');
        },
        '/'
    );

    console.log('Rabbit has been initialized');
};

Rabbit.init();
