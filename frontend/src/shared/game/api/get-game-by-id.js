import axios from "axios";
import {GET_GAME_BY_ID} from "../../config";


export const getGameById = async (gameID,token) => {
    return axios.get(GET_GAME_BY_ID(gameID), {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};
