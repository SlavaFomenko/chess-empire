import React, { useEffect, useState } from "react";
import styles from '../styles/users-page.module.scss';
import { UserItem } from "../../../../../entities/user-item";
import { getAllUsers } from "../../../../../shared/user";

export function UsersPage() {
  const [users, setUsers] = useState(null);
  const [pages, setPages] = useState(null);
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  useEffect(() => {
    async function fetchData() {
      const data = await getAllUsers({ name: search, page: currentPage });
      setUsers(data.users);
      setPages(data.pagesCount);
    }
    fetchData();
  }, [search, currentPage]);

  const handleSearchChange = (e) => {
    setSearch(e.target.value);
    setCurrentPage(1);
  };

  const handlePageChange = (page) => {
    setCurrentPage(page);
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
      <div>
        <table className={styles.usersTable}>
          <thead>
          <tr>
            <td>ID</td>
            <td>Username</td>
            <td>First name</td>
            <td>Last name</td>
            <td>Rating</td>
            <td>Role</td>
            <td>Email</td>
          </tr>
          </thead>
          <tbody>
          {users && users.map(user => (
            <tr key={user.id}>
              <UserItem
                  user={user}
                />
            </tr>
          ))}
          </tbody>
        </table>

      </div>
      <div className={styles.pagination}>
        {Array.from({ length: pages }, (_, i) => i + 1).map(page => (
          <button
            key={page}
            onClick={() => handlePageChange(page)}
            disabled={page === currentPage}
          >
            {page}
          </button>
        ))}
      </div>
    </div>
  );
}
