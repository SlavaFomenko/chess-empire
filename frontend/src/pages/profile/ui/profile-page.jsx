import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { LayoutPage } from "../../../layouts/page-layout";
import axios from "axios";
import { GET_GAMES_FOR_USER, GET_USER_BY_ID_URL } from "../../../shared/config";
import { useNavigate } from "react-router-dom";
import styles from "../styles/profile.module.scss";
import { GamesList } from "../../../entities/profile";
import { showNotification } from "../../../shared/notification";

export function ProfilePage () {
  const userStore = useSelector(state => state.user);
  const [user, setUser] = useState(null);
  const [games, setGames] = useState({loading: false, list: [], page: 0, lastPage: false})
  const navigate = useNavigate();
  const dispatch = useDispatch();

  const fetchGames = () => {
    if(!user || !userStore.user.token || games.lastPage){
      return;
    }
    try {
      setGames({...games, loading: true})
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
      });
    }
    catch (error) {
      dispatch(showNotification("Error fetching your games"))
    }
  };

  useEffect(() => {
    fetchGames()
  }, [user]);

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const response = await axios.get(GET_USER_BY_ID_URL + "/" + userStore.user.id);
        setUser(response.data.user);
      } catch (error) {
        dispatch(showNotification("Error fetching your profile"));
      }
    };
    if (userStore.user) {
      fetchUser();
    } else {
      navigate("/");
    }
  }, [userStore]);

  return (
    <LayoutPage>
      <div className={styles.profilePage}>
        {user ? (
          <div>
            <h1>Hi, {user?.username}!</h1>
            <p className={styles.aka}>Also known as {user.firstName} {user.lastName}</p>
            <p>Email: {user.email}</p>
            <p>Rating: {user.rating}</p>
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
    </LayoutPage>
  );
}
