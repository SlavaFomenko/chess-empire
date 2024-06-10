import React from "react";
import styles from "../styles/user-card.module.scss";
import { useNavigate } from "react-router-dom";
import defaultProfilePic from "../../../../shared/images/icons/defaultProfilePic.png";
import { HOST_URL } from "../../../../shared/config";

export function UserCard ({ user, children, onClick = () => {}, displayRoles = false }) {
  const roleTitles = {
    ROLE_USER: "User",
    ROLE_ADMIN: "Admin",
    ROLE_BANNED: "Banned"
  };

  return (
    <div className={styles.card} onClick={onClick}>
      <table>
        <tbody>
        <tr className={styles.usernameRow}>
          <td className={styles.profilePicTd} rowSpan="3">
            <img src={user.profilePic === "" ? defaultProfilePic : `https://${HOST_URL}/${user.profilePic}`} onError={e => e.target.src = defaultProfilePic} alt="Profile pic" />
          </td>
          <td className={styles.username}>
            {user.username} ({user.rating})
          </td>
          <td className={styles.role}>
            {displayRoles && roleTitles[user.role]}
          </td>
        </tr>
        <tr>
          <td className={styles.fullName}>{user.firstName} {user.lastName}</td>
          <td className={styles.buttons} rowSpan="2">{children}</td>
        </tr>
        <tr className={styles.lastRow}>
          <td className={styles.email}>{user.email}</td>
        </tr>
        </tbody>
      </table>
    </div>
  );
}
