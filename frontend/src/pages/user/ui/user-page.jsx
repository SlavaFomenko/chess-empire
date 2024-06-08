import React, { useEffect, useState } from "react";
import axios from "axios";
import styles from "../styles/user-page.module.scss";
import { GET_USER_BY_ID } from "../../../shared/config";
import { useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import { LayoutPage } from "../../../layouts/page-layout";
import { ProfileData } from "../../../entities/profile/profile-data/ui/profile-data";

export function UserPage () {
  const userStore = useSelector(state => state.user);
  const navigate = useNavigate();
  const [error, setError] = useState(null);
  const [user, setUser] = useState(null);

  useEffect(() => {
    setError(null);
    if (!userStore.user?.token) {
      setError("You are not authorized");
      return;
    }

    let userId = window.location.pathname.replace("/user/", "");
    if (`${+userId}` !== `${userId}`) {
      setError("Invalid URL");
      return;
    }
    userId = +userId;
    if(userId === userStore.user.id){
      navigate("/profile");
    }

    axios.get(GET_USER_BY_ID(userId), {
      headers: {
        Authorization: `Bearer ${userStore.user.token}`
      }
    }).then(response => {
      setUser(response.data?.user);
    }).catch(error => setError(error.response.data.message));
  }, [userStore.user]);

  return (
    <LayoutPage>
      <div className={styles.userPage}>
        {error && <h1 className={styles.errorMessage}>{error}</h1>}
        {!error && (user ? (
          <ProfileData user={user}/>
        ) : (
          <p>Loading...</p>
        ))}
      </div>
    </LayoutPage>
  );
}
