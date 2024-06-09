import { cordsToTurn } from "../../game/lib";

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

const turn = (data) => {
  return {
    type: "socket/turn",
    payload: cordsToTurn(data)
  };
};

const searchGame = (data) => {
  return {
    type: "socket/searchGame",
    payload: data
  };
};

const cancelSearchGame = (data) => {
  return {
    type: "socket/cancelSearchGame",
    payload: data
  };
};

const playFriend = (data) => {
  return {
    type: "socket/playFriend",
    payload: data
  };
};

const gameAccept = (data) => {
  return {
    type: "socket/gameAccept",
    payload: data
  };
};

const gameReject = (data) => {
  return {
    type: "socket/gameReject",
    payload: data
  };
};

const resign = () => {
  return {
    type: "socket/resign"
  };
};

const closeEndGame = () => {
  return {
    type: "socket/closeEndGame"
  };
};

export const s = {
  connect: connect,
  disconnect: disconnect,
  on: on,
  searchGame: searchGame,
  cancelSearchGame: cancelSearchGame,
  playFriend: playFriend,
  gameAccept: gameAccept,
  gameReject: gameReject,
  turn: turn,
  resign: resign,
  closeEndGame: closeEndGame
}