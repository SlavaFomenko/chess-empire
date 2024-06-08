import { GET_ALL_GAMES_URL } from "../../config";
import axios from "axios";

export const getAllGames = async ({ page, search }) => {
  const params = { page };
  if (search && search.trim().length > 0) {
    params.name = search;
  }
  let response = { pagesCount: 0, games: [] };
  await axios.get(GET_ALL_GAMES_URL, {
    params,
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`
    }
  }).then(res => response = res.data).catch(error => {
    console.log(error);
  });
  return response;
};

