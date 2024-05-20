import React, { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import { LayoutPage } from "../../../layouts/page-layout";
import axios from "axios";
import { GET_USER_BY_ID_URL } from "../../../shared/config";
import { useNavigate } from "react-router-dom";
import styles from "../styles/profile.module.scss"

export function ProfilePage() {
  const [user, setUser] = useState(null);
  const navigate = useNavigate()
  const userStore = useSelector(state => state.user);

  useEffect(() => {
    const fetchUser = async () => {
      try {
        const response = await axios.get(GET_USER_BY_ID_URL, {
          params: { id: userStore.user.id }
        });
        setUser(response.data.user);
      } catch (error) {
        console.error("Error fetching user data:", error);
      }
    };
    if (userStore.user) {
      fetchUser();
    } else {
      navigate("/");
    }
  }, [userStore]);

  return (
    <LayoutPage>
      <div className={styles.profilePage}>
        {user ? (
          <div>
            <h1>Hi, {user?.username}!</h1>
            <p className={styles.aka}>Also known as {user.firstName} {user.lastName}</p>
            <p>Email: {user.email}</p>
            <p>Rating: {user.rating}</p>
          </div>
        ) : (
          <p>Loading...</p>
        )}
      </div>
    </LayoutPage>
  );
}
