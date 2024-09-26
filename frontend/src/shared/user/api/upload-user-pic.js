import axios from "axios";
import {UPLOAD_USER_PIC} from "../../config";

export const uploadUserPic = async (userStore,formData,token) => {
    return axios.post(UPLOAD_USER_PIC(userStore.user.id), formData, {
        headers: {
            "content-type": "multipart/form-data",
            Authorization: `Bearer ${token}`
        }
    })
};