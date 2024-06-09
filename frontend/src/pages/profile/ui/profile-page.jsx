import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { LayoutPage } from "../../../layouts/page-layout";
import axios from "axios";
import {
  ACCEPT_FRIEND_REQUEST,
  GET_ALL_USERS_URL,
  GET_GAMES_FOR_USER,
  GET_USER_BY_ID,
  PATCH_USER, REMOVE_FRIEND,
  UPLOAD_USER_PIC
} from "../../../shared/config";
import styles from "../styles/profile.module.scss";
import { PlayDialog, GamesList, UsersList } from "../../../entities/profile";
import { showNotification } from "../../../shared/notification";
import editIcon from "../../../shared/images/icons/edit-icon.png";
import { EditProfileDialog } from "../../../entities/profile/edit-profile-dialog/ui/edit-profile-dialog";
import { ProfileData } from "../../../entities/profile/profile-data/ui/profile-data";
import { InviteFriendDialog } from "../../../entities/profile/invite-friend-dialog/ui/invite-friend-dialog";
import { useNavigate } from "react-router-dom";
import { BannerLayout } from "../../../layouts/banner-layout";
import { SearchGame } from "../../../features/search-game";
import { s } from "../../../shared/socket";
import { ChangePicDialog } from "../../../entities/profile/play-dialog/ui/change-pic-dialog";

