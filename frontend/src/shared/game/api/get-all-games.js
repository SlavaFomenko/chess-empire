import { GET_ALL_GAMES_URL } from "../../config";
import axios from "axios";

export const getAllGames = async ({ page, search }) => {
  const response = await axios.get(GET_ALL_GAMES_URL, {
    params: { page, name: search },
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`
    }
  });
  return response.data;
};

