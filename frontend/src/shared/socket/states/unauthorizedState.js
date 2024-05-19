import { showNotification } from "../../notification";
import { baseState } from "./baseState";
import { defaultState } from "./defaultState";

export const unauthorizedState = ({ socket, dispatch, history }) => {
  return {
    ...baseState({ socket, dispatch, history }),
    name: "unauthorized",
    auth_err: (data) => {
      dispatch(showNotification("Authentication failed while connecting to the server"));
    },
    auth_ok: (data) => {
      socket.id = data;
      socket.setState(defaultState, { dispatch, history });
    }
  };
};




