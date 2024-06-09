import React, { useEffect, useState } from "react";
import styles from "../styles/invite-friend-dialog.module.scss";
import { BannerLayout } from "../../../../layouts/banner-layout";
import { UsersList } from "../../users-list/ui/users-list";
import { Pagination } from "../../../pagination";
import { useNavigate } from "react-router-dom";
import { GET_ALL_USERS_URL, POST_RATING_RANGE, SEND_FRIEND_REQUEST } from "../../../../shared/config";
import axios from "axios";
import { showNotification } from "../../../../shared/notification";
import { useDispatch, useSelector } from "react-redux";

export function InviteFriendDialog ({ onClose = ()=>{} }) {
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const userStore = useSelector(state => state.user);
  const [users, setUsers] = useState(null);
  const [search, setSearch] = useState("");
  const [pagination, setPagination] = useState({ currentPage: 1, pagesCount: 0 });
  const [inviteTrigger, setInviteTrigger] = useState(false);

  const fetchUsers = async ({ name, page = 1}) => {
    const params = {page, request: false, friend: false, pending: false};
    if (name && name.trim().length > 0) {
      params.name = name;
    }
    const response = await axios.get(GET_ALL_USERS_URL, {
      params,
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`
      }
    });
    return response?.data || [];
  };

  useEffect(() => {
    async function fetchData () {
      const data = await fetchUsers({ name: search, page: pagination.currentPage });
      setUsers(data.users);
      setPagination({ ...pagination, pagesCount: data.pagesCount });
    }

    fetchData();
  }, [search, pagination.currentPage, inviteTrigger]);

  const handleSearchChange = (e) => {
    setSearch(e.target.value);
    setPagination({ ...pagination, currentPage: 1 });
  };

  const handlePageChange = (page) => {
    setPagination({ ...pagination, currentPage: page });
  };

  const sendInvite = (user) => {
    try {
      axios.post(SEND_FRIEND_REQUEST, {receiverId: user.id}, {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setInviteTrigger(!inviteTrigger);
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error sending friend request"));});
    } catch (error) {
      dispatch(showNotification("Error sending friend request"));
    }
  }

  return (
    <BannerLayout onClick={onClose}>
      <div className={styles.inviteFriendDialog} onClick={e => e.stopPropagation()}>
        <div className={styles.searchLine}>
          <input
            type="text"
            value={search}
            onChange={handleSearchChange}
            placeholder="Search by username"
          />
        </div>
        <UsersList
          onClick={(user) => navigate(`/user/${user.id}`)}
          classNames={styles.usersList}
          users={users} childrenCallback={(user) => <>
          <button onClick={(e) => {e.stopPropagation(); sendInvite(user);}}>
            Invite
          </button>
        </>}
        />
        {users?.length === 0 && <p>No users found</p>}
        {pagination.pagesCount !== 1 &&
          <Pagination currentPage={pagination.currentPage} pagesCount={pagination.pagesCount} onClick={(page) => handlePageChange(page)} />}
      </div>
    </BannerLayout>
  );
}
