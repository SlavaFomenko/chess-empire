import React from "react";
import styles from "../styles/users-list.module.scss";
import { UserCard } from "../../user-card/ui/user-card";

export function UsersList ({ users, classNames, childrenCallback = ()=>{}}) {
  return (
    <div className={`${styles.container} ${classNames}`}>
      {users && users.map((user,index)=><UserCard key={index} user={user}>
        {childrenCallback(user)}
      </UserCard>)}
    </div>
  );
}
