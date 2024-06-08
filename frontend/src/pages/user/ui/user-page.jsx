import React, { useEffect, useState } from "react";
import axios from "axios";
import styles from "../styles/user-page.module.scss";
import {
  GET_FRIEND_PAIR_BY_USER,
  GET_GAMES_FOR_USER,
  GET_USER_BY_ID,
  SEND_FRIEND_REQUEST
} from "../../../shared/config";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import { LayoutPage } from "../../../layouts/page-layout";
import { ProfileData } from "../../../entities/profile/profile-data/ui/profile-data";
import { showNotification } from "../../../shared/notification";
import { GamesList } from "../../../entities/profile";

export function UserPage () {
  const userStore = useSelector(state => state.user);
  const navigate = useNavigate();
  const [games, setGames] = useState({ loading: false, list: [], page: 0, lastPage: false, canLoad: false });
  const [error, setError] = useState(null);
  const [user, setUser] = useState(null);
  const dispatch = useDispatch();
  const [friendStatus, setFriendStatus] = useState(null);
  const socketState = useSelector(store => store.socket);

  const sendInvite = () => {
    if (!user) {
      return;
    }
    try {
      axios.post(SEND_FRIEND_REQUEST, { receiverId: user.id }, {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        window.location.reload();
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error sending friend request"));});
    } catch (error) {
      dispatch(showNotification("Error sending friend request"));
    }
  };

  const fetchGames = () => {
    if (!user || !userStore.user.token || games.lastPage || userStore.user.role !== "ROLE_ADMIN") {
      return;
    }
    try {
      setGames({ ...games, loading: true });
      axios.get(GET_GAMES_FOR_USER, {
        params: {
          page: games.page + 1,
          userId: user.id
        },
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setGames({
          loading: false,
          list: [...games.list, ...response.data.games],
          page: games.page + 1,
          lastPage: games.page + 1 >= response.data.pagesCount,
          canLoad: true
        });
      }).catch(error => dispatch(showNotification("Error fetching user games")));
    } catch (error) {
      dispatch(showNotification("Error fetching user games"));
    }
  };

  useEffect(() => {
    setError(null);
    if (!userStore.user?.token) {
      setError("You are not authorized");
      return;
    }

    let userId = window.location.pathname.replace("/user/", "");
    if (`${+userId}` !== `${userId}`) {
      setError("Invalid URL");
      return;
    }
    userId = +userId;
    if (userId === userStore.user.id) {
      navigate("/profile");
    }

    axios.get(GET_USER_BY_ID(userId), {
      headers: {
        Authorization: `Bearer ${userStore.user.token}`
      }
    }).then(response => {
      setUser(response.data?.user);
    }).catch(error => setError(error.response.data.message));
  }, [userStore.user]);

  const checkFriendStatus = () => {
    if (!user) {
      return;
    }

    axios.get(GET_FRIEND_PAIR_BY_USER(user.id), {
      headers: {
        Authorization: `Bearer ${userStore.user.token}`
      }
    }).then(response => {
      setFriendStatus(response.data.accepted === 1 ? "friend" : "pending");
    }).catch(error => setFriendStatus("not-friend"));
  };

  useEffect(() => {
    fetchGames();
    checkFriendStatus();
  }, [user]);

  return (
    <LayoutPage>
      <div className={styles.userPage}>
        {error && <h1 className={styles.errorMessage}>{error}</h1>}
        {!error && (user ? (
          <ProfileData user={user}>
            {["pending", "not-friend"].includes(friendStatus) &&
              <button
                disabled={friendStatus === "pending"}
                onClick={sendInvite}
              >
              Invite
            </button>}
            {friendStatus === "friend" &&
              <button
                disabled={socketState.state !== "default"}
                onClick={sendInvite}
              >
                Play
              </button>}
          </ProfileData>
        ) : (
          <p>Loading...</p>
        ))}
        <div className={styles.gamesHistory}>
          <GamesList
            games={games.list}
          />
          {games.canLoad && !games.lastPage && !games.loading && <button onClick={fetchGames}>Load more</button>}
          {games.loading && <p>Loading...</p>}
        </div>
      </div>
    </LayoutPage>
  );
}
