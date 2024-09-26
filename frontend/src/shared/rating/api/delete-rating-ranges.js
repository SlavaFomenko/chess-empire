import {DELETE_RATING_RANGE} from "../../config";
import axios from "axios";

export const deleteRatingRanges = async (id,token) => {
    return        axios.delete(DELETE_RATING_RANGE(id), {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};