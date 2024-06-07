import React from "react";
import styles from "../styles/pagination.module.scss";
import classNames from "classnames";

export function Pagination ({ pagesCount, currentPage, onClick }) {
  const pages = Array.from({ length: 7 }, (_, i) => currentPage + i - 3).filter(page => page >= 1 && page <= pagesCount);

  return (
    <div className={styles.pagination}>
      {pagesCount > 1 && !pages.includes(1) && <button
        key="FirstPage"
        onClick={() => onClick(1)}
      >
        First
      </button>}
      {pages.map(page => (
        <button
          className={classNames({ [`${styles.active}`]: page === currentPage })}
          key={page}
          onClick={() => onClick(page)}
          disabled={page === currentPage}
        >
          {page}
        </button>
      ))}
      {pagesCount > 1 && !pages.includes(pagesCount) && <button
        key="LastPage"
        onClick={() => onClick(pagesCount)}
      >
        Last
      </button>}
    </div>
  );
}
