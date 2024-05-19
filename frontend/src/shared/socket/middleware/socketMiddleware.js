import { socket } from "../socket";
import { showNotification } from "../../notification";
import { unauthorizedState } from "../states/unauthorizedState";

export const socketMiddleware = (params) => (next) => (action) => {
  const { dispatch, getState } = params;
  const { type } = action;
  const [storeName, actionName] = type?.split("/") ?? [null, null];

  if (storeName !== "socket") {
    return next(action);
  }

  switch (actionName) {
    case "connect":
      socket.initialize(process.env.REACT_APP_SOCKET_HOST).then(r => {
        socket.setState(unauthorizedState, { dispatch });
        socket.emit("auth", localStorage.getItem("token"));
      }).catch(() => {
        dispatch(showNotification("Cannot establish connection with server"));
      });
      break;
    case "disconnect":
      socket.close();
      break;
    case "on":
      socket.on(action.payload.event, action.payload.callback);
      break;
    default:
      break;
  }

  return next(action);
};