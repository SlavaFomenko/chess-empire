import React from "react";
import styles from "../styles/profile-data.module.scss";
import defaultProfilePic from "../../../../shared/images/icons/defaultProfilePic.png";
import { HOST_URL } from "../../../../shared/config";

export function ProfileData ({ user, onImageEdit = null, children, greetingMessage = (username) => `${username}` }) {

  return (
    <div className={styles.profileData}>
      <div className={styles.profilePicDiv}>
        <img src={user.profilePic === "" ? defaultProfilePic : `https://${HOST_URL}/${user.profilePic}`} onError={e => e.target.src = defaultProfilePic} alt="Profile pic"/>
        {onImageEdit && <button onClick={onImageEdit}>Change</button>}
      </div>
      <div>
        <div className={styles.usernameBar}>
          <h1>{greetingMessage(user.username)}</h1>
          <div>{children}</div>
        </div>
        <p className={styles.aka}>Also known as {user.firstName} {user.lastName}</p>
        <p>Email: {user.email}</p>
        <p>Rating: {user.rating} {user.ratingTitle && `(${user.ratingTitle})`}</p>
      </div>
    </div>
  );
}
