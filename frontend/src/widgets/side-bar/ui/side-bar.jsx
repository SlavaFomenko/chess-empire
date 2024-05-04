import React from "react";
import { useLocation, useNavigate } from "react-router-dom";
import styles from "../styles/side-bar.module.scss";

export function SideBar () {
  const navigate = useNavigate();
  const location = useLocation();

  const redirect = (page) => {
    if(location.pathname !== page){
      navigate(page);
    }
  };

  return (
    <div className={styles.side_bar}>
      <ul>
        <li>
          <button onClick={() => redirect("/")}>home page</button>
        </li>
        <li>
          <button onClick={() => redirect("/game")}>game page</button>
        </li>
        <li>
          <button onClick={() => redirect("/authorization")}>authorization page</button>
        </li>
      </ul>
    </div>
  );
}
