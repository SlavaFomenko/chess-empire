import React from "react";
import deleteIcon from "../../../../shared/images/icons/delete-icon.png";
import styles from "../styles/user-item.module.scss"

export function UserItem({user}) {
  const roleTitles = {
    ROLE_USER: "User",
    ROLE_ADMIN: "Admin",
    ROLE_BANNED: "Banned",
  }

  return (
    <tr className={styles.userItem} key={user.id}>
      <td>{user.id}</td>
      <td>{user.username}</td>
      <td>{user.firstName}</td>
      <td>{user.lastName}</td>
      <td>{user.rating}</td>
      <td>{roleTitles[user.role]}</td>
      <td>{user.email}</td>
      <td>
        {user.role !== "ROLE_ADMIN" && <img className={styles.deleteButton} src={deleteIcon} alt="Delete" />}
      </td>
    </tr>
  );
}
