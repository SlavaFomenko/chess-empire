import React, { useEffect, useState } from "react";
import { getAllGames } from "../../../../../shared/game";
import { GamesList } from "../../../../../entities/profile";
import styles from "../styles/games-page.module.scss";
import { Pagination } from "../../../../../entities/pagination";

export function GamesPage () {
  const [games, setGames] = useState([]);
  const [pagination, setPagination] = useState({ currentPage: 1, pagesCount: 0 });
  const [search, setSearch] = useState("");
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");

  useEffect(() => {
    fetchData();
  }, [pagination.currentPage, search, startDate, endDate]);

  const fetchData = async () => {
    const params = { page: pagination.currentPage, search };
    if (startDate) {
      params.startDate = new Date(startDate).getTime() / 1000;
    }
    if (endDate) {
      params.endDate =  new Date(endDate).getTime() / 1000;
    }
    const data = await getAllGames(params);
    setGames(data.games);
    setPagination({ ...pagination, pagesCount: data.pagesCount });
  };

  const handlePageChange = (page) => {
    setPagination({ ...pagination, currentPage: page });
  };

  const handleSearchChange = (e) => {
    setSearch(e.target.value);
    setPagination({ ...pagination, currentPage: 1 });
  };

  const handleStartDateChange = (e) => {
    setStartDate(e.target.value);
    setPagination({ ...pagination, currentPage: 1 });
  };

  const handleEndDateChange = (e) => {
    setEndDate(e.target.value);
    setPagination({ ...pagination, currentPage: 1 });
  };

  const getDateTime = (timestamp = null) => {
    const now = timestamp ? new Date(timestamp) : new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  };

  useEffect(() => {
    const currentDateTime = getDateTime();
    setStartDate(getDateTime(1704060000000));
    setEndDate(currentDateTime);
  }, []);

  return (
    <div>
      <div className={styles.searchLine}>
        <input
          type="text"
          value={search}
          onChange={handleSearchChange}
          placeholder="Search by username"
        />
        <div className={styles.dateRange}>
          From
          <input
            type="datetime-local"
            onChange={handleStartDateChange}
            value={startDate}
            max={endDate}
          />
        </div>
        <div className={styles.dateRange}>
          To
          <input
            type="datetime-local"
            value={endDate}
            onChange={handleEndDateChange}
            min={startDate}
            max={getDateTime()}
          />
        </div>
      </div>
      <GamesList games={games} />
      {pagination.pagesCount !== 1 &&
        <Pagination currentPage={pagination.currentPage} pagesCount={pagination.pagesCount} onClick={(page) => handlePageChange(page)} />}
    </div>
  );
}