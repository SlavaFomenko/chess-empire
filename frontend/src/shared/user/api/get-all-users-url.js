import {GET_ALL_USERS_URL} from "../../config";
import axios from "axios";

export const getAllUsersUrl = async (friend,token) => {
   return await axios.get(GET_ALL_USERS_URL, {
       params: {
           page: friend.page + 1,
           request: true,
           orderBy: "id",
           desc: true
       },
       headers: {
           Authorization: `Bearer ${token}`
       }
   })
};