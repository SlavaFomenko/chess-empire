import axios from "axios";
import {REMOVE_FRIEND} from "../../config";


export const removeFriend = async (user,token) => {
    return axios.delete(REMOVE_FRIEND(user.id), {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};