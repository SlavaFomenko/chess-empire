import React, { useEffect, useState } from "react";
import { getAllGames } from "../../../../../shared/game";
import { GamesList } from "../../../../../entities/profile";
import styles from '../styles/games-page.module.scss';

export function GamesPage(props) {
  const [games, setGames] = useState([]);
  const [pages, setPages] = useState(null);
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);


  useEffect(() => {
    fetchData();
  }, [currentPage, search]);

  const fetchData = async () => {
    const data = await getAllGames({ page: currentPage, search });
    setGames(data.games);
    setPages(data.pagesCount);
  };

  const handlePageChange = (page) => {
    setCurrentPage(page);
  };

  const handleSearchChange = (e) => {
    setSearch(e.target.value);
  };

  return (
    <div>
      <div>
        <input
          type="text"
          value={search}
          onChange={handleSearchChange}
          placeholder="Search by username"
        />
      </div>
      <GamesList games={games} />
      <div className={styles.pagination}>
        {Array.from({ length: pages }, (_, i) => i + 1).map((page) => (
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
