import React, { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import { LayoutPage } from "../../../layouts/page-layout";
import axios from "axios";
import { GET_USER_BY_ID_URL } from "../../../shared/config";
import { useNavigate } from "react-router-dom";

export function ProfilePage() {
  const [user, setUser] = useState(null);
  const navigate = useNavigate()
  const userStore = useSelector(state => state.user);

  console.log(userStore);



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
      <div>
        <h1>Profile</h1>
        {user ? (
          <div>
            <p>id: {user.id}</p>
            <p>user name: {user.username}</p>
            <p>Email: {user.email}</p>
            <p>first name: {user.firstName}</p>
            <p>last name: {user.lastName}</p>
            <p>rating: {user.rating}</p>
          </div>
        ) : (
          <p>Loading...</p>
        )}
      </div>
    </LayoutPage>
  );
}
