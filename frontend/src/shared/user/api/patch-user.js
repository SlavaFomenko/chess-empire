import axios from "axios";
import {PATCH_USER, UPLOAD_USER_PIC} from "../../config";

export const patchUser = async (userStore,data,token) => {
    return axios.patch(PATCH_USER(userStore.user.id), data, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};