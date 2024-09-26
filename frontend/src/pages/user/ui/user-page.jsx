import React, { useEffect, useState } from "react";
import styles from "../styles/user-page.module.scss";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import { PageLayout } from "../../../layouts/page-layout";
import { ProfileData } from "../../../entities/profile/profile-data/ui/profile-data";
import { showNotification } from "../../../shared/notification";
import { GamesList } from "../../../entities/profile";
import { BannerLayout } from "../../../layouts/banner-layout";
import { SearchGame } from "../../../features/search-game";
import { s } from "../../../shared/socket";
import {sendFriendRequest} from "../../../shared/friend/api/send-friend-request";
import {getGamesForUser} from "../../../shared/friend/api/get-games-for-user";
import {getUserById} from "../../../shared/user/api/get-user-by-id";
import {getFriendPairByUser} from "../../../shared/friend/api/get-friend-pair-by-user";

export function UserPage () {
  const userStore = useSelector(state => state.user);
  const navigate = useNavigate();
  const [games, setGames] = useState({ loading: false, list: [], page: 0, lastPage: false, canLoad: false });
  const [error, setError] = useState(null);
  const [user, setUser] = useState(null);
  const dispatch = useDispatch();
  const [friendStatus, setFriendStatus] = useState(null);
  const socketState = useSelector(store => store.socket);
  const [playForm, setPlayForm] = useState({ opened: false, friendId: null });

  const sendInvite = () => {
    if (!user) {
      return;
    }
    try {
        sendFriendRequest(user.id,userStore.user.token)
            .then(response => {
             window.location.reload();
            })
            .catch(error => {
              dispatch(showNotification(error.response?.data?.message || "Error sending friend request"));
            });
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
          getGamesForUser(games,user,userStore.user.token)
              .then(response => {
                setGames({
                  loading: false,
                  list: [...games.list, ...response.data.games],
                  page: games.page + 1,
                  lastPage: games.page + 1 >= response.data.pagesCount,
                  canLoad: true
                });
              })
              .catch(error => dispatch(showNotification("Error fetching user games")));
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

    getUserById(userId,userStore.user.token)
        .then(response => {
          setUser(response.data?.user);
        })
        .catch(error => setError(error.response.data.message));
  }, [userStore.user]);

  const checkFriendStatus = () => {
    if (!user) {
      return;
    }
      getFriendPairByUser(user,userStore.user.token)
          .then(response => {
            setFriendStatus(response.data.accepted === 1 ? "friend" : "pending");
          })
          .catch(error => setFriendStatus("not-friend"));
  };

  useEffect(() => {
    fetchGames();
    checkFriendStatus();
  }, [user]);

  return (
    <PageLayout>
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
                onClick={(e)=>{e.stopPropagation(); setPlayForm({...playForm, opened: true, friendId: user.id})}}
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
    </PageLayout>
  );
}
