import React, { useEffect, useState } from "react";
import { getAllGames } from "../../../../../shared/game";
import { GamesList } from "../../../../../entities/profile";
import styles from '../styles/games-page.module.scss';
import { Pagination } from "../../../../../entities/pagination";

export function GamesPage() {
  const [games, setGames] = useState([]);
  const [pagination, setPagination] = useState({ currentPage: 1, pagesCount: 0 });
  const [search, setSearch] = useState("");


  useEffect(() => {
    fetchData();
  }, [pagination.currentPage, search]);

  const fetchData = async () => {
    const data = await getAllGames({ page: pagination.currentPage, search });
    setGames(data.games);
    setPagination({...pagination, pagesCount: data.pagesCount});
  };

  const handlePageChange = (page) => {
    setPagination({...pagination, currentPage: page});
  };

  const handleSearchChange = (e) => {
    setSearch(e.target.value);
    setPagination({...pagination, currentPage: 1});
  };

  return (
    <div>
      <div className={styles.searchLine}>
        <input
          type="text"
          value={search}
          onChange={handleSearchChange}
          placeholder="Search by username"
        />
      </div>
      <GamesList games={games} />
      {pagination.pagesCount !== 1 &&
        <Pagination currentPage={pagination.currentPage} pagesCount={pagination.pagesCount} onClick={(page) => handlePageChange(page)} />}
    </div>
  );
}
