import axios from "axios";
import {GET_ALL_USERS_URL} from "../../config";

export const fetchUsers = async (params) => {
     return await axios.get(GET_ALL_USERS_URL, {
        params,
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
        }});
};