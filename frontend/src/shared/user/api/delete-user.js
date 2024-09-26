import axios from "axios";
import {DELETE_USER, REMOVE_FRIEND} from "../../config";


export const deleteUser = async (id,token) => {
    return axios.delete(DELETE_USER(id), {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};