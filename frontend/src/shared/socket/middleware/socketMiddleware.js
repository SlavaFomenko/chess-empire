import { socket } from "../socket";
import { showNotification } from "../../notification";
import { unauthorizedState } from "../states/unauthorizedState";
import { history } from "../../routing";
import { searchingGameState } from "../states/searchingGameState";
import { defaultState } from "../states/defaultState";
import { HOST_URL } from "../../config";

const getDeviceName = () => {
  const userAgent = navigator?.userAgent ?? "";
  let browserName, deviceName;

  switch (true) {
    case userAgent.indexOf("Firefox") > -1:
      browserName = "Mozilla Firefox";
      break;
    case userAgent.indexOf("Opera") > -1 || userAgent.indexOf("OPR") > -1:
      browserName = "Opera";
      break;
    case userAgent.indexOf("Trident") > -1:
      browserName = "Microsoft Internet Explorer";
      break;
    case userAgent.indexOf("Edge") > -1:
      browserName = "Microsoft Edge";
      break;
    case userAgent.indexOf("Chrome") > -1:
      browserName = "Google Chrome";
      break;
    case userAgent.indexOf("Safari") > -1:
      browserName = "Apple Safari";
      break;
    default:
      browserName = "Unknown Browser";
      break;
  }

  switch (true) {
    case userAgent.indexOf("Windows") > -1:
      deviceName = "Windows";
      break;
    case userAgent.indexOf("Android") > -1:
      deviceName = "Android";
      break;
    case userAgent.indexOf("Macintosh") > -1 || userAgent.indexOf("OPR") > -1:
      deviceName = "MacOS";
      break;
    case userAgent.indexOf("iPhone") > -1:
      deviceName = "iPhone";
      break;
    case userAgent.indexOf("iPad") > -1:
      deviceName = "iPad";
      break;
    case userAgent.indexOf("iPod") > -1:
      deviceName = "iPod";
      break;
    case userAgent.indexOf("Linux") > -1:
      deviceName = "Linux";
      break;
    default:
      deviceName = "Unknown Device";
      break;
  }

  return `${deviceName}, ${browserName}`;
};

export const socketMiddleware = (params) => (next) => (action) => {
  const { dispatch, getState } = params;
  const { type } = action;
  const [storeName, actionName] = type?.split("/") ?? [null, null];

  if (storeName !== "socket") {
    return next(action);
  }

  switch (actionName) {
    case "connect":
      socket.initialize(`wss://${HOST_URL}/ws-server`).then(r => {
        socket.setState(unauthorizedState, { dispatch, history, getState });
        socket.emit("auth", { token: localStorage.getItem("token"), deviceName: getDeviceName() });
      }).catch((e) => {
        console.log(e);
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
    case "transferGame":
      socket.emit("transfer_game", action.payload);
      break;
    default:
      break;
  }

  return next(action);
};