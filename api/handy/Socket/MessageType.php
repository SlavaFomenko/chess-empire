<?php

namespace Handy\Socket;

enum MessageType: string
{
    case Continuous = 'continuous';
    case Text = 'text';
    case Bin = 'binary';
    case Close = 'close';
    case Ping = 'ping';
    case Pong = 'pong';
}
