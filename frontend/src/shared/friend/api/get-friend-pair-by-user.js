import axios from "axios";
import {GET_FRIEND_PAIR_BY_USER} from "../../config";

export const getFriendPairByUser = async (user,token) => {
    return axios.get(GET_FRIEND_PAIR_BY_USER(user.id), {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};