import { baseState } from "./baseState";
import { inGameState } from "./inGameState";

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
  };
};




