import { showNotification } from "../../notification";
import { baseState } from "./baseState";
import { defaultState } from "./defaultState";

export const inGameState = ({ socket, dispatch, history }) => {
  return {
    ...baseState({ socket, dispatch, history }),
    name: "inGame",
    game_update: (data) => {
      dispatch({
        type: "game/updateState",
        payload: data,
      });
    }
  };
};




