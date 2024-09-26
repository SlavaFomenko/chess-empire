import axios from "axios";
import {SEND_FRIEND_REQUEST} from "../../config";

export const sendFriendRequest = async (userId,token) => {
    return await axios.post(SEND_FRIEND_REQUEST, {receiverId: userId}, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });
};