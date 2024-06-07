import React from "react";
import deleteIcon from "../../../shared/images/icons/delete-icon.png";

export function UserItem({user}) {
  const btnHandler = ()=>{
    console.log('Click!' + user.id);
  }

  return (
    <>
      <td>{user.id}</td>
      <td>{user.username}</td>
      <td>{user.firstName}</td>
      <td>{user.lastName}</td>
      <td>{user.rating}</td>
      <td>{user.role}</td>
      <td>{user.email}</td>
      <td onClick={btnHandler}>
        <img  src={deleteIcon} alt="Delete" />
      </td>
    </>
  );
}
