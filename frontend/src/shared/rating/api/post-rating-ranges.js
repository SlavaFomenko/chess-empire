import {GET_RATING_RANGES, POST_RATING_RANGE} from "../../config";
import axios from "axios";

export const postRatingRanges = async (newRange,token) => {
    return         axios.post(POST_RATING_RANGE, newRange, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};