import { showNotification } from "../../notification";
import { baseState } from "./baseState";
import { defaultState } from "./defaultState";

export const unauthorizedState = ({ socket, dispatch }) => {
  return {
    ...baseState({ socket, dispatch }),
    auth_err: (data) => {
      dispatch(showNotification("Authentication failed while connecting to the server"));
    },
    auth_ok: (data) => {
      socket.id = data;
      socket.setState(defaultState, { dispatch });
    }
  };
};




