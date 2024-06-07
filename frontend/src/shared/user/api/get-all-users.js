import { GET_ALL_USERS_URL } from "../../config";
import axios from "axios";

export const getAllUsers = async ({ name, page = 1, limit = 10 }) => {

  const response = await axios.get(GET_ALL_USERS_URL, {
    params: {
      name,
      page,
      limit,
    },
  });
  return response.data;
};
