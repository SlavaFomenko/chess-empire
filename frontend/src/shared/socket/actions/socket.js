const connect = () => {
  return {
    type: "socket/connect"
  };
};

const disconnect = () => {
  return {
    type: "socket/disconnect"
  };
};

const on = (data) => {
  return {
    type: "socket/on",
    payload: { data }
  };
};

export const s = {
  connect: connect,
  disconnect: disconnect,
  on: on,
}