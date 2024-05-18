import React from "react";
import { useLocation, useNavigate } from "react-router-dom";
import styles from "../styles/side-bar.module.scss";
import { useSelector } from "react-redux";

export function SideBar () {
  const navigate = useNavigate();
  const location = useLocation();
  const user = useSelector(state => state.user);

  const redirect = (page) => {
    if (location.pathname !== page) {
      navigate(page);
    }
  };

  const logout = () => {
    localStorage.removeItem("token");
    window.location.reload();
  };

  return (
    <div className={styles.side_bar}>
      <ul>
        <li>
          <button onClick={() => redirect("/")}>Home</button>
        </li>
        <li>
          <button onClick={() => redirect("/game")}>Game</button>
        </li>
        {!user.user?.token ? <>
            <li>
              <button onClick={() => redirect("/login")}>Sign in</button>
            </li>
            <li>
              <button onClick={() => redirect("/register")}>Sign up</button>
            </li>
          </> :
          <li>
            <button onClick={logout}>Log out</button>
          </li>
        }
      </ul>
    </div>
  );
}
