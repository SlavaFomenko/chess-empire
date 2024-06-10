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
      socket.initialize("wss://" + window.location.host + "/ws-server").then(r => {
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
    case "cancelSearchGame":
      socket.emit("cancel_random", action.payload);
      socket.setState(defaultState, { dispatch, history, getState });
      break;
    case "playFriend":
      socket.emit("play_friend", action.payload);
      break;
    case "gameAccept":
      socket.emit("game_accept", action.payload);
      break;
    case "gameReject":
      socket.emit("game_reject", action.payload);
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