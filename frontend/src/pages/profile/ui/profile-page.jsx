import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { LayoutPage } from "../../../layouts/page-layout";
import axios from "axios";
import { GET_GAMES_FOR_USER, GET_USER_BY_ID_URL, HOST_URL, PATCH_USER, UPLOAD_USER_PIC } from "../../../shared/config";
import styles from "../styles/profile.module.scss";
import { ChangePicDialog, GamesList } from "../../../entities/profile";
import { showNotification } from "../../../shared/notification";
import defaultProfilePic from "../../../shared/images/icons/defaultProfilePic.png";
import editIcon from "../styles/icons/edit-icon.png"
import { EditProfileDialog } from "../../../entities/profile/edit-profile-dialog/ui/edit-profile-dialog";

export function ProfilePage () {
  const userStore = useSelector(state => state.user);
  const [user, setUser] = useState(null);
  const [games, setGames] = useState({ loading: false, list: [], page: 0, lastPage: false });
  const [picForm, setPicForm] = useState({opened: false, selectedFile: null})
  const [editForm, setEditForm] = useState({opened: false, data: null})
  const dispatch = useDispatch();

  const fetchGames = () => {
    if (!user || !userStore.user.token || games.lastPage) {
      return;
    }
    try {
      setGames({ ...games, loading: true });
      axios.get(GET_GAMES_FOR_USER, {
        params: {
          page: games.page + 1,
          userId: userStore.user.id
        },
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setGames({
          loading: false,
          list: [...games.list, ...response.data],
          page: games.page + 1,
          lastPage: response.data.length < 20
        });
      }).catch(error => dispatch(showNotification("Error fetching your games")));
    } catch (error) {
      dispatch(showNotification("Error fetching your games"));
    }
  };

  const fetchUser = async () => {
    try {
      const response = await axios.get(GET_USER_BY_ID_URL + "/" + userStore.user.id);
      setUser(response.data.user);
    } catch (error) {
      console.log(error);
      dispatch(showNotification("Error fetching your profile"));
    }
  };

  const uploadPic = async () => {
    if(!picForm.selectedFile){
      return;
    }
    try {
      const formData = new FormData();
      formData.append('pic',picForm.selectedFile)
      axios.post(UPLOAD_USER_PIC(userStore.user.id), formData, {
        headers: {
          'content-type': 'multipart/form-data',
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        window.location.reload();
      }).catch(error => dispatch(showNotification("Error uploading new pic")));
    } catch (error) {
      dispatch(showNotification("Error uploading new pic"));
    }
  }

  const patchProfile = async (data) => {
    try {
      axios.patch(PATCH_USER(userStore.user.id), data, {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        window.location.reload();
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error patching your profile!"))});
    } catch (error) {
      dispatch(showNotification("Error patching your profile!"));
    }
  }

  useEffect(() => {
    fetchGames();
  }, [user]);

  useEffect(() => {
    if (userStore.user) {
      fetchUser();
    }
  }, [userStore]);

  return (
    <LayoutPage>
      <div className={styles.profilePage}>
        {user ? (
          <div className={styles.profileData}>
            <div className={styles.profilePicDiv}>
              <img src={user.profilePic === "" ? defaultProfilePic : `${HOST_URL}/${user.profilePic}`} onError={e => e.target.src = defaultProfilePic} alt="Profile pic"/>
              <button onClick={()=>setPicForm({...picForm, opened: true})}>Change</button>
            </div>
            <div>
              <div className={styles.usernameBar}>
                <h1>Hi, {user.username}!</h1>
                <img className={styles.editProfileButton} src={editIcon} alt="Edit profile" onClick={()=>setEditForm({...editForm, opened: true, data: user})}/>
              </div>
              <p className={styles.aka}>Also known as {user.firstName} {user.lastName}</p>
              <p>Email: {user.email}</p>
              <p>Rating: {user.rating} {user.ratingTitle && `(${user.ratingTitle})`}</p>
            </div>
          </div>
        ) : (
          <p>Loading...</p>
        )}
        <h2>Games history:</h2>
        <div className={styles.gamesHistory}>
          <GamesList
            games={games.list}
          />
          {!games.lastPage && !games.loading && <button onClick={fetchGames}>Load more</button>}
          {games.loading && <p>Loading...</p>}
        </div>
      </div>
      {picForm.opened && <ChangePicDialog state={picForm} setState={setPicForm} onSubmit={()=>{uploadPic()}} onDelete={()=>{patchProfile({profilePic: "REMOVE"})}}/>}
      {editForm.opened && <EditProfileDialog state={editForm} setState={setEditForm} onSubmit={(data)=>{patchProfile(data)}}/>}
    </LayoutPage>
  );
}
