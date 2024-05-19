import { showNotification } from "../../notification";
import { disconnectedState } from "./disconnectedState";

export const baseState = ({ socket, dispatch, history }) => {
  return {
    name: "base",
    close: (data) => {
      socket.setState(disconnectedState, {dispatch})
      dispatch(showNotification("Connection with server lost"));
    }
  };
};


