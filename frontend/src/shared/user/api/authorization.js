import { LOGIN_URL } from "../../config";
import axios from "axios";

export const authorization = async (data) => {
  try {
    const response = await axios.post(LOGIN_URL, data);
    return response.data;
  } catch (err) {
    if (err.response && err.response.status === 401) {
      throw err;
    }
  }
};