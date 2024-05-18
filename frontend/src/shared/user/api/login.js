import { LOGIN_URL } from "../../config";
import axios from "axios";

export const login = async (data) => {
  const response = await axios.post(LOGIN_URL, data);
  return response.data;
};