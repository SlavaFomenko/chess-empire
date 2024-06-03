export const HOST_URL = process.env.REACT_APP_BASE_URL;
export const API_URL = HOST_URL + '/api';
export const LOGIN_URL = API_URL + '/login-check';
export const REGISTRATION_URL = API_URL + '/users';
export const GET_USER_BY_ID_URL = API_URL + '/users';
export const GET_GAMES_FOR_USER = API_URL + '/games';
export const GET_GAME_BY_ID = (id) => API_URL + `/games/${id}`;
export const UPLOAD_USER_PIC = (id) => API_URL + `/users/${id}/pic`;