export function ProfilePage () {
  const userStore = useSelector(state => state.user);
  const navigate = useNavigate();
  const [user, setUser] = useState(null);
  const [games, setGames] = useState({ loading: false, list: [], page: 0, lastPage: false });
  const [friends, setFriends] = useState({ loading: false, list: [], page: 0, lastPage: false });
  const [friendRequests, setFriendRequests] = useState({
    loading: false,
    count: 0,
    list: [],
    page: 0,
    lastPage: false
  });
  const [friendsTab, setFriendsTab] = useState("list");
  const [picForm, setPicForm] = useState({ opened: false, selectedFile: null });
  const [editForm, setEditForm] = useState({ opened: false, data: null });
  const [playForm, setPlayForm] = useState({ opened: false, friendId: null });
  const dispatch = useDispatch();
  const socketState = useSelector(store => store.socket);

  const fetchFriends = () => {
    if (!user || !userStore.user.token || friends.lastPage) {
      return;
    }
    try {
      setFriends({ ...friends, loading: true });
      axios.get(GET_ALL_USERS_URL, {
        params: {
          page: friends.page + 1,
          friend: true,
          orderBy: "id",
          desc: true
        },
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setFriends({
          loading: false,
          list: [...friends.list, ...response.data.users],
          page: friends.page + 1,
          lastPage: friends.page + 1 >= response.data.pagesCount
        });
      }).catch(error => dispatch(showNotification("Error fetching your friends")));
    } catch (error) {
      dispatch(showNotification("Error fetching your friends"));
    }
  };

  const fetchFriendRequests = () => {
    if (!user || !userStore.user.token || friendRequests.lastPage) {
      return;
    }
    try {
      setFriendRequests({ ...friendRequests, loading: true });
      axios.get(GET_ALL_USERS_URL, {
        params: {
          page: friendRequests.page + 1,
          request: true,
          orderBy: "id",
          desc: true
        },
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setFriendRequests({
          loading: false,
          count: response.data.count,
          list: [...friendRequests.list, ...response.data.users],
          page: friendRequests.page + 1,
          lastPage: friendRequests.page + 1 >= response.data.pagesCount
        });
      }).catch(error => dispatch(showNotification("Error fetching your friend requests")));
    } catch (error) {
      dispatch(showNotification("Error fetching your friend requests"));
    }
  };

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
          list: [...games.list, ...response.data.games],
          page: games.page + 1,
          lastPage: games.page + 1 >= response.data.pagesCount
        });
      }).catch(error => dispatch(showNotification("Error fetching your games")));
    } catch (error) {
      dispatch(showNotification("Error fetching your games"));
    }
  };

  const fetchUser = async () => {
    try {
      const response = await axios.get(GET_USER_BY_ID(userStore.user.id));
      setUser(response.data.user);
    } catch (error) {
      console.log(error);
      dispatch(showNotification("Error fetching your profile"));
    }
  };

  const uploadPic = async () => {
    if (!picForm.selectedFile) {
      return;
    }
    try {
      const formData = new FormData();
      formData.append("pic", picForm.selectedFile);
      axios.post(UPLOAD_USER_PIC(userStore.user.id), formData, {
        headers: {
          "content-type": "multipart/form-data",
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        window.location.reload();
      }).catch(error => dispatch(showNotification("Error uploading new pic")));
    } catch (error) {
      dispatch(showNotification("Error uploading new pic"));
    }
  };

  const patchProfile = async (data) => {
    try {
      axios.patch(PATCH_USER(userStore.user.id), data, {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        window.location.reload();
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error patching your profile!"));});
    } catch (error) {
      dispatch(showNotification("Error patching your profile!"));
    }
  };

  const acceptRequest = async (user) => {
    try {
      axios.post(ACCEPT_FRIEND_REQUEST(user.id), null, {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setFriends({
          ...friends,
          list: [...friends.list, user]
        });
        setFriendRequests({
          ...friendRequests,
          list: friendRequests.list.filter(u=>u.id !== user.id),
          count: friendRequests.count - 1
        });
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error accepting friend request"));});
    } catch (error) {
      dispatch(showNotification("Error accepting friend request"));
    }
  };

  const removeFriend = async (user) => {
    try {
      axios.delete(REMOVE_FRIEND(user.id), {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setFriends({
          ...friends,
          list: friends.list.filter(u=>u.id !== user.id)
        });
        setFriendRequests({
          ...friendRequests,
          list: friendRequests.list.filter(u=>u.id !== user.id),
          count: friendRequests.count - 1
        });
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error deleting friend pair"));});
    } catch (error) {
      dispatch(showNotification("Error deleting friend pair"));
    }
  };


  useEffect(() => {
    fetchGames();
    fetchFriends();
    fetchFriendRequests();
  }, [user]);

  useEffect(() => {
    if (userStore.user) {
      fetchUser();
    }
  }, [userStore]);

  const friendsTabs = {
    "list": <>
      <UsersList
        users={friends.list}
        onClick={(user)=>{navigate(`/user/${user.id}`)}}
        childrenCallback={(user) => <>
          <button onClick={(e)=>{e.stopPropagation(); setPlayForm({...playForm, opened: true, friendId: user.id})}} disabled={socketState.state !== "default"}>
            Play
          </button>
          <button onClick={(e)=>{e.stopPropagation(); removeFriend(user)}}>
            Remove
          </button>
        </>}
      />
      {!friends.lastPage && !friends.loading && <button onClick={fetchFriends}>Load more</button>}
      {friends.loading && <p>Loading...</p>}
      {friends.lastPage && !friends.loading && friends.list.length === 0 && <p>You have no friends :(</p>}
    </>,
    "requests": <>
      <UsersList
        users={friendRequests.list}
        childrenCallback={(user) => <>
          <button onClick={() => acceptRequest(user)}>
            Accept
          </button>
          <button onClick={() => removeFriend(user)}>
            Decline
          </button>
        </>}
      />
      {!friendRequests.lastPage && !friendRequests.loading && <button onClick={fetchFriendRequests}>Load more</button>}
      {friendRequests.loading && <p>Loading...</p>}
      {friendRequests.lastPage && !friendRequests.loading && friendRequests.list.length === 0 &&
        <p>You have no friend requests</p>}
    </>
  };

  return (
    <LayoutPage>
      <div className={styles.profilePage}>
        {user ? (
          <ProfileData
            user={user} greetingMessage={(username) => `Hi, ${username}!`} onImageEdit={() => setPicForm({
            ...picForm,
            opened: true
          })}
          >
            <img
              className={styles.editProfileButton} src={editIcon} alt="Edit profile" onClick={() => setEditForm({
              ...editForm,
              opened: true,
              data: user
            })}
            />
          </ProfileData>
        ) : (
          <p>Loading...</p>
        )}
        <div className={styles.panels}>
          <div>
            <h2>Games history:</h2>
            <div className={styles.gamesHistory}>
              <GamesList
                games={games.list}
                user={user}
              />
              {!games.lastPage && !games.loading && <button onClick={fetchGames}>Load more</button>}
              {games.loading && <p>Loading...</p>}
              {games.lastPage && !games.loading && games.list.length === 0 && <p>You haven't played any games yet</p>}
            </div>
          </div>
          <div>
            <h2>Friends:</h2>
            <div className={styles.friendsTabs}>
              <button disabled={friendsTab === "list"} onClick={e => setFriendsTab("list")}>List</button>
              <button disabled={friendsTab === "requests"} onClick={e => setFriendsTab("requests")}>Requests {friendRequests.count > 0 && `(${friendRequests.count})`}</button>
              <button onClick={e => setFriendsTab("invite")}>Invite</button>
            </div>
            <div className={styles.friendsList}>
              {friendsTabs[friendsTab]}
            </div>
          </div>
        </div>
      </div>
      {picForm.opened &&
        <ChangePicDialog state={picForm} setState={setPicForm} onSubmit={() => {uploadPic();}} onDelete={() => {patchProfile({ profilePic: "REMOVE" });}} />}
      {editForm.opened &&
        <EditProfileDialog state={editForm} setState={setEditForm} onSubmit={(data) => {patchProfile(data);}} />}
      {friendsTab === "invite" &&
         <InviteFriendDialog onClose={e=>setFriendsTab("list")}/>}
      {}
      {playForm.opened && playForm.friendId && <BannerLayout onClick={()=>setPlayForm({...playForm, opened: false})}>
        <div className={styles.playFriendForm} onClick={e=>e.stopPropagation()}>
          <SearchGame onSubmit={(data)=>{
            dispatch(s.playFriend({ ...data, friendId: playForm.friendId}))
            setPlayForm({...playForm, opened: false});
          }}>
            {socketState.state === "default" && <button type="submit">Invite</button>}
          </SearchGame>
        </div>
      </BannerLayout>}
    </LayoutPage>
  );
}
