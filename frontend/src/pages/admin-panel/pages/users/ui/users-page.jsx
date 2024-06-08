import React, { useEffect, useState } from "react";
import styles from "../styles/users-page.module.scss";
import { DELETE_RATING_RANGE, DELETE_USER, getAllUsers } from "../../../../../shared/user";
import { Pagination } from "../../../../../entities/pagination";
import { UsersList } from "../../../../../entities/profile";
import deleteIcon from "../../../../../shared/images/icons/delete-icon.png";
import { hideNotification, showNotification } from "../../../../../shared/notification";
import { useDispatch, useSelector } from "react-redux";
import axios from "axios";

export function UsersPage () {
  const dispatch = useDispatch();
  const userStore = useSelector(state => state.user);
  const [users, setUsers] = useState(null);
  const [pagination, setPagination] = useState({ currentPage: 1, pagesCount: 0 });
  const [order, setOrder] = useState({ by: "id", desc: false });
  const [search, setSearch] = useState("");
  const [rating, setRating] = useState({ min: null, max: null });
  const [deleteTrigger, setDeleteTrigger] = useState(false);

  useEffect(() => {
    async function fetchData () {
      const data = await getAllUsers({ name: search, page: pagination.currentPage, order, rating });
      setUsers(data.users);
      setPagination({ ...pagination, pagesCount: data.pagesCount });
    }

    fetchData();
  }, [search, pagination.currentPage, order, rating.min, rating.max, deleteTrigger]);

  const handleSearchChange = (e) => {
    setSearch(e.target.value);
    setPagination({ ...pagination, currentPage: 1 });
  };

  const handlePageChange = (page) => {
    setPagination({ ...pagination, currentPage: page });
  };

  const handleRatingChange = (rating) => {
    setRating(rating);
    setPagination({ ...pagination, currentPage: 1 });
  };

  const submitDelete = (user) => {
    dispatch(showNotification(
      <div className={styles.deleteSubmit}>
        Are you sure you want to delete user {user.username}?
        <div>
          <button
            onClick={() => {
              deleteUser(user.id);
              dispatch(hideNotification());
            }
            }
          >Yes
          </button>
          <button
            onClick={() => {
              dispatch(hideNotification());
            }
            }
          >No
          </button>
        </div>
      </div>
    ));
  };

  const deleteUser = (id) => {
    try {
      axios.delete(DELETE_USER(id), {
        headers: {
          Authorization: `Bearer ${userStore.user.token}`
        }
      }).then(response => {
        setDeleteTrigger(!deleteTrigger);
      }).catch(error => {dispatch(showNotification(error.response?.data?.message || "Error deleting user"));});
    } catch (error) {
      dispatch(showNotification("Error deleting user"));
    }
  };

  const handleReset = () => {
    setOrder({ by: "id", desc: false });
    setRating({ min: null, max: null });
  }

  return (
    <div className={styles.wrapper}>
      <div className={styles.searchLine}>
        <input
          type="text"
          value={search}
          onChange={handleSearchChange}
          placeholder="Search by username"
        />
      </div>
      <div className={styles.filtersDiv}>
        <button
          onClick={handleReset}
        >
          Reset
        </button>
        <div>
          <span>Rating: </span>
          <input
            type="number"
            value={rating.min ?? ""}
            onChange={(e) => handleRatingChange({ ...rating, min: +e.target.value ?? null })}
            placeholder="Min"
          />
          <span>to</span>
          <input
            type="number"
            value={rating.max ?? ""}
            onChange={(e) => handleRatingChange({ ...rating, max: +e.target.value ?? null })}
            placeholder="Max"
          />
        </div>
        <div>
          <span>Sort by:</span>
          <select
            value={order.by} onChange={e => {
            setOrder({ by: e.target.value, desc: false });
          }}
          >
            <option value="id">ID</option>
            <option value="username">Username</option>
            <option value="first_name">First name</option>
            <option value="last_name">Last name</option>
            <option value="rating">Rating</option>
            <option value="role">Role</option>
            <option value="email">Email</option>
          </select>
          <select
            value={order.desc ? "desc" : "asc"} onChange={e => {
            console.log(e);
            setOrder({
              by: order.by,
              desc: e.target.value === "desc"
            });
          }}
          >
            <option value="asc">ASC</option>
            <option value="desc">DESC</option>
          </select>
        </div>
      </div>
      <UsersList
        classNames={styles.usersList}
        users={users} childrenCallback={(user) => user.role !== "ROLE_ADMIN" && <>
        <button onClick={() => submitDelete(user)}>
          <img src={deleteIcon} alt="Delete User" />
        </button>
      </>}
      />
      {pagination.pagesCount !== 1 &&
        <Pagination currentPage={pagination.currentPage} pagesCount={pagination.pagesCount} onClick={(page) => handlePageChange(page)} />}

    </div>
  );
}
