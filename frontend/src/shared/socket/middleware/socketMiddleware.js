import { socket } from "../socket";
import { showNotification } from "../../notification";
import { unauthorizedState } from "../states/unauthorizedState";
import { history } from "../../routing";
import { searchingGameState } from "../states/searchingGameState";
import { defaultState } from "../states/defaultState";

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
        socket.setState(unauthorizedState, { dispatch, history, getState });
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
    case "searchGame":
      socket.emit("play_random", action.payload);
      socket.setState(searchingGameState, { dispatch, history, getState });
      break;
    case "turn":
      socket.emit("turn", action.payload);
      break;
    case "resign":
      socket.emit("resign");
      break;
    case "closeEndGame":
      dispatch({
        type: "game/reset"
      });
      socket.setState(defaultState, { dispatch, history, getState });
      break;
    default:
      break;
  }

  return next(action);
};