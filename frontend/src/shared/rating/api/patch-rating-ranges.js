import {PATCH_RATING_RANGE} from "../../config";
import axios from "axios";

export const patchRatingRanges = async (editState,data,token) => {
    return       axios.patch(PATCH_RATING_RANGE(editState.id), data, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};