import axios from "axios";
import {ACCEPT_FRIEND_REQUEST, } from "../../config";

export const acceptFriendRequest = async (user,token) => {
    return axios.post(ACCEPT_FRIEND_REQUEST(user.id), null, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};