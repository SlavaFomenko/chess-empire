import axios from "axios";
import {GET_LEADERBOARD} from "../../config";


export const getLeaderboard = async () => {
    return  axios.get(GET_LEADERBOARD)
};
