import axios from "axios";
import {GET_GAMES_FOR_USER} from "../../config";

export const getGamesForUser = async (games,user,token) => {
    return axios.get(GET_GAMES_FOR_USER, {
        params: {
            page: games.page + 1,
            userId: user.id
        },
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};