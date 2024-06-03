import React from "react";
import { useLocation, useNavigate } from "react-router-dom";
import styles from "../styles/side-bar.module.scss";
import { useDispatch, useSelector } from "react-redux";
import { s } from "../../../shared/socket";

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
      <button
        className={location.pathname === "/" ? styles.current : ""}
        onClick={() => redirect("/")}
      >
        Home
      </button>
      <button
        className={location.pathname === "/game" ? styles.current : ""}
        onClick={() => redirect("/game")}
      >
        Game
      </button>
      {!user.user?.token ? <>
          <button
            className={location.pathname === "/login" ? styles.current : ""}
            onClick={() => redirect("/login")}
          >
            Sign in
          </button>
          <button
            className={location.pathname === "/register" ? styles.current : ""}
            onClick={() => redirect("/register")}
          >
            Sign up
          </button>
        </> :
        <>
          <button
            className={location.pathname === "/profile" ? styles.current : ""}
            onClick={() => redirect("/profile")}
          >
            Profile
          </button>
          <button
            onClick={logout}
          >
            Log out
          </button>
        </>
      }
    </div>
  );
}
