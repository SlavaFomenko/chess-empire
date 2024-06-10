import React, { useEffect, useState } from "react";
import styles from "../styles/home.module.scss";
import { LayoutPage } from "../../../layouts/page-layout";
import axios from "axios";
import { GET_LEADERBOARD } from "../../../shared/config";
import { showNotification } from "../../../shared/notification";
import { useDispatch } from "react-redux";
import { UserCard } from "../../../entities/profile/user-card/ui/user-card";
import { useNavigate } from "react-router-dom";

export function HomePage () {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const [leaderboard, setLeaderboard] = useState([]);

  useEffect(() => {
    try {
      axios.get(GET_LEADERBOARD).then(response => {
        setLeaderboard(response.data);
      }).catch(error => dispatch(showNotification("Error fetching leaderboard")));
    } catch (error) {
      dispatch(showNotification("Error fetching leaderboard"));
    }
  }, []);

  return (
    <LayoutPage>
      <div className={styles.homePage}>
        <div className={styles.greetingsDiv}>
          <h1>Welcome to Chess Empire!</h1>
          <br/>
          <p>
            Your ultimate destination for all things chess in Ukraine!
          </p>
          <br/>
          <p>
             Whether you're a seasoned grandmaster or a curious beginner,
            Chess Empire offers you comprehensive range of features designed to enhance your chess experience, track your progress,
            organize your local tournaments and simply have fun with your friends.
          </p>
          <br/>
          <p>
            At Chess Empire, we believe in making chess
            accessible and enjoyable for everyone, fostering a love for the game that transcends generations. Enjoy!
          </p>
        </div>
        <div className={styles.leaderBoard}>
          <h2>Our best players:</h2>
          {leaderboard && leaderboard.map((player, idx) => <div className={styles.leaderBoardUnit}>
              <h1 className={styles.place}>{idx + 1}</h1>
              <UserCard user={player} onClick={e => navigate(`/user/${player.id}`)} />
            </div>
          )}
        </div>
      </div>
    </LayoutPage>
  );
}