import {GET_RATING_RANGES} from "../../config";
import axios from "axios";

export const getRatingRanges = async (token) => {
    return        axios.get(GET_RATING_RANGES, {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
};