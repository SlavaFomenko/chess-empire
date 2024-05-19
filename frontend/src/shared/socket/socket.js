class Socket {
  id;
  host;
  state;
  webSocket;
  listeners;
  pingInterval;

  constructor (host = "") {
    this.host = host;
    this.webSocket = null;
    this.listeners = {};
    this.pingInterval = null;
  }

  initialize (host = this.host) {
    this.host = host;
    return new Promise((resolve, reject) => {
      this.webSocket = new WebSocket(host);
      this.webSocket.onopen = () => {
        this.notifyListeners("open");
        this.pingInterval = setInterval(() => this.ping(), 10000);
        this.webSocket.addEventListener("message", (e) => {
          const msg = JSON.parse(e.data);
          if (Object.hasOwn(msg, "event")) {
            this.notifyListeners(msg.event, msg.data);
          }
        });
        this.webSocket.addEventListener("close", () => {
          this.notifyListeners("close");
          clearInterval(this.pingInterval);
          this.clearListeners();
        });
        resolve(this);
      };
      this.webSocket.onerror = (err) => {
        this.notifyListeners("error");
        reject(err);
      };
    });
  }

  close () {
    this.webSocket?.close();
  }

  emit (event, data = null) {
    this.webSocket?.send(JSON.stringify({ event: event, data: data }));
  }

  ping () {
    this.emit("ping");
  }

  on (event, cb) {
    this.listeners[event] = [...(this.listeners[event] ?? []), cb];
  }

  removeListener (event, cb) {
    this.listeners[event] = this.listeners[event]?.filter(listener => listener != cb);
  }

  removeAllListeners (event) {
    delete this.listeners[event];
  }

  clearListeners () {
    this.listeners = {};
  }

  setState (state, props) {
    this.state = state({...props, socket: this})
  }

  notifyListeners (event, data = null) {
    const stateListener = this.state && this.state[event];
    stateListener && stateListener(data);
    this.listeners[event]?.forEach(listener => listener(data));
  }
}

let socket = new Socket();

module.exports.socket = socket;