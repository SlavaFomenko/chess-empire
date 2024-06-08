export const HOST_URL = process.env.REACT_APP_BASE_URL;
export const API_URL = HOST_URL + '/api';
export const LOGIN_URL = API_URL + '/login-check';
export const REGISTRATION_URL = API_URL + '/users';
export const GET_USER_BY_ID = (id) => API_URL + `/users/${id}`;
export const GET_GAMES_FOR_USER = API_URL + '/games';
export const GET_GAME_BY_ID = (id) => API_URL + `/games/${id}`;
export const UPLOAD_USER_PIC = (id) => API_URL + `/users/${id}/pic`;
export const PATCH_USER = (id) => API_URL + `/users/${id}`;
export const DELETE_USER = (id) => API_URL + `/users/${id}`;
export const GET_RATING_RANGES = API_URL + '/rating-ranges';
export const POST_RATING_RANGE = API_URL + '/rating-ranges';
export const PATCH_RATING_RANGE = (id) => API_URL + `/rating-ranges/${id}`;
export const DELETE_RATING_RANGE = (id) => API_URL + `/rating-ranges/${id}`;
export const GET_ALL_USERS_URL = API_URL + '/users'
export const GET_ALL_GAMES_URL = API_URL + '/games'