import { showNotification } from "../../notification";
import { disconnectedState } from "./disconnectedState";
import { s } from "../actions/socket";

export const baseState = ({ socket, dispatch, history, getState }) => {
  return {
    name: "base",
    close: (data) => {
      window.onbeforeunload = null;
      socket.setState(disconnectedState, { dispatch, history, getState })
      dispatch(showNotification("Connection with server lost"));
    },
    update_devices: (data) => {
      dispatch(s.updateDevices(data))
    }
  };
};


