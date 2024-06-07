import React, { useEffect, useState } from "react";
import styles from "../styles/users-page.module.scss";
import { UserItem } from "../../../../../entities/admin-panel/user-item";
import { getAllUsers } from "../../../../../shared/user";
import { Pagination } from "../../../../../entities/pagination";
import ascOrderIcon from "../../../../../shared/images/icons/asc-order-icon.png";
import descOrderIcon from "../../../../../shared/images/icons/desc-order-icon.png";

export function UsersPage () {
  const [users, setUsers] = useState(null);
  const [pagination, setPagination] = useState({ currentPage: 1, pagesCount: 0 });
  const [order, setOrder] = useState({ by: null, desc: false });
  const [search, setSearch] = useState("");
  const [rating, setRating] = useState({ min: null, max: null });
  useEffect(() => {
    async function fetchData () {
      const data = await getAllUsers({ name: search, page: pagination.currentPage, order, rating });
      setUsers(data.users);
      setPagination({ ...pagination, pagesCount: data.pagesCount });
    }

    fetchData();
  }, [search, pagination.currentPage, order, rating.min, rating.max]);

  const handleSearchChange = (e) => {
    setSearch(e.target.value);
    setPagination({ ...pagination, currentPage: 1 });
  };

  const handlePageChange = (page) => {
    setPagination({ ...pagination, currentPage: page });
  };

  const handleOrderChange = (by) => {
    setOrder({ by: by, desc: by === order.by ? !order.desc : false });
  };

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
        <button>Reset</button>
        <span>Rating: </span>
        <input
          type="number"
          value={rating.min}
          onChange={(e)=>setRating({...rating, min: +e.target.value ?? null})}
          placeholder="Min"
        />
        <span>to</span>
        <input
          type="number"
          value={rating.max}
          onChange={(e)=>setRating({...rating, max: +e.target.value ?? null})}
          placeholder="Max"
        />
      </div>
      <div>
        <table className={styles.usersTable}>
          <tr className={styles.headersRow}>
            <td onClick={() => handleOrderChange("id")}>
              {order.by === "id" && <img src={order.desc ? descOrderIcon : ascOrderIcon} alt="Order" />}
              ID
            </td>
            <td onClick={() => handleOrderChange("username")}>
              {order.by === "username" && <img src={order.desc ? descOrderIcon : ascOrderIcon} alt="Order" />}
              Username
            </td>
            <td onClick={() => handleOrderChange("first_name")}>
              {order.by === "first_name" && <img src={order.desc ? descOrderIcon : ascOrderIcon} alt="Order" />}
              First name
            </td>
            <td onClick={() => handleOrderChange("last_name")}>
              {order.by === "last_name" && <img src={order.desc ? descOrderIcon : ascOrderIcon} alt="Order" />}
              Last name
            </td>
            <td onClick={() => handleOrderChange("rating")}>
              {order.by === "rating" && <img src={order.desc ? descOrderIcon : ascOrderIcon} alt="Order" />}
              Rating
            </td>
            <td onClick={() => handleOrderChange("role")}>
              {order.by === "role" && <img src={order.desc ? descOrderIcon : ascOrderIcon} alt="Order" />}
              Role
            </td>
            <td onClick={() => handleOrderChange("email")}>
              {order.by === "email" && <img src={order.desc ? descOrderIcon : ascOrderIcon} alt="Order" />}
              Email
            </td>
          </tr>
          <tbody>
          {users && users.map(user => (
            <UserItem
              user={user}
            />
          ))}
          </tbody>
        </table>

      </div>
      {pagination.pagesCount !==1 && <Pagination currentPage={pagination.currentPage} pagesCount={pagination.pagesCount} onClick={(page) => handlePageChange(page)} />}

    </div>
  );
}
