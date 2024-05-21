import { baseState } from "./baseState";
import { inGameState } from "./inGameState";
import { showNotification } from "../../notification";
import { defaultState } from "./defaultState";

export const searchingGameState = ({ socket, dispatch, history, getState }) => {
  return {
    ...baseState({ socket, dispatch, history, getState }),
    name: "searchingGame",
    game_update: (data) => {
      dispatch({
        type: "game/updateState",
        payload: data
      });
    },
    game_join: (data) => {
      history.push("/game");
      const { id } = getState().user.user;
      const { black, white } = data;
      let myColor = null;
      if (id === black.id) {
        myColor = "black";
      } else if (id === white.id) {
        myColor = "white";
      }
      socket.setState(inGameState, { dispatch, history, getState });

      dispatch({
        type: "game/updateState",
        payload: { ...data, myColor: myColor }
      });
    }
    ,
    play_random_err: (data) => {
      dispatch(showNotification("Oops! Seems like you're playing or searching the game from another device. Are you okay?"));
      socket.setState(defaultState, { dispatch, history, getState });
    }
  };
};




