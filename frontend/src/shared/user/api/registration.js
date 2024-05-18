import { REGISTRATION_URL } from "../../config";
import axios from "axios";

export const registration = async (data) => {
  return await axios.post(REGISTRATION_URL, data);
};