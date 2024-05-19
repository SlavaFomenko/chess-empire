import React from "react";
import { useLocation, useNavigate } from "react-router-dom";
import styles from "../styles/side-bar.module.scss";
import { useDispatch, useSelector } from "react-redux";
import { s } from "../../../shared/socket/actions/socket";

export function SideBar () {
  const navigate = useNavigate();
  const location = useLocation();
  const dispatch = useDispatch();
  const user = useSelector(state => state.user);

  const redirect = (page) => {
    if (location.pathname !== page) {
      navigate(page);
    }
  };

  const logout = () => {
    localStorage.removeItem("token");
    dispatch(s.disconnect());
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
