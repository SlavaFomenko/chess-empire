import { showNotification } from "../../notification";
import { disconnectedState } from "./disconnectedState";

export const baseState = ({ socket, dispatch, history, getState }) => {
  return {
    name: "base",
    close: (data) => {
      socket.setState(disconnectedState, { dispatch, history, getState })
      dispatch(showNotification("Connection with server lost"));
    }
  };
};


