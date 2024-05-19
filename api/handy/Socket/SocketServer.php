<?php

namespace Handy\Socket;

use Handy\Socket\Exception\SocketServerLaunchException;
use Handy\Socket\Exception\UnsupportedClientClassException;
use Socket;

class SocketServer implements IEventFlow
{

    /**
     * @var string
     */
    public string $clientClass;
    /**
     * @var string
     */
    public string $ip;
    /**
     * @var int
     */
    public int $port;
    /**
     * @var int
     */
    public int $maxBufferSize;
    /**
     * @var Socket
     */
    public Socket $master;
    /**
     * @var array
     */
    public array $heldMessages;
    /**
     * @var array
     */
    public array $sockets;
    /**
     * @var array
     */
    public array $clients;
    /**
     * @var array
     */
    public array $rooms;
    /**
     * @var array
     */
    public array $events;

    /**
     * @param string $ip
     * @param int $port
     * @param int $maxBufferSize
     * @param string $clientClass
     * @throws SocketServerLaunchException
     * @throws UnsupportedClientClassException
     */
    public function __construct(string $ip, int $port, int $maxBufferSize = 2048, string $clientClass = SocketClient::class)
    {
        if (!($clientClass == SocketClient::class || is_subclass_of($clientClass, SocketClient::class))) {
            throw new UnsupportedClientClassException("Class $clientClass is not inherited from " . SocketClient::class);
        }

        $this->clientClass = $clientClass;
        $this->sockets = [];
        $this->clients = [];
        $this->rooms = [];
        $this->events = [];
        $this->heldMessages = [];
        $this->maxBufferSize = $maxBufferSize;
        $this->ip = $ip;
        $this->port = $port;
        $this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or throw new SocketServerLaunchException("Failed to create server socket.");
        socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1) or throw new SocketServerLaunchException("Failed to configure server socket.");
        socket_bind($this->master, $this->ip, $this->port) or throw new SocketServerLaunchException("Failed to start listening.");
        socket_listen($this->master, 256) or throw new SocketServerLaunchException("Failed to start listening.");
        $this->sockets['m'] = $this->master;
    }

    /**
     * @return mixed
     */
    public function run(): mixed
    {
        echo "Socket server started\nListening on $this->ip:$this->port" . PHP_EOL;
        while (true) {
            if (empty($this->sockets)) {
                $this->sockets['m'] = $this->master;
            }
            $read = $this->sockets;
            $write = $except = null;
            $this->_tick();
            $this->tick();
            @socket_select($read, $write, $except, 1);
            foreach ($read as $socket) {
                if ($socket == $this->master) {
                    $client = socket_accept($socket);
                    if (!$client) {
                        echo "Failed to accept new connection" . PHP_EOL;
                    } else {
                        $this->connect($client);
                        echo "Client " . $this->getClientBySocket($client)->id . " connected" . PHP_EOL;
                    }
                    continue;
                }

                $numBytes = @socket_recv($socket, $buffer, $this->maxBufferSize, 0);
                if ($numBytes === false) {
                    $errorCode = socket_last_error($socket);
                    if (in_array($errorCode, [
                        102,
                        103,
                        104,
                        108,
                        110,
                        111,
                        112,
                        113,
                        121,
                        125
                    ])) {
                        echo "Unusual disconnect on socket $socket->id. Code: $errorCode" . PHP_EOL;
                        $this->disconnect($socket, true, $errorCode);
                    } else {
                        echo "Socket error with client $socket->id: " . socket_strerror($errorCode) . PHP_EOL;
                    }
                    continue;
                }

                if ($numBytes == 0) {
                    $this->disconnect($socket);
                    echo "Client disconnected. TCP connection lost" . PHP_EOL;
                    continue;
                }

                $client = $this->getClientBySocket($socket);
                if ($client->handshake === null) {
                    $tmp = str_replace("\r", '', $buffer);
                    if (!str_contains($tmp, "\n\n")) {
                        continue;
                    }
                    $this->doHandshake($client, $buffer);
                } else {
                    $this->splitPacket($numBytes, $buffer, $client);
                }
            }
        }
    }

    /**
     * @return void
     */
    protected function tick()
    {
    }

    /**
     * @return void
     */
    protected function _tick(): void
    {
        foreach ($this->heldMessages as $key => $message) {
            $clientFound = false;
            foreach ($this->clients as $currentClient) {
                if ($message['client']->socket != $currentClient->socket) {
                    continue;
                }
                $clientFound = true;
                if ($currentClient->handshake !== null) {
                    unset($this->heldMessages[$key]);
                    $this->send($currentClient, $message['message']);
                }
            }
            if (!$clientFound) {
                unset($this->heldMessages[$key]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, object $callback): void
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }
        $this->events[$event][] = $callback;
    }

    /**
     * @inheritDoc
     */
    public function removeListener(string $event, object $callback): void
    {
        if (!isset($this->events[$event])) {
            return;
        }
        $this->events[$event] = array_filter($this->events[$event], fn($cb) => $cb !== $callback);
    }

    /**
     * @inheritDoc
     */
    public function removeAllListeners(string $event): void
    {
        unset($this->events[$event]);
    }

    /**
     * @inheritDoc
     */
    public function notifyListeners(string $event, mixed $data, ?SocketClient $client = null): void
    {
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $listener) {
                $listener($data, $client);
            }
        }
        if ($client->room !== null) {
            $this->getRoomById($client->room)?->notifyListeners($event, $data, $client);
        }
        $client->notifyListeners($event, $data, $client);
    }

    /**
     * @inheritDoc
     */
    public function clearEvents(): void
    {
        $this->events = [];
    }

    /**
     * @param SocketClient $client
     * @param string $message
     * @return void
     */
    protected function process(SocketClient $client, string $message)
    {
    }

    /**
     * @param SocketClient $client
     * @return void
     */
    protected function connected(SocketClient $client)
    {
    }

    /**
     * @param SocketClient $client
     * @return void
     */
    protected function closed(SocketClient $client)
    {
    }

    /**
     * @param SocketClient $client
     * @return void
     */
    protected function connecting(SocketClient $client)
    {
    }

    /**
     * @param SocketClient $client
     * @param string $message
     * @return void
     */
    public function send(SocketClient $client, string $message): void
    {
        if ($client->handshake !== null) {
            $message = $this->frame($message, $client);
            $this->write($client, $message);
            return;
        }

        $holdingMessage = [
            'client'    => $client,
            'message' => $message
        ];
        $this->heldMessages[] = $holdingMessage;
    }

    /**
     * @param Socket $socket
     * @return void
     */
    protected function connect(Socket $socket): void
    {
        $client = new $this->clientClass($this, $socket, uniqid('u'));
        $this->clients[$client->id] = $client;
        $this->sockets[$client->id] = $socket;
        $this->connecting($client);
    }

    /**
     * @param Socket $socket
     * @param bool $triggerClosed
     * @param int|null $socketError
     * @return void
     */
    protected function disconnect(Socket $socket, bool $triggerClosed = true, ?int $socketError = null): void
    {
        $client = $this->getClientBySocket($socket);

        if ($client === null) {
            return;
        }

        if ($client->room !== null) {
            @$this->rooms[$client->room]?->kick($client);
        }

        unset($this->clients[$client->id]);
        unset($this->sockets[$client->id]);

        if (!is_null($socketError)) {
            socket_clear_error($socket);
        }

        if ($triggerClosed) {
            echo "Client $client->id disconnected" . PHP_EOL;
            $this->closed($client);
            socket_close($client->socket);
        } else {
            $message = $this->frame('', $client, MessageType::Close);
            $this->write($client, $message);
        }
    }

    /**
     * @param SocketClient $client
     * @param string $buffer
     * @return void
     */
    protected function doHandshake(SocketClient $client, string $buffer): void
    {
        $magicGUID = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11";
        $headers = [];

        $lines = explode("\n", $buffer);
        foreach ($lines as $line) {
            if (str_contains($line, ":")) {
                $header = explode(":", $line, 2);
                $headers[strtolower(trim($header[0]))] = trim($header[1]);
            } elseif (stripos($line, "get ") !== false) {
                preg_match("/GET (.*) HTTP/i", $buffer, $reqResource);
                $headers['get'] = trim($reqResource[1]);
            }
        }

        if (isset($headers['get'])) {
            $client->requestedResource = $headers['get'];
        } else {
            $handshakeResponse = "HTTP/1.1 405 Method Not Allowed\r\n\r\n";
        }

        if (!isset($headers['host']) ||
            (!isset($headers['upgrade']) || strtolower($headers['upgrade']) != 'websocket') ||
            (!isset($headers['connection']) || !str_contains(strtolower($headers['connection']), 'upgrade')) ||
            (!isset($headers['sec-websocket-key']))
        ) {
            $handshakeResponse = "HTTP/1.1 400 Bad Request";
        }

        if (!isset($headers['sec-websocket-version']) || strtolower($headers['sec-websocket-version']) != 13) {
            $handshakeResponse = "HTTP/1.1 426 Upgrade Required\r\nSec-WebSocketVersion: 13";
        }

        if (isset($handshakeResponse)) {
            $this->write($client, $handshakeResponse);
            $this->disconnect($client->socket);
            return;
        }

        $client->headers = $headers;
        $client->handshake = $buffer;

        $webSocketKeyHash = sha1($headers['sec-websocket-key'] . $magicGUID);

        $handshakeToken = "";
        for ($i = 0; $i < 20; $i++) {
            $handshakeToken .= chr(hexdec(substr($webSocketKeyHash, $i * 2, 2)));
        }
        $handshakeToken = base64_encode($handshakeToken) . "\r\n";

        $handshakeResponse = "HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: $handshakeToken\r\n";
        $this->write($client, $handshakeResponse);
        $this->connected($client);
    }

    /**
     * @param Socket $socket
     * @return SocketClient|null
     */
    public function getClientBySocket(Socket $socket): ?SocketClient
    {
        return array_values(array_filter($this->clients, fn($u) => $u->socket == $socket) + [null])[0];
    }

    /**
     * @param string $id
     * @return SocketClient|null
     */
    public function getClientById(string $id): ?SocketClient
    {
        return @$this->clients[$id] ?? null;
    }

    /**
     * @param string $id
     * @return SocketClient|null
     */
    public function getRoomById(string $id): ?SocketRoom
    {
        return @$this->rooms[$id] ?? null;
    }

    /**
     * @param string $message
     * @param SocketClient $client
     * @param MessageType $messageType
     * @param $messageContinues
     * @return string
     */
    protected function frame(string $message, SocketClient $client, MessageType $messageType = MessageType::Text, $messageContinues = false): string
    {
        switch ($messageType) {
            case MessageType::Continuous:
                $b1 = 0;
                break;
            case MessageType::Text:
                $b1 = ($client->sendingContinuous) ? 0 : 1;
                break;
            case MessageType::Bin:
                $b1 = ($client->sendingContinuous) ? 0 : 2;
                break;
            case MessageType::Close:
                $b1 = 8;
                break;
            case MessageType::Ping:
                $b1 = 9;
                break;
            case MessageType::Pong:
                $b1 = 10;
                break;
        }

        $client->sendingContinuous = $messageContinues;
        if (!$messageContinues) {
            $b1 += 128;
        }

        $length = strlen($message);
        $lengthField = "";
        if ($length >= 126) {

            $b2 = $length < 65536 ? 126 : 127;
            $hexLength = dechex($length);

            if (strlen($hexLength) % 2 == 1) {
                $hexLength = '0' . $hexLength;
            }
            $n = strlen($hexLength) - 2;

            for ($i = $n; $i >= 0; $i = $i - 2) {
                $lengthField = chr(hexdec(substr($hexLength, $i, 2))) . $lengthField;
            }
            while (strlen($lengthField) < ($b2 == 126 ? 2 : 8)) {
                $lengthField = chr(0) . $lengthField;
            }
        } else {
            $b2 = $length;
        }

        return chr($b1) . chr($b2) . $lengthField . $message;
    }

    /**
     * @param int $length
     * @param string $packet
     * @param SocketClient $client
     * @return void
     */
    protected function splitPacket(int $length, string $packet, SocketClient $client): void
    {
        if ($client->handlingPartialPacket) {
            $packet = $client->partialBuffer . $packet;
            $client->handlingPartialPacket = false;
            $length = strlen($packet);
        }

        $framePos = 0;

        while ($framePos < $length) {
            $headers = $this->extractHeaders($packet);
            $headersSize = $this->calcOffset($headers);

            $frameSize = $headers['length'] + $headersSize;

            $frame = substr($packet, $framePos, $frameSize);

            if (($message = $this->deFrame($frame, $client)) !== FALSE) {
                if ((preg_match('//u', $message)) || ($headers['opCode'] == 2)) {
                    $eventData = json_decode($message, true);
                    if ($eventData !== null && isset($eventData["event"])) {
                        $this->notifyListeners($eventData["event"], @$eventData["data"] ?? null, $client);
                    }
                    $this->process($client, $message);
                } else {
                    echo "ERROR: The message is not encoded with UTF-8" . PHP_EOL;
                }
            }

            $framePos += $frameSize;
            $packet = substr($packet, $framePos);
        }
    }

    /**
     * @param array $headers
     * @return int
     */
    protected function calcOffset(array $headers): int
    {
        $offset = $headers['hasMask'] ? 6 : 2;

        if ($headers['length'] > 65535) {
            $offset += 8;
        } elseif ($headers['length'] > 125) {
            $offset += 2;
        }

        return $offset;
    }

    /**
     * @param string $message
     * @param SocketClient $client
     * @return false|string
     */
    protected function deFrame(string $message, SocketClient $client): false|string
    {
        $headers = $this->extractHeaders($message);
        $pong = false;
        switch ($headers['opCode']) {
            case 9:
                $pong = true;
            case 0:
            case 1:
            case 2:
            case 10:
                break;
            case 8:
                $this->disconnect($this->sockets[$client->id]);
            default:
                return false;
        }

        if ($this->checkRSVBits($headers)) {
            return false;
        }

        $payload = $client->partialMessage . $this->extractPayload($message, $headers);

        if ($pong) {
            $reply = $this->frame($payload, $client, MessageType::Pong);
            $this->write($client, $reply);
            return false;
        }

        $payload = $this->applyMask($payload, $headers);

        if ($headers['length'] > strlen($payload)) {
            $client->handlingPartialPacket = true;
            $client->partialBuffer = $message;
            return false;
        }

        if ($headers['fin']) {
            $client->partialMessage = "";
            return $payload;
        }

        $client->partialMessage = $payload;
        return false;
    }

    /**
     * @param string $message
     * @return array
     */
    protected function extractHeaders(string $message): array
    {
        $header = [
            'fin'     => $message[0] & chr(128),
            'rsv1'    => $message[0] & chr(64),
            'rsv2'    => $message[0] & chr(32),
            'rsv3'    => $message[0] & chr(16),
            'opCode'  => ord($message[0]) & 15,
            'hasMask' => $message[1] & chr(128),
            'length'  => (ord($message[1]) >= 128) ? ord($message[1]) - 128 : ord($message[1]),
            'mask'    => ""
        ];

        if ($header['length'] == 126) {
            if ($header['hasMask']) {
                $header['mask'] = $message[4] . $message[5] . $message[6] . $message[7];
            }
            $header['length'] = ord($message[2]) * 256
                + ord($message[3]);
        } elseif ($header['length'] == 127) {
            if ($header['hasMask']) {
                $header['mask'] = $message[10] . $message[11] . $message[12] . $message[13];
            }
            $header['length'] = ord($message[2]) * 65536 * 65536 * 65536 * 256
                + ord($message[3]) * 65536 * 65536 * 65536
                + ord($message[4]) * 65536 * 65536 * 256
                + ord($message[5]) * 65536 * 65536
                + ord($message[6]) * 65536 * 256
                + ord($message[7]) * 65536
                + ord($message[8]) * 256
                + ord($message[9]);
        } elseif ($header['hasMask']) {
            $header['mask'] = $message[2] . $message[3] . $message[4] . $message[5];
        }

        return $header;
    }

    /**
     * @param string $message
     * @param array $headers
     * @return string
     */
    protected function extractPayload(string $message, array $headers): string
    {
        return substr($message, $this->calcOffset($headers));
    }

    /**
     * @param string $payload
     * @param array $headers
     * @return string
     */
    protected function applyMask(string $payload, array $headers): string
    {
        if (!$headers['hasMask']) {
            return $payload;
        }

        $mask = $headers['mask'];

        $preparedMask = str_repeat($mask, ceil(strlen($payload) / strlen($mask)));
        $preparedMask = substr($preparedMask, 0, strlen($payload));

        return $preparedMask ^ $payload;
    }

    /**
     * @param array $headers
     * @return bool
     */
    protected function checkRSVBits(array $headers): bool
    {
        return ord($headers['rsv1']) + ord($headers['rsv2']) + ord($headers['rsv3']) > 0;
    }

    /**
     * @param SocketClient $client
     * @param string $message
     * @return void
     */
    protected function write(SocketClient $client, string $message): void
    {
        @socket_write($client->socket, $message, strlen($message));
    }

}