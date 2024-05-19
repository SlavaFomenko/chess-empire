import { showNotification } from "../../notification";
import { s } from "../actions/socket";

export const disconnectedState = ({ socket, dispatch, history }) => {
  return {
    name: "disconnected"
  };
};


