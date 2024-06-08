import React from "react";
import styles from "../styles/profile-data.module.scss";
import classNames from "classnames";
import { useNavigate } from "react-router-dom";
import defaultProfilePic from "../../../../shared/images/icons/defaultProfilePic.png";
import { HOST_URL } from "../../../../shared/config";
import editIcon from "../../../../shared/images/icons/edit-icon.png";

export function ProfileData ({ user, onImageEdit = null, children, greetingMessage = (username) => `${username}` }) {
  // const navigate = useNavigate();
  //
  // const formatDate = (date) => {
  //   const pad = (number) => number.toString().padStart(2, "0");
  //
  //   const day = pad(date.getDate());
  //   const month = pad(date.getMonth() + 1);
  //   const year = date.getFullYear();
  //   const hours = pad(date.getHours());
  //   const minutes = pad(date.getMinutes());
  //
  //   return `${day}.${month}.${year} ${hours}:${minutes}`;
  // };
  //
  // const color = user?.id === gameData.white_id ? "w" : user?.id === gameData.black_id ? "b" : "-";
  // const result = gameData.winner === "t" ? "Tie" : color === gameData.winner ? "Won" : "Lost";
  // const formattedDate = formatDate(new Date(gameData.playedDate * 1000));
  //
  // const rating = {
  //   white: gameData.white_rating,
  //   white_change: gameData.white_rating_change,
  //   white_class: gameData.white_rating_change > 0 ? styles.resultGreen : styles.resultRed,
  //   black: gameData.black_rating,
  //   black_change: gameData.black_rating_change,
  //   black_class: gameData.black_rating_change > 0 ? styles.resultGreen : styles.resultRed
  // }

  return (
    <div className={styles.profileData}>
      <div className={styles.profilePicDiv}>
        <img src={user.profilePic === "" ? defaultProfilePic : `${HOST_URL}/${user.profilePic}`} onError={e => e.target.src = defaultProfilePic} alt="Profile pic"/>
        {onImageEdit && <button onClick={onImageEdit}>Change</button>}
      </div>
      <div>
        <div className={styles.usernameBar}>
          <h1>{greetingMessage(user.username)}</h1>
          {children}
        </div>
        <p className={styles.aka}>Also known as {user.firstName} {user.lastName}</p>
        <p>Email: {user.email}</p>
        <p>Rating: {user.rating} {user.ratingTitle && `(${user.ratingTitle})`}</p>
      </div>
    </div>
  );
}
