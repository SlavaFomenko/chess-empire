import React from "react";
import styles from "../styles/rating-range-row.module.css";
import editIcon from "../../../../shared/images/icons/edit-icon.png";
import deleteIcon from "../../../../shared/images/icons/delete-icon.png";

export function RatingRangeRow ({ range, onEdit, onDelete }) {

  return (
    <>
      <td>{range.title}</td>
      <td>{range.minRating}</td>
      <td className={styles.rangeWin}>{range.win}</td>
      <td className={styles.rangeLoss}>{range.loss}</td>
      <td>
        <img
          className={styles.editButton} src={editIcon} alt="Edit" onClick={onEdit}
        />
      </td>
      <td>
        {range.minRating !== 0 && <img className={styles.deleteButton} src={deleteIcon} alt="Delete" onClick={() => onDelete(range.id)} />}
      </td>
    </>
  );
}
